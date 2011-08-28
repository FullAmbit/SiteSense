<?php
function admin_modulesBuild($data,$db){
	$statement = $db->query('getAllModules', 'modules');
	$moduleFiles = glob('modules/*.module.php');
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