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
function admin_modulesBuild($data,$db){
	$statement=$db->query('getAllModules','admin_modules');
	$moduleFiles=glob('modules/*.module.php');
	$data->output['modules']=$statement->fetchAll();
	$fileModules = array_map(
		function($path){
			$dirend = strrpos($path, '/') + 1;
			$nameend = strpos($path, '.');
			return substr($path, $dirend, $nameend - $dirend);
		}, 
		$moduleFiles
	);
	// Remove duplicate database entries
	$delete=$db->prepare('deleteModule','admin_modules');
	foreach($data->output['modules'] as $dbModule){
		foreach($data->output['modules'] as $key => $dbModule2){
			if(isset($duplicatedModules))
				if(in_array($dbModule2['id'],$duplicatedModules))
					continue;
			if($dbModule['name']==$dbModule2['name'] 
			&& $dbModule['id']!=$dbModule2['id']) {
				$delete->execute(array(':id' => $dbModule2['id']));
				unset($data->output['modules'][$key]);
				$duplicatedModules[]=$dbModule['id'];
			}
		}
	}
	//delete database entries which no longer have associated files
	foreach($data->output['modules'] as $dbModule){
		if(false === array_search($dbModule['name'], $fileModules)){
			$delete->execute(array(':id' => $dbModule['id']));
		}
	}
	//insert new modules into the database
	$insert = $db->prepare('newModule', 'modules');
	foreach($fileModules as $fileModule){
		$found = false;
		foreach($data->output['modules'] as $dbModule){
			if($dbModule['name'] == $fileModule){
				$found = true;
			}
		}
		if(!$found){
			$insert->execute(
				array(
					':name' => $fileModule,
					':shortName' => $fileModule,
					':enabled' => 0
				)
			);
		}
	}
	//--Reget All Modules--//
	$statement->execute();
	$data->output['modules'] = $statement->fetchAll();
	/*
	//--Build Uninstalled Module List--//
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
				$moduleSettings = $settingsFunc($data,$data);
				$data->output['uninstalledModules'][] = $moduleSettings;
			} else {
				$settings = array();
			}
			
		}
	}*/
}
function admin_modulesShow($data){
	/*-- Installed Modules --*/
	theme_modulesListTableHead();
	if (empty($data->output['modules'])) {
		theme_modulesListNoModules();
	} else {
		$count=0;
		foreach($data->output['modules'] as $module){
			/*switch($module['shortName'])
			{
				case 'forms':
					$link = 'admin/forms/list/';
					break;
				case 'page':
					$link = 'admin/pages/list';
					break;
				default:
					$link = 'admin/modules/sidebars/'.$module['id'];
					break;
			}*/
			theme_modulesListTableRow($data,$module,$count);
			$count++;
		}
	}
	theme_modulesListTableFoot();
	/*-- New Modules -- */
	/*theme_modulesListNewTableHead();
	if (empty($data->output['uninstalledModules'])) {
		theme_modulesListNoNewModules();
	} else {
		$count=0;
		foreach($data->output['uninstalledModules'] as $module){
			theme_modulesListNewTableRow($data,$module,$count,$data->linkRoot);
			$count++;
		}
	}
	theme_modulesListNewTableFoot();*/
}
?>