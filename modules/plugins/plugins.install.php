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
* @author	 Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
function plugins_settings() {
	return array(
		'name'	    => 'plugins',
		'shortName' => 'plugins',
		'version'   => '1.0',
	);
}
function plugins_install($db,$drop=false,$firstInstall=false,$lang='en_us') {
	$structures = array(
		'plugins' => array(
				'id'		=>  SQR_IDKey,
				'name'	    =>  SQR_shortName,
				'enabled'   =>  SQR_boolean.' DEFAULT \'1\'',
				'isCDN'	    =>  SQR_boolean.' DEFAULT \'0\'',
				'isEditor'  =>  SQR_boolean.' DEFAULT \'0\''
		),
		'plugins_modules' => array(
			'plugin'	=> SQR_ID,
			'module'	=> SQR_ID
		)
	);
	if($drop){
		$db->dropTable('plugins');
		$db->dropTable('plugins_modules');
	}
	$db->createTable('plugins',$structures['plugins']);
	$db->createTable('plugins_modules',$structures['plugins_modules']);
	$dirs=scandir('plugins');
	foreach($dirs as $dir) {
		if(strpos($dir,'.')) continue;
		// Include the install file for this plugin
		if(file_exists('plugins/'.$dir.'/install.php'))
			common_include('plugins/'.$dir.'/install.php');
		// Get the plugin's settings
		$targetFunction=$dir.'_settings';
		if(function_exists($targetFunction)) {
			$settings=$targetFunction();
			// Run the plugin installation procedure
			$targetFunction=$dir.'_install';
			if(function_exists($targetFunction)) {
				$targetFunction(NULL,$db);
				// Add this plugin to the database
				// INSERT INTO !prefix!plugins (name,enabled,isCDN,isEditor) VALUES (:name,:enabled,:isCDN,:isEditor)
				$statement=$db->prepare('addPlugin','admin_plugins');
				$statement->execute(array(
					':pluginName' => $dir,
					':isCDN'	  => isset($settings['isCDN']) ? $settings['isCDN'] : '0',
					':isEditor'   => isset($settings['isEditor']) ? $settings['isEditor'] : '0'
				));
			}
		}
	}
	return NULL;
}
function plugins_uninstall($db,$lang='en_us') {
	$db->dropTable('plugins');
	$db->dropTable('plugins_modules');
}
?>