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
	$moduleName = $data->action[3];
	
	$statement = $db->prepare('getModuleByShortName', 'modules');
	$statement->execute(array(':shortName' => $data->action[3]));
	$moduleInstalled = $statement->fetch();
	
	$moduleInstallFile = 'modules/'.$data->action[3].'.install.php';
	
	if( ! $data->action[3]) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No module name was entered to be installed';
	} else if ($moduleInstalled) {
		$data->output['rejectError']='module already installed';
		$data->output['rejectText']='The module sent has already been installed';
	} else if( ! file_exists($moduleInstallFile) ) {
		$data->output['rejectError']='module does not exist';
		$data->output['rejectText']='The module sent was not found';
	} else {
	
		require($moduleInstallFile);
		
		$settingsFunc = $moduleName.'_settings';
		if(function_exists($settingsFunc)) {
			$moduleSettings = $settingsFunc($db);
		} else {
			$settings = array();
		}
		
		//insert new modules into the database
		$insert = $db->prepare('newModule', 'modules'); 
		$insert->execute(
			array(
				':name' => $moduleSettings['name'],
				':shortName' => $moduleSettings['shortName'],
				':enabled' => 0
			)
		);
    
		$installFunc = $moduleName.'_install';
		if(function_exists($installFunc))
		{
			$installFunc($db);
		}

		$postInstallFunc = $moduleName.'_postInstall';
		if(function_exists($postInstallFunc))
		{
			$postInstallFunc($db);
		}
	}
}

function admin_modulesShow($data){
	$aRoot=$data->linkRoot.'admin/modules/';
	if (empty($data->output['rejectError'])) {
		theme_modulesInstallSuccess();
	} else {
		theme_rejectError($data);
	}
}
?>