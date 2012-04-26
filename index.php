<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
ob_start(); //This is used to prevent errors causing g-zip compression problems before g-zip is started.
require_once('dbSettings.php');
require_once('libraries/common.php');
require_once('libraries/defines.php');

final class dynamicPDO extends PDO {
	private $tablePrefix;
	public $sessionPrefix;
	private $sqlType;
	private $queries;
	private $qSearch=array('!prefix!','!table!');
	private $forbiddenStructureChars=array(',',';');
	public static function exceptionHandler($exception) {
		die('Uncaught Exception:'.$exception->getMessage());
	}
	public function __construct() {
		/*
			The exceptionHandler prevents connection details from being
			revealed in the case of failure
		*/
		$dbSettings=dbSettings();
		
		set_exception_handler(array(__CLASS__,'exceptionHandler'));
		parent::__construct(
			$dbSettings['dsn'],
			$dbSettings['username'],
			$dbSettings['password']
		);
		restore_exception_handler();

		$this->sqlType=strstr($dbSettings['dsn'],':',true);
		$this->tablePrefix=$dbSettings['tablePrefix'];
		/* should implement a better session prefix method */
		$this->sessionPrefix=$this->tablePrefix;
		$this->loadModuleQueries('common',true);
	}
	public function loadCommonQueryDefines($dieOnError=false) {
		$target='queries/'.$this->sqlType.'/common.query.defines.php';
		if (file_exists($target)) {
			require_once($target);
			return true;
		} else if ($dieOnError) {
			die('Fatal Error - Common Query Defines Library File not found!<br>'.$target);
		} else return false;
	}
	public function loadModuleQueries($moduleName,$dieOnError=false) {
		$target='queries/'.$this->sqlType.'/'.$moduleName.'.queries.php';
		if (file_exists($target)) {
			require_once($target);
			$loader=$moduleName.'_addQueries';
			$this->queries[$moduleName]=$loader();
			return true;
		} else if ($dieOnError) {
			die('Fatal Error - '.$moduleName.' Queries Library File not found!<br>'.$target);
		} else return false;
	}
	private function prepQuery($queryName,$module,$tableName) {
		if(!isset($this->queries[$module])){
			$this->loadModuleQueries($module);
		}
		if (isset($this->queries[$module][$queryName])) {
			return str_replace(
				$this->qSearch,
				array(
					$this->tablePrefix,
					$tableName
				),
				$this->queries[$module][$queryName]
			);
		} else return false;
	}
	public function query($queryName,$module='common',$tableName='') {
		if ($query=$this->prepQuery($queryName,$module,$tableName)) {
			return parent::query($query);
		} else {
			return false;
		}
	}
	public function exec($queryName,$module='common',$tableName='') {
		if ($query=$this->prepQuery($queryName,$module,$tableName)) {
			return parent::exec($query);
		} else return false;
	}
	public function prepare($queryName,$module='common',$tableName='') {
		if ($query=$this->prepQuery($queryName,$module,$tableName)) {
			return parent::prepare($query);
		} else return false;
	}
	public function tableExists($tableName) {
		try {
			$statement=$this->query('tableExists','common',$tableName);
	  	$result=$statement->fetchAll();
  		return (count($result)>0);
		} catch (PDOException $e) {
  		return false;
  	}
	}
	public function countRows($tableName) {
		$result=$this->query('countRows','common',$tableName);
		return $result->fetchColumn();
	}
	public function lastInsertId()
	{
		return parent::lastInsertId();
	}
	/*
		structure is an array of field names and definitions
	*/
	public function createTable($tableName,$structure,$verbose=false) {
	
		if ($this->tableExists($tableName)) {
			if($verbose) echo '<p>Table ',$tableName,' already exists</p>';
			return false;
		} else {
		
			$query='CREATE TABLE `'.$this->tablePrefix.$tableName.'` (';
			$qList=array();
			foreach ($structure as $field => $struct) {
				if(is_int($field)){ //no field name, so it's a command - e.g. INDEX(`blogId`) etc
					$qList[].="\n\t" . str_replace(';','',$struct);
				}else{
					$qList[].="\n\t`".str_replace(';','',$field).'` '.str_replace(';','',$struct);
				}
				
			}
			
			$query.=implode(', ',$qList)."\n) ENGINE=MyISAM";
			if ($verbose) echo '<pre>',$query,'</pre>';
			
			try {
				parent::exec($query);
			} catch(PDOException $e) {
				if($verbose) {
					echo '
						<p class="error">Failed to create '.$name.' table!</p>
						<pre>'.$e->getMessage().'</pre>';
				}
				return false;
			}
			
			return true;
			
		}
	}
	public function dropTable($tableName,$verbose=false) {
		if($verbose) echo '<p>Dropping ',$tableName,' table</p>';
		
		if($this->tableExists($tableName)) {
			$this->exec('dropTable','installer',$tableName);
		} else {
			if($verbose) echo '<p>Table ',$tableName,' does not exist</p>';
		}
	
	}
}
final class sitesense {
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
		$loginResult=false,
		$plugins = array(),
		$cdn,
		$smallStaticLinkRoot,
		$largeStaticLinkRoot,
		$flashLinkRoot,
		$cdnLinks = array(),
		$banned = false,
		$jsEditor;
		
