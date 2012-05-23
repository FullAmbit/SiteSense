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
function admin_formsBuild($data,$db)
{
	//permission check for forms edit
	if(!checkPermission('edit','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	$formId = (int)$data->action[3];
	// Check if Form Exists
	$statement = $db->prepare('getFormById','dynamicForms');
	$statement->execute(array(':id' => $formId));
	if(($data->output['formItem'] = $statement->fetch()) === FALSE)
	{
		$data->output['error'] = "The form you requested was not found";
		return;
	}
	
	// Do SideBar Settings For This Page Exist? (Match Row Count with total sidebar count)
	$maxSideBarCount = $db->countRows('sidebars');
	$statement = $db->prepare('countSideBarsByForm','dynamicForms');
	$statement->execute(array(':formId' => $formId));
	list($rowCount) = $statement->fetch();
	if($rowCount < $maxSideBarCount)
	{
		$i = $rowCount;
		// Get A List Of All SideBars
		$statement = $db->prepare('getAllSideBars','sidebars');
		$statement->execute();
		$sideBarList = $statement->fetchAll();
		foreach($sideBarList as $sideBarItem)
		{
			$i++;
			$statement = $db->prepare('createSideBarSetting','dynamicForms');
			$statement->execute(array(
				':formId' => $formId,
				':sideBarId' => $sideBarItem['id'],
				':enabled' => $sideBarItem['enabled'],
				':sortOrder' => $i
			));
		}
	}
	
	// Does a change need to be made?
	switch($data->action[4]){
		case 'enable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('enableSideBar', 'dynamicForms');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'disable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('disableSideBar', 'dynamicForms');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'moveDown':
		case 'moveUp':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('getSideBarSetting','dynamicForms');
			$statement->execute(array(':id' => $settingId));
			if(($sideBarItem = $statement->fetch()) === FALSE)
			{
				continue;
			}
			if($data->action[4] == 'moveUp' && intval($sideBarItem['sortOrder']) > 1) {
				$query1 = 'shiftSideBarOrderUpRelative';
				$query2 = 'shiftSideBarOrderUpByID';
			} else if($data->action[4] == 'moveDown' && intval($sideBarItem['sortOrder']) < $rowCount) {
				$query1 = 'shiftSideBarOrderDownRelative';
				$query2 = 'shiftSideBarOrderDownByID';
			}
			if(isset($query1))
			{
				$statement = $db->prepare($query1,'dynamicForms');
				$statement->execute(array(
					':sortOrder' => $sideBarItem['sortOrder'],
					':formId' => $formId
				));
				$statement = $db->prepare($query2,'dynamicForms');
				$statement->execute(array(
					':id' => $sideBarItem['id']
				));
			}
			
		break;
	}
	
	// Get List Of All Sidebars For This Form
	$statement = $db->prepare('getSideBarsByForm','dynamicForms');
	$statement->execute(array(':formId' => $formId));
	$data->output['sideBarList'] = $statement->fetchAll();
}

function admin_formsShow($data)
{
	if(isset($data->output['error']))
	{
		echo $data->output['error'];
	} else {
		theme_formsSidebarsTableHead($data);
		$count=0;
		foreach($data->output['sideBarList'] as $sideBar)
		{
			$action = ($sideBar['enabled'] == 1) ? 'disable' : 'enable';
			theme_formsSidebarsTableRow($data,$sideBar,$action,$count);
			$count++;
		}
		theme_formsSidebarsTableFoot();
}
}
?>
