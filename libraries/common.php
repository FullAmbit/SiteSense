<?php

function common_isValidEmail($address) {
	if (filter_var($address,FILTER_VALIDATE_EMAIL)==FALSE) {
		return false;
	}
	/* explode out local and domain */
	list($local,$domain)=explode('@',$address);

	$localLength=strlen($local);
	$domainLength=strlen($domain);

	return (
		/* check for proper lengths */
		($localLength>0 && $localLength<65) &&
		($domainLength>3 && $domainLength<256) &&
		(
			checkdnsrr($domain,'MX') ||
			checkdnsrr($domain,'A')
		)
	);
}


function common_redirect_local($data, $where){
	common_redirect($data->linkHome . $where);
}
function common_redirect($where){
	ob_end_clean();
	header('location: ' . $where);
	exit;
}

function common_camelBack($inString) {
	return lcfirst(str_replace(
		array(
			' ',"\n","\t","\r",'&nbsp;','_'
		),'',ucwords($inString)
	));
}

function common_randomPassword($min=8,$max=12) {
	$result='';
	if ($max<$min) $max=$min;
	$count=rand($min,$max);
	while ($count>0) {
		switch (rand(1,3)) {
			case 1:
				$result.=chr(rand(48,57));
			break;
			case 2:
				$result.=chr(rand(65,90));
			break;
			case 3:
				$result.=chr(rand(97,122));
			break;
		}
		$count--;
	}
	return $result;
}


function common_hasUrlPrefix($url) {
	$urlArray=Array(
		'http:',
		'https:',
		'ftp:'
	);
	foreach ($urlArray as $urlPrefix) {
		if (stripos($url,$urlPrefix)===0) return true;
	}
	return false;
}


function common_timedRedirect($URL, $seconds = 5){
	echo _common_timedRedirect($URL);
}

function _common_timedRedirect($URL, $seconds = 5){
	return '
		<p>Click <a href="'. $URL . '">here</a> if you are not redirected in ' . $seconds . ' seconds</p>
		<script type="text/javascript">
			window.setTimeout("window.location.href = \'' . $URL . '\';", ' . ($seconds * 1000) . ');
		</script>
	';
}

function common_parseDynamicValues($data, &$textToParse) {
	$codeReplacements=array(
		'|linkRoot|' => $data->linkRoot,
		'|imageDir|' => $data->linkRoot.'images/'
	);
	foreach ($codeReplacements as $key => $value) {
		$textToParse=str_replace($key,$value,$textToParse);
	}
}

function common_include($includeName) {
	require_once($includeName);
}

class dataHandler {

	public
		$settings,$pageSettings,
		$text,$user,
		$siteRoot,$domainName,$linkHome,$linkRoot,
		$action,$currentPage,$module,$request,
		$httpHeaders,
		$metaList,$menuList,$sideBarList,
		$menuSource,
		$admin,
		$compressionType,
		$compressionStarted=false,
		$output=array(),
		$loginResult=false;

	private $db;

