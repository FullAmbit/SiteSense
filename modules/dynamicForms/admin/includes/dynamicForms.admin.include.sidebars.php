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
function admin_dynamicFormsBuild($data,$db)
{
	//permission check for forms edit
	if(!checkPermission('edit','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	$formId = (int)$data->action[3];
	// Check if Form Exists
	$statement = $db->prepare('getFormById','admin_dynamicForms');
	$statement->execute(array(':id' => $formId));
	if(($data->output['formItem'] = $statement->fetch()) === FALSE)
	{
		$data->output['error'] = "The form you requested was not found";
		return;
	}
	
	// Do Sidebar Settings For This Page Exist? (Match Row Count with total sidebar count)
	$maxSidebarCount = $db->countRows($data->language.'_sidebars');
	$statement = $db->prepare('countSidebarsByForm','admin_dynamicForms');
	$statement->execute(array(':formId' => $formId));
	list($rowCount) = $statement->fetch();
	if($rowCount < $maxSidebarCount) {
		// Get A List Of All Sidebars
		$statement = $db->prepare('getAllSidebars','admin_sidebars');
		$statement->execute();
		$sidebarList = $statement->fetchAll();
		foreach($sidebarList as $sidebarItem) {
			$statement = $db->prepare('createSidebarSetting','admin_dynamicForms');
			$statement->execute(array(
				':formId' => $formId,
				':sidebarId' => $sidebarItem['id'],
				':enabled' => $sidebarItem['enabled'],
				':sortOrder' => admin_sortOrder_new($data,$db,'form_sidebars','sortOrder','form',$formId)
			));
		}
	}
	
	// Does a change need to be made?
	switch($data->action[4]){
		case 'enable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('enableSidebar','admin_dynamicForms');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'disable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('disableSidebar','admin_dynamicForms');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'moveDown':
		case 'moveUp':
            admin_sortOrder_move($data,$db,'form_sidebars',$data->action[4],$data->action[5],'sortOrder','form');
		    break;
	}
	
	// Get List Of All Sidebars For This Form
	$statement = $db->prepare('getSidebarsByForm','admin_dynamicForms');
	$statement->execute(array(':formId' => $formId));
	$data->output['sidebarList'] = $statement->fetchAll();
}

function admin_dynamicFormsShow($data)
{
	if(isset($data->output['error']))
	{
		echo $data->output['error'];
	} else {
		theme_dynamicFormsSidebarsTableHead($data);
		$count=0;
		foreach($data->output['sidebarList'] as $sidebar)
		{
			$action = ($sidebar['enabled'] == 1) ? 'disable' : 'enable';
			theme_dynamicFormsSidebarsTableRow($data,$sidebar,$action,$count);
			$count++;
		}
		theme_dynamicFormsSidebarsTableFoot();
}
}
?>
