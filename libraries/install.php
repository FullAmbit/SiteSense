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
define('INSTALLER', true);
$setupPassword = 'startitup';
if (file_exists('INSTALL.LOCK')) {
	die('Installer is locked.');
}
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
  xmlns="http://www.w3.org/1999/xhtml"
  lang="en"
  xml:lang="en"
><head>
<meta
  http-equiv="Content-Type"
  content="text/html; charset=utf-8"
/>
<meta
  http-equiv="Content-Language"
  content="en"
/>
<link
  type="text/css"
  rel="stylesheet"
  href="themes/default/installer.css"
  media="screen,projection,tv"
/>
<title>
  SiteSense Installer
</title>
</head><body>
<h1>SiteSense Installer/Upgrader</h1>
';
if (
	!isset($_POST['spw']) ||
	($_POST['spw']!==$setupPassword)
) {
	echo (
	(isset($_POST['spw']) && ($_POST['spw']!=$setupPassword)) ? '
<p class="error">Incorrect Setup Password</p>' :
	    ''
	),'
<form action="" method="post">
  <fieldset>
	<label for="spw">Please Enter Your Setup Password to Continue<br /></label>
	<input type="password" id="spw" name="spw" width="24" /><br /><br />
	<label for="cbDrop">
	  <input type="checkBox" class="checkBox" id="cbDrop" name="cbDrop" value="drop" />
	  Drop all tables first?<br />
	</label>
	<p class="warning">*** WARNING *** Dropping all tables will erase ALL entries in the CMS!</p>
  </fieldset>
</form>';
} else {
	$lang = 'en_us';
	$drop = false;
	if( isset($_POST['cbDrop']) && $_POST['cbDrop']=='drop' )
	    $drop = true;
	$db->installErrors=0;
	$db->loadModuleQueries('installer',true);
	$db->loadCommonQueryDefines(true);
	$structures=installer_tableStructures();
	echo '<p>Connect to Database Successful</p>';

	if($drop) {
	    $db->dropTable('settings',$lang);
	    $db->dropTable('banned');
	    $db->dropTable('sessions');
	    $db->dropTable('sidebars',$lang);
	    $db->dropTable('main_menu',$lang);
	    $db->dropTable('activations');
	    $db->dropTable('urls');
	    $db->dropTable('modules');
	    $db->dropTable('module_sidebars');
	    $db->dropTable('languages');
	    $db->dropTable('languages_phrases',$lang);
	    $db->dropTable('hostnames');
	    // Dynamic User Permissions
	    $db->dropTable('user_groups');
	    $db->dropTable('user_group_permissions');
	    $db->dropTable('user_permissions');
	}
	
	// Install modules
	$coreModules = array(
		'languages',
		'settings',
	    'sidebars',
	    'modules',
	    'dynamicForms',
	    'urls',
	    'blogs',
	    'pages',
	    'mainMenu',
	    'dashboard',
	    'hostnames',
	    'plugins',
	    'users',
	);

	$uninstalledModuleFiles = array_flip(glob('modules/*/*.install.php'));
	$temp = array_flip($uninstalledModuleFiles);
	unset($uninstalledModuleFiles['modules/sidebars/sidebars.install.php'],$uninstalledModuleFiles['modules/languages/languages.install.php'],$uninstalledModuleFiles['modules/settings/settings.install.php']);
	$uninstalledModuleFiles = array('modules/languages/languages.install.php'=>rand(),'modules/sidebars/sidebars.install.php'=>rand(),'modules/settings/settings.install.php'=>rand()) + $uninstalledModuleFiles;
	$uninstalledModuleFiles = array_flip($uninstalledModuleFiles);
	
	$moduleSettings=array();
	foreach($uninstalledModuleFiles as $moduleInstallFile) {
	    // Include the install file for this module
	    if(!file_exists($moduleInstallFile)) {
	        $db->output['rejectError']='Module installation file does not exist';
	        $db->output['rejectText']='The module installation could not be found.';
	    } else {
	        common_include($moduleInstallFile);
	        // Extract the name of the module from the filename
	        $dirEnd=strrpos($moduleInstallFile,'/')+1;
	        $nameEnd=strpos($moduleInstallFile,'.');
	        $moduleName=substr($moduleInstallFile,$dirEnd,$nameEnd-$dirEnd);
	        if(in_array($moduleName,$coreModules)) {
	            // Run the module installation procedure
	            $targetFunction=$moduleName.'_install';
	            if(!function_exists($targetFunction)) {
	                $db->output['rejectError']='Improper installation file';
	                $db->output['rejectText']='The module install function could not be found within the module installation file.';
	            } elseif($moduleName=='users') {
	                $newPassword=$targetFunction($db,$drop,TRUE);
	            } else {
	                $targetFunction($db,$drop,TRUE);
	            }
	            $targetFunction=$moduleName.'_settings';
	            if(function_exists($targetFunction)) {
	                $moduleSettings[$moduleName]=$targetFunction();
	                
	                $statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>'en_us'));
	                // Load The Phrases (Admin End)
					if (file_exists('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.en_us.php')) {
						common_include('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.en_us.php');
						$func = 'languages_'.$moduleName.'_en_us';
						if (function_exists($func)) {
							$phrases = $func();
							if(isset($phrases['core']) && is_array($phrases['core']) && !empty($phrases['core'])){
								foreach($phrases['core'] as $phrase=>$text){
									$result = $statement->execute(array(
										':phrase' => $phrase,
										':text' => $text,
										':module' => '',
										':isAdmin' => 0
									));
								}
								unset($phrases['core']);
							}
							// Save The Modular Phrases, Set isADMIN To True
							foreach($phrases as $phrase => $text){
								$result = $statement->execute(array(
									':phrase' => $phrase,
									':text' => $text,
									':module' => $moduleSettings[$moduleName]['shortName'],
									':isAdmin' => 0
								));
							}
						}
					}
	                
	                // Load The Phrases (Admin End)
					if (file_exists('modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.en_us.php')) {
						common_include('modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.en_us.php');
						$func = 'languages_'.$moduleName.'_admin_en_us';
						if (function_exists($func)) {
							$phrases = $func();
							if(isset($phrases['core']) && is_array($phrases['core']) && !empty($phrases['core'])){
								foreach($phrases['core'] as $phrase=>$text){
									$result = $statement->execute(array(
										':phrase' => $phrase,
										':text' => $text,
										':module' => '',
										':isAdmin' => 1
									));
								}
								unset($phrases['core']);
							}
							// Save The Modular Phrases, Set isADMIN To True
							foreach($phrases as $phrase => $text){
								$result = $statement->execute(array(
									':phrase' => $phrase,
									':text' => $text,
									':module' => $moduleSettings[$moduleName]['shortName'],
									':isAdmin' => 1
								));
							}
						}
					}
	            } else {
	                $db->output['rejectError']='Improper installation file';
	                $db->output['rejectText']='The module install function could not be found within the module installation file.';
	            }
	        } else if ($drop) {
	            // Run the module uninstall procedure
	            $targetFunction=$moduleName.'_uninstall';
	            if(!function_exists($targetFunction)) {
	                $db->output['rejectError']='Improper installation file';
	                $db->output['rejectText']='The module uninstall function could not be found within the module installation file.';
	            } else $targetFunction($db);
	        }
	    }
	}

	$moduleFiles=glob('modules/*/*.module.php');
	// Build an array of the names of the modules in the filesystem
	$fileModules=array_map(
	    function($path) {
	        $dirEnd=strrpos($path,'/')+1;
	        $nameEnd=strpos($path,'.');
	        return substr($path,$dirEnd,$nameEnd-$dirEnd);
	    },
	    $moduleFiles
	);
	// Insert new modules into the database
	$insert=$db->prepare('newModule');
	foreach($fileModules as $fileModule) {
	    $shortName=$fileModule;
	    if(array_key_exists($fileModule,$moduleSettings)) {
	        if(array_key_exists('shortName',$moduleSettings[$fileModule])) {
	            $shortName=$moduleSettings[$fileModule]['shortName'];
	        }
	    }
	    $enabled=in_array($fileModule,$coreModules) ? 1 : 0;
	    $insert->execute(
	        array(
	            ':name'      => $fileModule,
	            ':shortName' => $shortName,
	            ':enabled'   => $enabled,
				':version'   => $moduleSettings[$fileModule]['version'],
	        )
	    );
	}

	// Set up default permission groups
	$defaultPermissionGroups=array(
		'Writer'    => array(
			'core_access',
			'dashboard_access',

			'mainMenu_access',
			'mainMenu_add',
			'mainMenu_delete',
			'mainMenu_disable',
			'mainMenu_edit',
			'mainMenu_enable',
			'mainMenu_list',

			'sidebars_access',
			'sidebars_add',
			'sidebars_delete',
			'sidebars_edit',
			'sidebars_list',

			'urls_access',
			'urls_add',
			'urls_delete',
			'urls_edit',
			'urls_list'
		),
		'Moderator' => array(
			'core_access',
			'dashboard_access'
		),
		'Blogger'   => array(
			'core_access',
			'dashboard_access'
		),
		'User'      => array()
	);
	foreach($defaultPermissionGroups as $groupName => $permissions) {
	    foreach($permissions as $permissionName) {
	        $statement=$db->prepare('addPermissionByGroupName');
	        $statement->execute(
	            array(
	                ':groupName' => $groupName,
	                ':permissionName' => $permissionName,
	                ':value' => '0'
	            )
	        );
	    }
	}
	if ($db->installErrors==0) {
	    echo '
	  <h2 id="done">Complete</h2>
	  <p class="success">
	    Installation/Verification Completed Successfully
	  </p><p>
	    It is recommended to log into the Admin panel and go to the "mainMenu" function to populate the menu functions. Until you do so, there will be no menu. Any sidebars you have installed will also not show until you enable them in the Admin sidebar control.
	  </p>';
	    if (isset($newPassword)) {
			$touch = touch('INSTALL.LOCK'); // attempt to lock the installer
			if (!$touch) {
				echo '<p>
					The installation is complete, however, <strong>there is one last step you must do.</strong> Create an empty file named "INSTALL.LOCK" (without quotes) in the root of your SiteSense directory. Until you do this, anybody will be able to delete your data using this page.
				</p>';
			}
	        echo '
	  <p>
	    A new administrator login was created. You must use the following information to log into the system:
	  </p>
	  <dl>
	    <dt>Username:</dt><dd>admin</dd>
	    <dt>Password:</dt><dd>',$newPassword,'</dd>
	  </dl>
	  <p>
	    Changing the password is recommended. <a href="admin/users/edit/1/" class="error">Click here</a> to login to the admin panel.
	  </p>';
	    }
	} else {
	    echo '
	  <h2 id="done">Errors Present</h2>
	  <p>
	    We were unable to build the databases properly. Please review the above errros before attempting to use this installation.
	  </p>';
	}
}
echo '
</body></html>';
?>