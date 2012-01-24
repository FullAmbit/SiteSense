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
function admin_pluginsBuild($data,$db)
{
	// Plugin Name?
	$pluginDir = $data->action[3];
	if(empty($pluginDir) || !file_exists('plugins/'.$pluginDir.'/install.php'))
	{
		$data->output['pluginError'] = 'The plugin you specified could not be found.';
		return;
	}
	
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
		
		$data->output['installMessages'] = $function($data,$db);
		
		if($data->output['installSuccess'] == TRUE)
		{
			// Add To SQL
			$statement = $db->prepare('addPlugin','admin_plugins');
			$statement->execute(array(
				':pluginName' => $pluginDir,
				':isCDN' => isset($settings['isCDN']) ? $settings['isCDN'] : '0',
				':isEditor' => isset($settings['isEditor']) ? $settings['isEditor'] : '0'
			));
			$data->output['lastInsertId'] = $db->lastInsertId();
			
			$postInstall = $pluginDir.'_postInstall';
			// Run Post Install Functions
			if(function_exists($postInstall))
			{
				$postInstall($data,$db);
			}
			
			// Rename Install File
			rename('plugins/'.$pluginDir.'/install.php','plugins/'.$pluginDir.'/install_expired.exp');
		}
	} else {
		$data->output['pluginError'] = 'The install function is missing from your plugin\'s installation file.';
	}
}

function admin_pluginsShow($data)
{
	if(isset($data->output['pluginError']))
	{
		theme_pluginsInstallError($data->output['pluginError']);
		return;
	}
	theme_pluginsInstallOutput($data->output['installMessages']);
	
	if($data->output['installSuccess'] == TRUE)
	{
		theme_pluginsInstallSuccess($data->linkRoot);
	}
}

?>