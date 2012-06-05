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
ini_set("date.timezone","America/New_York");
ob_start(); //This is used to prevent errors causing g-zip compression problems before g-zip is started.
require_once('dbSettings.php');
require_once('libraries/common.php');

final class dynamicPDO extends PDO {
    public  $sessionPrefix;
    private $tablePrefix;
	private $sqlType;
	private $queries;
	private $qSearch=array('!prefix!','!table!');

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
		$target='libraries/queries/defines.'.$this->sqlType.'.php';
		if (file_exists($target)) {
			require_once($target);
			return true;
		} else if ($dieOnError) {
			die('Fatal Error - Common Query Defines Library File not found!<br>'.$target);
		} else return false;
	}
	public function loadModuleQueries($moduleName,$dieOnError=false) { //not seeing the admin... so not know to go to admin directory
		
        $target='modules/'.$moduleName.'/queries/'.$moduleName.'.'.$this->sqlType.'.php';
        // If StartUp Query File, Fix The Name
		if(strpos($moduleName,'_startup'))
		{
			list($moduleNameOnly) = explode('_',$moduleName);
			$target = 'modules/'.$moduleNameOnly.'/queries/'.$moduleNameOnly.'.'.$this->sqlType.'.startup.php';
		}
		// Check For Admin Query
        $pos=strpos($moduleName,'admin_');
        if(!($pos===false)) {
            $moduleNameOnly=substr($moduleName,6);
            $target='modules/'.$moduleNameOnly.'/admin/queries/'.$moduleNameOnly.'.admin.'.$this->sqlType.'.php';
        }
        if($moduleName=='admin' || $moduleName=='common' || $moduleName=='installer') {
            $target='libraries/queries/'.$moduleName.'.'.$this->sqlType.'.php';
        }
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
    // Replace !prefix! and !table! with actual values
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
	public function lastInsertId() {
		return parent::lastInsertId();
	}
	public function createTable($tableName,$structure,$verbose=false) {
    /*
        structure is an array of field names and definitions
    */
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
						<p class="error">Failed to create '.$tableName.' table!</p>
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
		$metaList,$menuList,$sidebarList = array(),
		$menuSource,
		$admin,
		$compressionType,
		$compressionStarted=false,
		$output=array(),
		$loginResult=false,
		$plugins = array(),
		$cdn,$smallStaticLinkRoot,$largeStaticLinkRoot,$flashLinkRoot,$cdnLinks = array(),
		$banned = false,
		$jsEditor;

	private $db;

    public function __construct() {

		$url=str_replace(array('\\','%5C'),'/',$_SERVER['REQUEST_URI']);
		if (strpos($url,'../')) killHacker('Uptree link in URI');
		$this->linkHome=str_ireplace('index.php','',$_SERVER['PHP_SELF']); 
		if (strpos($url,'?')>0) { 
			 // if using get, action based on query string
			 $queryString=$_SERVER['QUERY_STRING']; 
		} else { 
			 $queryString=substr($url,strlen($this->linkHome)-1); 
			// be sure to ===0 since false trips ==0
			 if (strpos($queryString,'index.php')===0) $queryString=substr($queryString,9); 
		} 
		$queryString=trim($queryString,'/'); 
		// Break URL up into action array
        $this->action=empty($queryString) ? array('default') : explode('/',$queryString);

        // Database connection
    	$this->db=new dynamicPDO();

        // Install
        if ($this->action[0]=='install') {
			$data=$this->db;
			require_once('libraries/install.php');
			die; // technically install.php should die at end, but to be sure...
		}

		// Load settings
        $statement=$this->db->query('getSettings');
        while ($row=$statement->fetch()) {
			if ($row['category']=='cms') {
				$this->settings[$row['name']]=$row['value'];
			} else {
				$this->settings[$row['category']][$row['name']]=$row['value'];
				$this->settings[$row['category']][$row['name']]=$row['value'];
			}
		}

		// Append attributions
		$this->settings['parsedFooterContent'] .= ($this->settings['removeAttribution'] == '0') ? '|attribution|' : '';

		// Check to see if CDN plugin should be loaded
		if($this->settings['useCDN']=='1') {
			common_loadPlugin($this,$this->settings['cdnPlugin']);
			$this->cdn =& $this->plugins[$this->settings['cdnPlugin']];
		}

		// Load the WYISWYG editor plugin
		common_loadPlugin($this,$this->settings['jsEditor']);
		$this->jsEditor =& $this->plugins[$this->settings['jsEditor']];

        // When registration gets it's own settings panel, remove this!
		$this->settings['register']['sender']='noreply@'.$_SERVER['SERVER_NAME'];

	 	// Check to see if compression is enabled
        $this->compressionType=false;
		if ($this->settings['compressionEnabled']) {
			if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'x-gzip')!==false) {
				$this->compressionType='x-gzip';
			} else if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false) {
				$this->compressionType='gzip';
			}
		}

        // Define server paths
        $this->domainName = 'http://'.$_SERVER['HTTP_HOST'];
		$this->siteRoot=$_SERVER['PHP_SELF'];
		$this->themeDir='themes/'.$this->settings['theme'].'/';
		$this->linkRoot=$this->linkHome;

		// Set up other CDN variables using linkRoot
		$this->smallStaticLinkRoot=(isset($this->settings['cdnSmall']{2})) ? $this->settings['cdnSmall'] : $this->linkRoot;
		$this->largeStaticLinkRoot=(isset($this->settings['cdnLarge']{2})) ? $this->settings['cdnLarge'] : $this->linkRoot;
		$this->flashLinkRoot=(isset($this->settings['cdnFlash']{2})) ? $this->settings['cdnFlash'] : $this->linkRoot;

        // Direct to Homepage
        if ($this->linkHome!='/') $url=str_replace($this->linkHome,'',$url);
        if (($url=='') ||
            ($url=='index.php') ||
            ($url=='index.html') ||
            ($url=='index.php?')) {
                // On default, go to homepage
                if(isset($this->settings['homepage']) && $this->action[0]=='default') {
                    $targetInclude='modules/'.$this->settings['homepage'].'/'.$this->settings['homepage'].'.module.php';
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

        // Direct banned users to page 'banned'
        $this->currentPage = ($this->banned) ? 'banned' : $this->action[0];

		// Does this module exist, and is it enabled? If not, is it a form, blog, or page?
		if($this->currentPage != 'admin' && !$this->banned){
			$moduleQuery = $this->db->prepare('getModuleByShortName','admin_modules');
			$moduleQuery->execute(array(':shortName' => $this->currentPage));
			$this->module = $moduleQuery->fetch();
            // Does this module exist, and is it enabled?
            if($this->module === false || $this->module['enabled'] == 0){ // Module does not exist or is disabled.
				if($this->module !== false){ // Exists, but is disabled.
					$this->currentPage = 'pageNotFound';
				}else if(file_exists('modules/'.$this->module['name'].'/'.$this->module['name'].'.module.php')){ // Exists in the file system, but not in the db.
					$statement = $this->db->prepare('newModule','admin_modules');
					$statement->execute(
						array(
							':name' => $this->currentPage,
							':shortName' => $this->currentPage,
							':enabled' => 0
						)
					);
					// If it was added to the database, this should fetch it:
					$moduleQuery->execute(array(':shortName' => $this->currentPage));
					$this->currentPage = 'pageNotFound'; // Still show a page-not-found because it will be disabled by default.
					$this->module = $moduleQuery->fetch();
				}else{ // Not a module, but could it be a form, blog or a page.
					// Check to see if it is a form:
                    $formStatement = $this->db->prepare('getTopLevelFormByShortName', 'dynamicForms'); //Form
					$formStatement->execute(
						array(
							':shortName' => $this->currentPage,
						)
					);
					if($formStatement->fetch() !== false){ // It's a Form
						$this->currentPage = 'dynamicForms';
						array_unshift($this->action, 'pages');
						$moduleQuery->execute(array(':shortName' => $this->currentPage));
						$this->module = $moduleQuery->fetch();
					}else{ // It's a Blog
                        // Check to see if it is a blog:
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
						}else{
                            // Check to see if it is a page:
							$statement = $this->db->prepare('getPageByShortNameAndParent', 'page');
							$statement->execute(
								array(
									':shortName' => $this->currentPage,
									':parent' => 0
								)
							);
							if($statement->fetch() !== false){// It's a Page
								$this->currentPage = 'page';
								array_unshift($this->action, 'page');
								$moduleQuery->execute(array(':shortName' => $this->currentPage));
								$this->module = $moduleQuery->fetch();
							}
						}
					}
				}
			}
			// If we didn't set the currentPage above, the page was not found.
            if($this->currentPage != 'pageNotFound'){
				$sidebarQuery = $this->db->prepare('getEnabledSidebarsByModule','admin_modules');
				$sidebarQuery->execute(
					array(
						':module' => $this->module['id']
					)
				);
				$sidebars = $sidebarQuery->fetchAll();
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

		// Get Left and Right Main Menu Order
        $statement=$this->db->query('getEnabledMainMenuOrderLeft');
		$this->menuList['left']=$statement->fetchAll();
		$statement=$this->db->query('getEnabledMainMenuOrderRight');
		$this->menuList['right']=$statement->fetchAll();
	
		// Cookies and Sessions
		$this->user['userLevel']=0;
		$userCookieName=$this->db->sessionPrefix.'SESSID';
		// If a logged in user who is not banned is trying to logout...
        if (!$this->banned &&
			($this->currentPage=='logout') &&
			(!empty($_COOKIE[$userCookieName]))
		) {	// Logout
            setCookie($userCookieName,'',0,$this->linkHome);
			$statement=$this->db->prepare('logoutSession');
			$statement->execute(array(
				':sessionID' => $_COOKIE[$userCookieName]
			));
		} else if (!$this->banned && !empty($_COOKIE[$userCookieName])) { // User doing anything else besides trying to logout
            // Check to see if the user's session is expired
            $userCookieValue=$_COOKIE[$userCookieName];
			// Purge expired sessions
			$this->db->query('purgeExpiredSessions');
			// Pull session record if still present after purge
			$statement=$this->db->prepare('getSessionById');
			$statement->execute(array(
				':sessionId' => $userCookieValue
			));
			if ($session=$statement->fetch(PDO::FETCH_ASSOC)) { // User's session has not expired
				if (
					($session['ipAddress']==$_SERVER['REMOTE_ADDR']) &&
					($session['userAgent']==$_SERVER['HTTP_USER_AGENT'])
				) { // Session IP and userAgent match user's IP and userAgent
					// Pull user info
					$statement=$this->db->prepare('pullUserInfoById');
					$statement->execute(array(
						':userId' => $session['userId']
					));
					if ($user=$statement->fetch()) {

						$this->user=$user;
                        // Load permissions
                        getUserPermissions($this->db,$this->user);
						$this->user['sessions']=$session;
						// Push expiration ahead
						$expires=time()+$this->settings['userSessionTimeOut'];
						$session['expires']=strtotime($session['expires']);
						if ($expires<$session['expires']) {
							/*
								If the current expiration is ahead of our calculated one,
								they must have hit 'keep me logged in' - so let's tack on
								a week.
							*/
							$expires+=604800;
						}

						// Update and sync cookie to server values
						setcookie($userCookieName,$userCookieValue,$expires,$this->linkHome,'','',true);
						$expires=gmdate("Y-m-d H:i:s",$expires);
						$statement=$this->db->prepare('updateSessionExpiration');
						$statement->execute(array(
							':expires' => $expires,
							':sessionId' => $session['sessionId']
						)) or die('Session Database failed updating expiration');

						// Update last access
                        $statement=$this->db->prepare('updateLastAccess');
						$statement->execute(array(
							':id' => $user['id']
						)) or die('User Database failed updating LastAccess<pre>'.print_r($statement->errorInfo()).'</pre>');

						//Load profile pictures
						$profilePictures = $this->db->prepare('getProfilePictures', 'gallery');
						$profilePictures->execute(array(':user' => $this->user['id']));
						$this->user['profilePictures'] = $profilePictures->fetchAll();

						//Load albums
						$albums = $this->db->prepare('getAlbumsByUser', 'gallery');
						$albums->execute(array(':user' => $this->user['id']));
						$this->user['albums'] = $albums->fetchAll();
                        $this->loginResult=true;


					}
				}
			}
		}
		/*
			If it drops through to here, user is not logged in...
			Are they trying to?
		*/
		if (!$this->banned && isset($_POST['login']) && $_POST['login']==$_SERVER['REMOTE_ADDR']) {
			$statement=$this->db->prepare('checkPassword');
			$statement->execute(array(
				':name' => $_POST['username'],
				':passphrase' => hash('sha256',$_POST['password'])
			));
			if ($user=$statement->fetch(PDO::FETCH_ASSOC)) {
				$this->user=$user;
                // Load permissions
                getUserPermissions($this->db,$this->user);
				// Purge existing sessions containing user ID
				$statement=$this->db->prepare('purgeSessionByUserId');
				$statement->execute(array(
					'userId' => $user['id']
				));
				// Create new session
				$userCookieValue = hash('sha256',
                     $user['id'].'|'.time().'|'.common_randomPassword(32,64)
				);
				$expires=time()+$this->settings['userSessionTimeOut'];
				if (isset($_POST['keepLogged']) && $_POST['keepLogged']=='on') {
					$expires+=+604800; // 1 week
				}
				// Update and sync cookie to server values
				setcookie($userCookieName,$userCookieValue,$expires,$this->linkHome,'','',true);
				$expires=gmdate("Y-m-d H:i:s",$expires);
				$statement=$this->db->prepare('updateUserSession');
				$statement->execute(array(
					':sessionId' => $userCookieValue,
					':userId'    => $user['id'],
					':expires'   => $expires,
					':ipAddress' => $_SERVER['REMOTE_ADDR'],
					':userAgent' => $_SERVER['HTTP_USER_AGENT']
				));
				$this->loginResult=true;

			}
		}
		
		$moduleQuery = $this->db->query('getEnabledModules');
		$modules = $moduleQuery->fetchAll();
		foreach ($modules as $module) {
			$this->output['moduleShortName'][$module['name']]=$module['shortName'];
            $filename = 'modules/'.$module['name'].'/'.$module['name'].'.startup.php';
			if(file_exists($filename)) {
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
			common_include('libraries/admin.template.php');
			common_include('libraries/admin.php');
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
				common_include('modules/pages/pages.module.php');
			}else if (file_exists($targetInclude = 'modules/'.$this->module['name'].'/'.$this->module['name'].'.module.php')) {
				common_include($targetInclude);
			} else {
				common_include('modules/pages/pages.module.php');
			}
			if (function_exists('page_getUniqueSettings')) {
				page_getUniqueSettings($this,$this->db);
			}
			$this->loadModuleTemplate('common');
			$this->loadModuleTemplate($this->module['name']);
		}
		// Get the plugins for this module
		
		$statement=$this->db->query('getEnabledPlugins');
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
		// Parse Sidebars Before Display
		if(isset($sidebars))
		{
			foreach($sidebars as $sidebar) {
				common_parseDynamicValues($this,$sidebar['titleUrl'],$this->db);
				common_parseDynamicValues($this,$sidebar['parsedContent'],$this->db);
				$this->sidebarList[$sidebar['side']][]=$sidebar;
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
		
		if(function_exists('theme_leftSidebar')) {
			theme_leftSidebar($this);
		}
		if (function_exists('theme_rightSidebar')) {
			theme_rightSidebar($this);
		}
		theme_footer($this);
	} /* __construct */

    //Anonymous Function Fix - adds support below 5.3
    public function arrayInterrater($item){
        return $item['display'];
    }

	public function loadModuleTemplate($module) {
		$currentThemeInclude=$this->themeDir.$module.'.template.php';
		$defaultThemeInclude='themes/default/'.$module.'.template.php';
        $moduleThemeInclude='modules/'.$module.'/'.$module.'.template.php';
		if(file_exists($currentThemeInclude)) {
			common_include($currentThemeInclude);
		} elseif(file_exists($defaultThemeInclude)) {
			common_include($defaultThemeInclude);
		} elseif(file_exists($moduleThemeInclude)) {
			common_include($moduleThemeInclude);
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