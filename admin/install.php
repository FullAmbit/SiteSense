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
define("INSTALLER", true);

$settings=array(
  'setupPassword'=> 'startitup',
  'saveToDb' => array(
    'siteTitle' => 'SiteSense',
    'homepage' => 'default',
    'theme' => 'default',
    'language' => 'en',
    'characterEncoding' => 'utf-8',
    'compressionEnabled' => true,
    'compressionLevel' => 9,
    'userSessionTimeOut' => 1800, /* in seconds */
    'useModRewrite' => true,
    'hideContentGuests' => 'no',
    'showPerPage' => 5,
    'rawFooterContent' => '&copy; SiteSense',
    'parsedFooterContent' => '&copy; SiteSense',
    'cdnSmall' => '',
    'cdnFlash' => '',
    'cdnLarge' => '',
    'useCDN' => '0',
    'cdnBaseDir' => '',
    'defaultBlog' => 'news',
    'useBBCode' => '1',
    'jsEditor' => 'ckeditor',
    'version' => 'Pre-Alpha',
    'verifyEmail' => 1,
    'requireActivation' => 0,
    'removeAttribution' => 0
  )
);

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
  ($_POST['spw']!==$settings['setupPassword'])
) {
  echo (
    (isset($_POST['spw']) && ($_POST['spw']!=$settings['setupPassword'])) ? '
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

  $drop = false;
  if( isset($_POST['cbDrop']) && $_POST['cbDrop']=='drop' )
	$drop = true;

  $data->installErrors=0;
  $data->loadModuleQueries('installer',true);
  $structures=installer_structures();
  echo '<p>Connect to Database Successful</p>';
  
  if($drop) {
	$data->dropTable('settings');
	$data->dropTable('banned');
	$data->dropTable('sessions');
	$data->dropTable('sidebars');
	$data->dropTable('main_menu');
	$data->dropTable('activations');
	$data->dropTable('url_remap');
	$data->dropTable('modules');
	$data->dropTable('module_sidebars');
  }
  
  //-------------------------------------------------------------------------//
  //                Create Tables                     //
  //-------------------------------------------------------------------------//
  //-- Make Settings Table
  if ($data->createTable('settings',$structures['settings'],true)) {
    try {
      $statement=$data->prepare('addSetting','installer');
      echo '
        <div>';
      foreach ($settings['saveToDb'] as $key => $value) {
        $statement->execute(array(
          ':name' => $key,
          ':category' => 'cms',
          ':value' => $value
        ));
        $result=$statement->fetchAll();
        echo '
          Created ',$key,' Entry<br />';
      }
      echo '
        </div><br />';
    } catch (PDOException $e) {
      $data->installErrors++;
      echo '
        <h2>Database Connection Error</h2>
        <pre>'.$e->getMessage().'</pre>';
    }
  }
  
  $data->createTable('banned',$structures['banned'],true);
  $data->createTable('sessions',$structures['sessions'],true);
  $data->createTable('sidebars',$structures['sidebars'],true);
  $data->createTable('main_menu',$structures['main_menu'],true);
  $data->createTable('activations',$structures['activations'],true);
  
  //-- Create url_remap Table
  $data->createTable('url_remap',$structures['url_remap'],true);
  
  //-- Create Module Related Tables
  $data->createTable('modules',$structures['modules'],true);
  $data->createTable('module_sidebars',$structures['module_sidebars'],true);
  
  //-------------------------------------------------------------------------//
  //                Install Modules                   //
  //-------------------------------------------------------------------------//
  
  //--Build Uninstalled Module List--//
  $attributes = array();
  $uninstalledModuleFiles = glob('modules/*.install.php');
  foreach($uninstalledModuleFiles as $moduleInstallFile)
  {
    if(empty($moduleInstallFile)) continue;
    
    if(file_exists($moduleInstallFile))
    {
      require($moduleInstallFile);
      
      $dirend = strrpos($moduleInstallFile, '/') + 1;
      $nameend = strpos($moduleInstallFile, '.');
      $moduleName = substr($moduleInstallFile, $dirend, $nameend - $dirend);
      
      $settingsFunc = $moduleName.'_settings';
      if(function_exists($settingsFunc)) {
        $moduleSettings = $settingsFunc($data);
      } else {
        $settings = array();
      }
      
      $statement = $data->prepare('getModuleByShortName', 'modules');
      $statement->execute(array(':shortName' => $moduleSettings['shortName']));
      $moduleInstalled = $statement->fetch();
      
      $moduleInstallFile = 'modules/'.$moduleSettings['shortName'].'.install.php';
      $moduleFile = 'modules/'.$moduleSettings['shortName'].'.module.php';
      
       if ($moduleInstalled) {
        echo 'The ',$moduleSettings['shortName'],' module has already been installed';
      } else if( ! file_exists($moduleFile) ) {
        echo 'The ',$moduleSettings['shortName'],' module does not have an associated module file';
      } else {
        
        $installFunc = $moduleName.'_install';
        if(function_exists($installFunc))
        {
          $attributes[$moduleName] = $installFunc($data,$drop);
        }
        
        $postInstallFunc = $moduleName.'_postInstall';
        if(function_exists($postInstallFunc))
        {
          $postInstallFunc($data);
        }
      }
    }
  }
  $newPassword = $attributes['user'];
  
  // Add All Modules to Modules Table
  $moduleFiles = glob('modules/*.module.php');
  $fileModules = array_map(
    function($path){
      $dirend = strrpos($path, '/') + 1;
      $nameend = strpos($path, '.');
      return substr($path, $dirend, $nameend - $dirend);
    }, 
    $moduleFiles
  );

  $enabledModules = array(
    'forms' => 1,
    'default' => 1,
    'blogs' => 1,
    'page' => 1,
    'login' => 1,
    'logout' => 1,
    'register' => 1,
    'user' => 1
  );

  //insert new modules into the database
  $insert = $data->prepare('newModule', 'installer'); 
  foreach($fileModules as $fileModule){
    $enabled = isset($enabledModules[$fileModule]) ? 1 : 0;
    $insert->execute(
      array(
        ':name' => $fileModule,
        ':shortName' => $fileModule,
        ':enabled' => $enabled
      )
    );
  }
  
  //-------------------------------------------------------------------------//
  //                Install Plugins                     //
  //-------------------------------------------------------------------------//
  if($drop)
	$data->dropTable('plugins');
	
  $data->createTable('plugins',$structures['plugins'],true);
  
  // Get Plugins That Have Yet To Be Installed
  $dirHandle = scandir('plugins');
  foreach($dirHandle as $pluginDir)
  {
    if($dirHandle == '..' || $dirHandle == '.') continue;
    
    // Check For Install File
    if(file_exists('plugins/'.$pluginDir.'/install.php'))
    {
      require('plugins/'.$pluginDir.'/install.php');
      
      $function = $pluginDir.'_install';
      if(function_exists($function))
      {
        // Get Settings //
        $settingsFunc = $pluginDir.'_settings';
        if(function_exists($settingsFunc))
        {
          $settings = $settingsFunc($data,$data);
        } else {
          $settings = array();
        }
        
        // Run Plugin Installation //
        $function($data,$data);
        // Add To SQL
        $statement = $data->prepare('addPlugin','installer');
        $statement->execute(array(
          ':pluginName' => $pluginDir,
          ':isCDN' => isset($settings['isCDN']) ? $settings['isCDN'] : '0',
          ':isEditor' => isset($settings['isEditor']) ? $settings['isEditor'] : '0'
        ));
        $data->output['lastInsertId'] = $data->lastInsertId();
        // Run Post Install Functions
        $postInstall = $pluginDir.'_postInstall';
        if(function_exists($postInstall))
        {
          $postInstall($data,$data);
        }
      }
    }
  }
  
  if ($data->installErrors==0) {
    echo '
      <h2 id="done">Complete</h2>
      <p class="success">
        Installation/Verification Completed Successfully
      </p><p>
        It is recommended to log into the Admin panel and go to the "mainMenu" function to populate the menu functions. Until you do so, there will be no menu. Any sidebars you have installed will also not show until you enable them in the Admin sidebar control.
      </p>';
    if (isset($newPassword)) {
      echo '
      <p>
        A new administrator login was created. You must use the following information to log into the system:
      </p>
      <dl>
        <dt>Username:</dt><dd>admin</dd>
        <dt>Password:</dt><dd>',$newPassword,'</dd>
      </dl>
      <p>
        Changing the password is recommended.
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