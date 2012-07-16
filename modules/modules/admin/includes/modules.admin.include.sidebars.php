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
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$moduleId = $data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getModuleById','admin_modules');
	$statement->execute(array(':id' => $data->action[3]));
	$module = $statement->fetch();
	if($module === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Module With ID ' . $data->action[3] . ' Doesn\'t Exist</h2>';
		return;
	}
	$data->output['module'] = $module;
	
	// Do Sidebar Settings For This Page Exist? (Match Row Count with total sidebar count)
	$maxSidebarCount = $db->countRows('sidebars');
	$statement = $db->prepare('countSidebarsByModule','admin_modules');
	$statement->execute(array(':moduleId' => $moduleId));
	list($rowCount) = $statement->fetch();
	if($rowCount < $maxSidebarCount) {
		// Get A List Of All Sidebars
		$statement = $db->prepare('getAllSidebars','admin_sidebars');
		$statement->execute();
		$sidebarList = $statement->fetchAll();
		foreach($sidebarList as $sidebarItem) {
			$statement = $db->prepare('createSidebarSetting','admin_modules');
			$statement->execute(array(
				':moduleId' => $moduleId,
				':sidebarId' => $sidebarItem['id'],
				':enabled' => $sidebarItem['enabled'],
				':sortOrder' => admin_sortOrder_new($db,'module_sidebars','sortOrder','module',$moduleId)
			));
		}
	}
	
	
	// Does a change need to be made?
	switch($data->action[4]){
		case 'enable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('enableSidebar','admin_modules');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'disable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('disableSidebar','admin_modules');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'moveDown':
		case 'moveUp':
            admin_sortOrder_move($db,'module_sidebars',$data->action[4],$data->action[5],'sortOrder','module');
		    break;
	}
	//
	$statement = $db->prepare('getSidebarsByModule','admin_modules');
	$statement->execute(array(':module' => $module['id']));
	$data->output['sidebars'] = $statement->fetchAll();
}
function admin_modulesShow($data){
	theme_modulesSidebarsTableHead($data);
	$count=0;
	foreach($data->output['sidebars'] as $sidebar){
		$action = ($sidebar['enabled'] == 1) ? 'disable' : 'enable';
		theme_modulesSidebarsTableRow($data,$sidebar,$action,$count);
		$count++;
	}
	theme_modulesSidebarsTableFoot();
}
?>