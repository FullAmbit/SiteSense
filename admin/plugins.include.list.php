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
	// Get Plugins That Have Yet To Be Installed
	$data->output['newPlugins'] = array();
	$dirHandle = scandir('plugins');
	foreach($dirHandle as $pluginDir)
	{
		if($dirHandle == '..' || $dirHandle == '.') continue;
		
		// Check For Install File
		if(file_exists('plugins/'.$pluginDir.'/install.php'))
		{
			$data->output['newPlugins'][] = $pluginDir;
		}
	}
	// Get All Installed Plugins
	$statement = $db->prepare('getAllPlugins','admin_plugins');
	$statement->execute();
	$data->output['pluginList'] = $pluginList = $statement->fetchAll();
}

function admin_pluginsShow($data)
{
	// Plugins Already In The Database
	theme_pluginsListTableHead('Installed Plugins');
	if(empty($data->output['pluginList']))
	{
		theme_pluginsListNoneInstalled('No installed plugins found');
	} else {
			
		foreach($data->output['pluginList'] as $pluginItem)
		{
			theme_pluginsListInstalledTableRow($pluginItem['name'],$pluginItem['id'],$data->linkRoot);
		}
	}
	theme_pluginsListTableFoot();
	// Uninstalled Plugins
	theme_pluginsListTableHead('New Plugins');
	if(empty($data->output['newPlugins']))
	{
		theme_pluginsListNoneInstalled('No new plugins to install');
	} else {
		foreach($data->output['newPlugins'] as $index => $pluginDir)
		{
			theme_pluginsListUninstalledTableRow($pluginDir,$data->linkRoot);
		}
	}
	theme_pluginsListTableFoot();
}

?>