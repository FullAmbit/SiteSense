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
	$statement = $db->query('getAllModules', 'modules');
	$moduleFiles = glob('modules/*/*.module.php');
	$dbModules = $statement->fetchAll();
	$fileModules = array_map(
		function($path){
			$dirend = strrpos($path, '/') + 1;
			$nameend = strpos($path, '.');
			return substr($path, $dirend, $nameend - $dirend);
		}, 
		$moduleFiles
	);
	$delete = $db->prepare('deleteModule', 'modules');
	foreach($dbModules as $dbModule){
		foreach($dbModules as &$dbModule2){
			if(
				$dbModule['name'] == $dbModule2['name'] 
				&&
				$dbModule['id'] != $dbModule2['id']
			){
				$delete->execute(array(':id' => $dbModule2['id']));
				unset($dbModule2);
			}
		}
	}
	//delete database entries which no longer have associated files
	foreach($dbModules as $dbModule){
		if(false === array_search($dbModule['name'], $fileModules)){
			$delete->execute(array(':id' => $dbModule['id']));
		}
	}
	//insert new modules into the database
	$insert = $db->prepare('newModule', 'modules');
	foreach($fileModules as $fileModule){
		$found = false;
		foreach($dbModules as $dbModule){
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
	common_redirect_local($data, 'admin/modules/');
}
function admin_modulesShow($data){
}