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
function admin_pluginsBuild($data,$db) {
    if(!checkPermission('plugins_list','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    $statement=$db->query('getAllPlugins','plugins');
	$data->output['plugins']=$statement->fetchAll();
	// Build an array of the names of the plugins in the filesystem
	$dirs=scandir('plugins');
	foreach($dirs as $dir) {
		if($dir==='.' || $dir==='..') continue;
		if(file_exists('plugins/'.$dir.'/plugin.php')) {
			$filePlugins[]=$dir;
		}
	}
	// Remove duplicate database entries
	$delete=$db->prepare('deletePlugin','plugins');
	foreach($data->output['plugins'] as $plugin) {
		foreach($data->output['plugins'] as $key => $plugin2) {
			if(isset($duplicatedPlugins))
				if(in_array($plugin2['id'],$duplicatedPlugins)) continue;
			if($plugin['name']==$plugin2['name']
			&& $plugin['id']!=$plugin2['id']) {
				$delete->execute(array(':id' => $plugin2['id']));
				unset($data->output['plugins'][$key]);
				$duplicatedPlugins[]=$plugin['id'];
			}
		}
	}
	// Delete database entries which no longer have associated files
	foreach($data->output['plugins'] as $key =>$plugin) {
		if(false===array_search($plugin['name'],$filePlugins)) {
			$delete->execute(array(':id' => $plugin['id']));
			unset($data->output['plugins'][$key]);
		}
	}
	// Insert new plugins into the database
	$insert=$db->prepare('newPlugin','plugins');
	foreach($filePlugins as $filePlugin) {
		$found=false;
		foreach($data->output['plugins'] as $dbPlugin) {
			if($dbPlugin['name']==$filePlugin) {
				$found=true;
			}
		}
		if(!$found) {
			$insert->execute(
				array(
					':name' => $filePlugin,
					':shortName' => $filePlugin,
					':enabled' => 0
				)
			);
		}
	}
}
function admin_pluginsShow($data) {
	theme_pluginsListTableHead('Plugins');
	if(empty($data->output['plugins'])) {
		theme_pluginsListNoneInstalled('No plugins found');
	} else {
		$count=0;
		foreach($data->output['plugins'] as $plugin) {
			theme_pluginsListInstalledTableRow($plugin,$data,$count);
			$count++;
		}
	}
	theme_pluginsListTableFoot();
}
?>