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
function admin_modulesBuild($data,$db) {
    if(!checkPermission('list','modules',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    $statement=$db->query('getAllModules','admin_modules');
	$data->output['modules']=$statement->fetchAll();
	// Build an array of the names of the modules in the filesystem
	$moduleFiles=glob('modules/*/*.module.php');
	$fileModules=array_map(
		function($path) {
			$dirEnd=strrpos($path,'/')+1;
			$nameEnd=strpos($path,'.');
			return substr($path,$dirEnd,$nameEnd-$dirEnd);
		},
		$moduleFiles
	);
	// Remove duplicate database entries
	$delete=$db->prepare('deleteModule','admin_modules');
	foreach($data->output['modules'] as $dbModule) {
		foreach($data->output['modules'] as $key => $dbModule2) {
			if(isset($duplicatedModules))
				if(in_array($dbModule2['id'],$duplicatedModules)) continue;
			if($dbModule['name']==$dbModule2['name']
			&& $dbModule['id']!=$dbModule2['id']) {
				$delete->execute(array(':id' => $dbModule2['id']));
				unset($data->output['modules'][$key]);
				$duplicatedModules[]=$dbModule['id'];
			}
		}
	}
	// Delete database entries which no longer have associated files
	foreach($data->output['modules'] as $key => $dbModule) {
		if(false===array_search($dbModule['name'],$fileModules)) {
			$delete->execute(array(':id' => $dbModule['id']));
			unset($data->output['modules'][$key]);
		}
	}
	// Insert new modules into the database
	$insert=$db->prepare('newModule','admin_modules');
	foreach($fileModules as $fileModule) {
		$found=false;
		foreach($data->output['modules'] as $dbModule) {
			if($dbModule['name']==$fileModule) {
				$found=true;
			}
		}
		if(!$found) {
			$insert->execute(
				array(
					':name' => $fileModule,
					':shortName' => $fileModule,
					':enabled' => 0
				)
			);
		}
	}
}
function admin_modulesShow($data){
	theme_modulesListTableHead($data);
	if (empty($data->output['modules'])) {
		theme_modulesListNoModules();
	} else {
		$count=0;
		foreach($data->output['modules'] as $module){
			theme_modulesListTableRow($data,$module,$count);
			$count++;
		}
	}
	theme_modulesListTableFoot();
}
?>