	public function __construct($dbSettings) {
		$this->db=db_init($dbSettings);
		$dbSettings=null;

		$statement=$this->db->query('getSettings');
		while ($row=$statement->fetch()) {
			if ($row['category']=='cms') {
				$this->settings[$row['name']]=$row['value'];
			} else {
				$this->settings[$row['category']][$row['name']]=$row['value'];
				$this->settings[$row['category']][$row['name']]=$row['value'];
			}
		}

		/* when registration gets it's own settings panel, remove this! */
		$this->settings['register']['sender']='noreply@'.$_SERVER['SERVER_NAME'];

	  $this->compressionType=false;

		if ($this->settings['compressionEnabled']) {
			if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'x-gzip')!==false) {
				$this->compressionType='x-gzip';
			} else if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false) {
				$this->compressionType='gzip';
			}
		}
		$this->siteRoot=$_SERVER['PHP_SELF'];
		$this->linkHome=str_ireplace('index.php','',$_SERVER['PHP_SELF']);
		$this->themeDir='themes/'.$this->settings['theme'].'/';
		$this->action=array();
		$url=strip_tags($_SERVER['REQUEST_URI']);
		/* prevent reverse-tree hacks */
		$url=str_replace('../','',$url);
		if ($this->linkHome!='/') $url=str_replace($this->linkHome,'',$url);
		$url=trim($url,'/');
		$this->linkRoot=(
			$this->settings['useModRewrite'] ?
			$this->linkHome :
			$_SERVER['PHP_SELF'].'?'
		);
		if (
			($url=='') ||
			($url=='index.php') ||
			($url=='index.html') ||
			($url=='index.php?')
		) {
			if(isset($this->settings['defaultModule'])){
				$this->action[0] = $this->settings['defaultModule'];
			}else{
				$this->action[0] = 'default';
			}
		}	else {
			$actionPos=stripos($url,'?');
			if ($actionPos) $url=substr($url,$actionPos+1);
			$url=str_replace('#','/',$url);
			$action = html_entity_decode($url);
			$this->request = explode('/', $action);
			session_start();
			if(!isset($_SESSION['remapCount'])){
				$_SESSION['remapCount'] = 0;
			}else if($_SESSION['remapCount'] < 2){
				$rewrite = $this->db->prepare('findReplacement', 'urlremap');
				$rewrite->execute(array(':url' => $action));
				if(false !== ($row = $rewrite->fetch())){
					$action = preg_replace('~' . $row['match'] . '~', $row['replace'], $action);
					$_SESSION['remapCount']++;
					if($row['redirect'] == 1){
						$_SESSION['POST'] = $_POST;
						common_redirect_local($this, $action);
					}
				}
			}
			if(isset($_SESSION['POST'])){
				$_POST = array_merge($_POST, $_SESSION['POST']);
				unset($_SESSION['POST']);
			}
			$_SESSION['remapCount'] = 0; //reset remap
			$this->action = explode('/', $action);
		}

		$this->currentPage=$this->action[0];
		
		$sideBars = array();
		//Does this module exist, and is it enabled?
		if($this->currentPage != 'admin'){ //The admin NEEDS to be able to access the admin panel - nothing else.
			$moduleQuery = $this->db->prepare('getModuleByShortName', 'modules');
			$moduleQuery->execute(array(':shortName' => $this->currentPage));
			$this->module = $moduleQuery->fetch();
			if($this->module === false || $this->module['enabled'] == 0){
				if($this->module !== false){ //its there, just disabled
					$this->currentPage = 'pageNotFound';
				}else if(file_exists('modules/' . $this->currentPage . '.module.php')){ //exists in the file system, but not in the db.
					$statement = $this->db->prepare('newModule', 'modules');
					$statement->execute(
						array(
							':name' => $this->currentPage,
							':shortName' => $this->currentPage,
							':enabled' => 0
						)
					);
					//if it was added to the database, this should fetch it:
					$moduleQuery->execute(array(':shortName' => $this->currentPage));
					$this->currentPage = 'pageNotFound'; //Still show a page-not-found because it will be disabled by default.
					$this->module = $moduleQuery->fetch();
				}else{ //could it be a page?
					$statement = $this->db->prepare('getPageByShortNameAndParent', 'page');
					$statement->execute(
						array(
							':shortName' => $this->currentPage,
							':parent' => 0
						)
					);
					if($statement->fetch() !== false){
						$this->currentPage = 'page';
						array_unshift($this->action, 'page');
						$moduleQuery->execute(array(':shortName' => $this->currentPage));
						$this->module = $moduleQuery->fetch();
					}
				}
			}
			if($this->currentPage != 'pageNotFound'){
				$sideBarQuery = $this->db->prepare('getEnabledSideBarsByModule', 'modules');
				$sideBarQuery->execute(
					array(
						':module' => $this->module['id']
					)
				);
				$sideBars = $sideBarQuery->fetchAll();
			}
		}
		
		
		$this->action = array_merge($this->action,array_fill(0,10,false));

		$this->httpHeaders=array(
			'Content-Type: text/html; charset='.$this->settings['characterEncoding']
		);

		$this->metaList=array(
			array(
				'http-equiv' => 'Content-Type',
				'content' => 'text/html; charset='.$this->settings['characterEncoding']
			),
			array(
				'http-equiv' => 'Content-Language',
				'content' => $this->settings['language']
			)
		);

		$statement=$this->db->query('getMainMenuOrderLeft');
		$this->menuList['left']=$statement->fetchAll();

		$statement=$this->db->query('getMainMenuOrderRight');
		$this->menuList['right']=$statement->fetchAll();
		
		$getSideBar = $this->db->prepare('getSidebarById');
		$delete=$this->db->prepare('deleteFromSidebarsById');
		foreach($sideBars as $sideBar){
			$getSideBar->execute(array(':id' => $sideBar['id']));
			$item = $getSideBar->fetch();
			if ($item['fromFile']) {
				if (file_exists('sideBars/'.$item['name'].'.sideBar.php')) {
					$this->sideBarList[]=$item;
				} else {
					$delete->execute(array(
						':id' => $item['id']
					));
				}
			} else {
				$this->sideBarList[]=$item;
			}
		}

		/* login session */

		$this->user['userLevel']=0;

		$userCookieName=$this->db->sessionPrefix.'SESSID';

		if (
			($this->currentPage=='logout') &&
			(!empty($_COOKIE[$userCookieName]))
		) {

			setCookie($userCookieName,'',0,$this->linkHome);

			$statement=$this->db->prepare('logoutSession');
			$statement->execute(array(
				':sessionID' => $_COOKIE[$userCookieName]
			));
		} else if (!empty($_COOKIE[$userCookieName])) {
			$userCookieValue=$_COOKIE[$userCookieName];

			/* purge expired sessions */
			$statement=$this->db->prepare('purgeExpiredSessions');
			$statement->execute(array(
				':currentTime' => time()
			));

			/* pull session record if still present after purge */
			$statement=$this->db->prepare('getSessionById');
			$statement->execute(array(
				':sessionId' => $userCookieValue
			));

			if ($session=$statement->fetch(PDO::FETCH_ASSOC)) {
				if (
					($session['ipAddress']==$_SERVER['REMOTE_ADDR']) &&
					($session['userAgent']==$_SERVER['HTTP_USER_AGENT'])
				) {
					/* pull user info if session found */
					$statement=$this->db->prepare('pullUserInfoById');
					$statement->execute(array(
						':userId' => $session['userId']
					));

					if ($user=$statement->fetch()) {

						$this->user=$user;
						$this->user['sessions']=$session;
			 			/* push expiration ahead! */
			 			$expires=time()+$this->settings['userSessionTimeOut'];
			 			if ($expires<$session['expires']) {
				 			/*
				 				if the current experiation is ahead of our calculated one,
				 				they must have hit 'keep me logged in' - so let's tack on
				 				a week.
				 			*/
							$expires+=604800;
			 			}
			 			/* update and sync cookie to server values */
			 			setcookie($userCookieName,$userCookieValue,$expires,$this->linkHome,'','',true);

						$statement=$this->db->prepare('updateSessionExpiration');
						$statement->execute(array(
							':expires' => $expires,
							':sessionId' => $session['sessionId']
						)) or die('Session Database failed updating expiration');

						$statement=$this->db->prepare('updateLastAccess');
						$statement->execute(array(
							':lastAccess' => time(),
							':id' => $user['id']
						)) or die('User Database failed updating LastAccess<pre>'.print_r($statement->errorInfo()).'</pre>');

						//Load Profile Pictures
						$profilePictures = $this->db->prepare('getProfilePictures', 'gallery');
						$profilePictures->execute(array(':user' => $this->user['id']));
						$this->user['profilePictures'] = $profilePictures->fetchAll();
						//Load albums
						$albums = $this->db->prepare('getAlbumsByUser', 'gallery');
						$albums->execute(array(':user' => $this->user['id']));
						$this->user['albums'] = $albums->fetchAll();
						$loginResult=true;
					}
				}
			}
		}

		/*
			if it drops through to here, they're not logged in...
			are they trying to?
		*/

		if (isset($_POST['login']) && $_POST['login']==$_SERVER['REMOTE_ADDR']) {
			$statement=$this->db->prepare('checkPassword');
			$statement->execute(array(
				':name' => $_POST['username'],
				':passphrase' => hash('sha256',$_POST['password'])
			));
			if ($user=$statement->fetch(PDO::FETCH_ASSOC)) {
				$this->user=$user;

				/* purge existing user ID instances in sessions */
				$statement=$this->db->prepare('purgeSessionByUserId');
				$statement->execute(array(
					'userId' => $user['id']
				));

				/* then make a new one */
				$userCookieValue=hash('sha256',
					$user['id'].'|'.time().'|'.common_randomPassword(32,64)
				);

	 			$expires=time()+$this->settings['userSessionTimeOut'];
	 			if (isset($_POST['keepLogged']) && $_POST['keepLogged']=='on') {
					$expires+=+604800; /* 1 week */
	 			}

	 			/* update and sync cookie to server values */
		 		setcookie($userCookieName,$userCookieValue,$expires,$this->linkHome,'','',true);

				$statement=$this->db->prepare('updateUserSession');
				$statement->execute(array(
					':sessionId' => $userCookieValue,
					':userId' => $user['id'],
					':expires' => $expires,
					':ipAddress' => $_SERVER['REMOTE_ADDR'],
					':userAgent' => $_SERVER['HTTP_USER_AGENT']
				));
				$this->loginResult=true;
			}
		}
		$moduleQuery = $this->db->query('getEnabledModules', 'modules');
		$modules = $moduleQuery->fetchAll();
		foreach ($modules as $module) {
			$filename = 'modules/' . $module['name'] . '.startup.php';
			if(file_exists($filename)){;
				common_include($filename);
				$targetFunction=$module['name'].'_startUp';
				if (function_exists($targetFunction)) {
					$targetFunction($this,$this->db);
				}
			}
		}
		foreach($this->menuSource as $startupitem){
			if(isset($startupitem['dynamictext'])){
				foreach($this->menuList as &$menus){
					foreach($menus as &$dbitem){
						if($dbitem['text'] == $startupitem['text']){
							$dbitem['text'] = $startupitem['dynamictext'];
							continue;
						}
					}
				}
			}
		}
		if ($this->currentPage=='admin') {
			common_include('admin/admin.template.php');
			common_include('admin/admin.php');
			$this->loadModuleLanguage('admin');
		} else {
			if (
				($this->settings['hideContentGuests']!='no') &&
				($this->user['userLevel']<USERLEVEL_USER)
			) {
				if ($this->currentPage!='pages') {
					$this->currentPage=$this->settings['hideContentGuests'];
				}
			}
			$targetInclude='modules/'.$this->module['name'].'.module.php';
			if (file_exists($targetInclude)) {
				common_include($targetInclude);
			} else {
				common_include('modules/page.module.php');
			}
			if (function_exists('page_getUniqueSettings')) {
				page_getUniqueSettings($this,$this->db);
			}

			$this->loadModuleTemplate('common');
			$this->loadModuleTemplate($this->module['name']);
		}

		$this->loadModuleLanguage('common');
		$this->loadModuleLanguage($this->currentPage);

		if (function_exists('page_buildContent')) {
			page_buildContent($this,$this->db);
		}

		$this->db=null;

		if ($this->compressionType) {
			common_include('libraries/gzip.php');
			gzip_start();
			$this->compressionStarted=true;
		}
		foreach ($this->httpHeaders as $header) {
			header($header);
		}

		if (!empty($this->pageSettings['httpHeaders'])) {
			foreach ($this->pageSettings['httpHeaders'] as $header) {
				header($header);
			}
		}

		if (!empty($this->pageSettings['cookies'])) {
			foreach ($this->pageSettings['cookies'] as $cookie) {
				setcookie(
					$cookie['name'],
					$cookie['value'],
					$cookie['expires']
				);
			}
		}
	} /* __construct */
	public function activateSidebar($name){
		if(isset($this->sideBarList[$name])){
			$this->sideBarList[$name]['display'] = true;
			return true;
		}else{
			return false;
		}
	}
	public function getActivatedSidebars(){
		return array_filter(
			$this->sideBarList,
			function($item){
				return $item['display'];
			}
		);
	}
	public function loadModuleLanguage($module) {
		$language=(
			isset($this->settings['language']) ?
			$this->settings['language'] :
			'english'
		);
		$target='language/'.$language.'/'.$module.'.language.php';
		if (file_exists($target)) {
			common_include($target);
			$languageFunction=$module.'_languageStrings';
			if (function_exists($languageFunction)) {
				$this->text[$module]=$languageFunction();
				return true;
			}
		}
		return false;
	} /* loadModuleLanguage */

	public function loadModuleTemplate($module) {
		$targetInclude=$this->themeDir.$module.'.template.php';
		$defaultInclude='themes/default/'.$module.'.template.php';
		if (file_exists($targetInclude)) {
			common_include($targetInclude);
		}else if(file_exists($defaultInclude)){
			common_include($defaultInclude);
		}
	}

	public function getUserIdByName($name) {
		$statement=$this->db->prepare('getUserIdByName');
		if ($statement->execute(array(':name' => $name))) {
			return $statement->fetchColumn();
		}
		return false;
	}

	function __destruct() {
		if ($this->compressionStarted) gzip_end($this);
	}

}

?>