		private $db;
		public function __construct() {
	
		/* -------- */
		$url=str_replace(array('\\','%5C'),'/',$_SERVER['REQUEST_URI']); 
		if (strpos($url,'../')) killHacker('Uptree link in URI'); 
		$this->linkHome=str_ireplace('index.php','',$_SERVER['PHP_SELF']); 
		if (strpos($url,'?')>0) { 
			 /* if using get, action based on query string */ 
			 $queryString=$_SERVER['QUERY_STRING']; 
		} else { 
			 $queryString=substr($url,strlen($this->linkHome)-1); 
			 /* be sure to ===0 since false trips ==0 */ 
			 if (strpos($queryString,'index.php')===0) $queryString=substr($queryString,9); 
		} 
		$queryString=trim($queryString,'/'); 
		$this->action=empty($queryString) ? array('default') : explode('/',$queryString);
		/* -------- */
		
		$this->db=new dynamicPDO();
		if ($this->action[0]=='install') { 
			$data=$this->db;
			require_once('admin/install.php'); 
			die; /* technically install.php should die at end, but to be sure... */ 
		}
		/**
		 * Check To See If the IP Is Banned
		**/
		$clientIp = $_SERVER['REMOTE_ADDR'];
		$statement = $this->db->prepare('checkIpBan','users');
		$statement->execute(array(
			':ip' => $clientIp,
		));		
		if($banItem = $statement->fetch())
		{
			// Check Expiration Time
			if(time() > $banItem['expiration'])
			{
				// You served your time, let's remove your ban.
				$statement = $this->db->prepare('removeBan','users');
				$statement->execute(array(
					':id' => $banItem['id']
				));
				// Was There A User Associated With This?
				if($banItem['userId'] !== NULL)
				{
					$statement = $this->db->prepare('updateUserLevel','users');
					$statement->execute(array(
						':userId' => $banItem['userId'],
						':userLevel' => $banItem['userLevel']
					));
				}
			} else {
				// You're still banned
				$this->currentPage = 'banned';
				$this->banned = true;
			}
		} else {
		}
		
		$statement=$this->db->query('getSettings');
		while ($row=$statement->fetch()) {
			if ($row['category']=='cms') {
				$this->settings[$row['name']]=$row['value'];
			} else {
				$this->settings[$row['category']][$row['name']]=$row['value'];
				$this->settings[$row['category']][$row['name']]=$row['value'];
			}
		}
		// Append Attributions //
		$this->settings['parsedFooterContent'] .= ($this->settings['removeAttribution'] == '0') ? '|attribution|' : '';
		// Do We Need To Load A CDN Plugin??? 
		if($this->settings['useCDN'] == '1')
		{
			common_loadPlugin($this,$this->settings['cdnPlugin']);
			$this->cdn =& $this->plugins[$this->settings['cdnPlugin']];
		}
		// Load The WYISWYG Editor Plugin //
		common_loadPlugin($this,$this->settings['jsEditor']);
		$this->jsEditor =& $this->plugins[$this->settings['jsEditor']];

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
		$this->domainName = $this->hostName = 'http://'.$_SERVER["HTTP_HOST"];
		$this->siteRoot=$_SERVER['PHP_SELF'];
		$this->themeDir='themes/'.$this->settings['theme'].'/';
		
		$this->linkRoot=$this->linkHome;
		// Set Up Other CDN Variables Using Link Root //
		$this->smallStaticLinkRoot=(isset($this->settings['cdnSmall']{2})) ? $this->settings['cdnSmall'] : $this->linkRoot;
		$this->largeStaticLinkRoot=(isset($this->settings['cdnLarge']{2})) ? $this->settings['cdnLarge'] : $this->linkRoot;
		$this->flashLinkRoot=(isset($this->settings['cdnFlash']{2})) ? $this->settings['cdnFlash'] : $this->linkRoot;
		if ($this->linkHome!='/') $url=str_replace($this->linkHome,'',$url);
        if (
           ($url=='') ||
           ($url=='index.php') ||
           ($url=='index.html') ||
           ($url=='index.php?')
        ) {
            if(isset($this->settings['homepage']) && $this->action[0]=='default') {
			    $targetInclude='modules/'.$this->settings['homepage'].'.module.php';
			    if(file_exists($targetInclude)) {
				    $this->action[0]=$this->settings['homepage'];
			    } else {
				    $this->action[0]='pages';
				    $this->action[1]=$this->settings['homepage'];
			    }
            } else {
                $this->action[0] = 'default';
            }
        }
		$this->currentPage = ($this->banned) ? 'banned' : $this->action[0];
		$sideBars = array();
		//Does this module exist, and is it enabled?
		if($this->currentPage != 'admin' && !$this->banned){ //The admin NEEDS to be able to access the admin panel - nothing else.
			$moduleQuery = $this->db->prepare('getModuleByShortName', 'modules');
			$moduleQuery->execute(array(':shortName' => $this->currentPage));
			$this->module = $moduleQuery->fetch();
			if($this->module === false || $this->module['enabled'] == 0){
				if($this->module !== false){ //it could be there, just disabled
					$this->currentPage = 'pageNotFound';
				}else if(file_exists('modules/' . $this->module['name'] . '.module.php')){ //exists in the file system, but not in the db.
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
				}else{ //could it be a form, blog or a page?
					$formStatement = $this->db->prepare('getTopLevelFormByShortName', 'form'); //Form
					$formStatement->execute(
						array(
							':shortName' => $this->currentPage,
						)
					);
					if($formStatement->fetch() !== false){
						$this->currentPage = 'forms';
						array_unshift($this->action, 'page');
						$moduleQuery->execute(array(':shortName' => $this->currentPage));
						$this->module = $moduleQuery->fetch();
					}else{ //Blog?
						$blogStatement = $this->db->prepare('getTopLevelBlogByName', 'blogs');
						$blogStatement->execute(
							array(
								':shortName' => $this->currentPage,
							)
						);
						if($blogStatement->fetch() !== false){
							$this->currentPage = 'blogs';
							array_unshift($this->action, 'blogs');
							$moduleQuery->execute(array(':shortName' => $this->currentPage));
							$this->module = $moduleQuery->fetch();
						}else{ //page?
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
		$statement=$this->db->query('getEnabledMainMenuOrderLeft');
		$this->menuList['left']=$statement->fetchAll();
		$statement=$this->db->query('getEnabledMainMenuOrderRight');
		$this->menuList['right']=$statement->fetchAll();
		$getSideBar = $this->db->prepare('getSidebarById');
		$delete=$this->db->prepare('deleteFromSidebarsById');
		foreach($sideBars as $sideBar)
		{
			$this->sideBarList[$sideBar['side']][]=$sideBar;
		}
		/* login session */
		$this->user['userLevel']=0;
		$userCookieName=$this->db->sessionPrefix.'SESSID';
		if (!$this->banned &&
			($this->currentPage=='logout') &&
			(!empty($_COOKIE[$userCookieName]))
		) {
			setCookie($userCookieName,'',0,$this->linkHome);
			$statement=$this->db->prepare('logoutSession');
			$statement->execute(array(
				':sessionID' => $_COOKIE[$userCookieName]
			));
		} else if (!$this->banned && !empty($_COOKIE[$userCookieName])) {
			$userCookieValue=$_COOKIE[$userCookieName];
			/* purge expired sessions */
			$this->db->query('purgeExpiredSessions');
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
						// Check Banned
						if($user['userLevel'] <= USERLEVEL_BANNED)
						{
							// Get The Expiration Time of Your ban
							$statement = $this->db->prepare('getBanByUserId','users');
							$statement->execute(array(
								':userId' => $session['userId']
							));
							if($this->output['banItem'] = $banItem = $statement->fetch())
							{
								if(time() < strtotime($banItem['expiration']))
								{
									$this->banned = true;
									$this->loginResult = false;
									$this->currentPage = 'banned';
								}
							}
						} else {
							$this->user=$user;
							$this->user['sessions']=$session;
							/* push expiration ahead! */
							$expires=time()+$this->settings['userSessionTimeOut'];
							$session['expires']=strtotime($session['expires']);
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
							$expires=gmdate("Y-m-d H:i:s",$expires);
							$statement=$this->db->prepare('updateSessionExpiration');
							$statement->execute(array(
								':expires' => $expires,
								':sessionId' => $session['sessionId']
							)) or die('Session Database failed updating expiration');
							$statement=$this->db->prepare('updateLastAccess');
							$statement->execute(array(
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
		}
		/*
			if it drops through to here, they're not logged in...
			are they trying to?
		*/
		if (!$this->banned && isset($_POST['login']) && $_POST['login']==$_SERVER['REMOTE_ADDR']) {
			$statement=$this->db->prepare('checkPassword');
			$statement->execute(array(
				':name' => $_POST['username'],
				':passphrase' => hash('sha256',$_POST['password'])
			));
			if ($user=$statement->fetch(PDO::FETCH_ASSOC)) {
				$this->user=$user;
				if($this->user['userLevel'] <= USERLEVEL_BANNED)
				{
					// Get The Expiration Time of Your ban
					$statement = $this->db->prepare('getBanByUserId','users');
					$statement->execute(array(
						':userId' => $session['userId']
					));
					if($this->output['banItem'] = $banItem = $statement->fetch())
					{
						if(time() < $banItem['expiration'])
						{
							$this->banned = true;
							$this->loginResult = false;
							$this->currentPage = 'banned';
						}
					}
				} else {
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
					$expires=gmdate("Y-m-d H:i:s",$expires);
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
		if ($this->currentPage=='admin' && !$this->banned) {
			common_include('admin/themes/default/admin.template.php');
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
			
			if($this->currentPage == 'pageNotFound' || $this->banned){
				common_include('modules/pages.module.php');
			}else if (file_exists($targetInclude = 'modules/'.$this->module['name'].'.module.php')) {
				common_include($targetInclude);
			} else {
				common_include('modules/pages.module.php');
			}
			if (function_exists('page_getUniqueSettings')) {
				page_getUniqueSettings($this,$this->db);
			}
			$this->loadModuleTemplate('common');
			$this->loadModuleTemplate($this->module['name']);
		}
		$this->loadModuleLanguage('common');
		$this->loadModuleLanguage($this->currentPage);
		// Get the plugins for this module
		
		$statement=$this->db->query('getEnabledPlugins','plugins');
		$plugins=$statement->fetchAll();
		foreach($plugins as $plugin) {
			common_include('plugins/'.$plugin['name'].'/plugin.php');
			$objectName='plugin_'.$plugin['name'];
			$this->plugins[$plugin['name']]=new $objectName;
		}
		// Is this an AJAX request?
		if($this->action[0]=='ajax') {
            ajax_buildContent($this,$this->db);
		} else {
		// Nope, this is a normal page request
			if (function_exists('page_buildContent')) {
				page_buildContent($this,$this->db);
			}
		}
		$this->db=null;
		if ($this->compressionType) {
			common_include('libraries/gzip.php');
			gzip_start();
			$this->compressionStarted=true;
		}
		if(is_array($this->httpHeaders)) 
		{
			foreach ($this->httpHeaders as $header) {
				header($header);
			}
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
		theme_header($this);
		page_content($this);
		if(function_exists('theme_leftSideBar')) {
			theme_leftSideBar($this);
		}
		if (function_exists('theme_rightSideBar')) {
			theme_rightSideBar($this);
		}
		theme_footer($this);
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
// Initialize and run the application
new sitesense();
?>