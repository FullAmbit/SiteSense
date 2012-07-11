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
function admin_pagesBuild($data,$db)
{
	//permission check for pages edit
	if(!checkPermission('edit','pages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}

	$pageId = (int)$data->action[3];
	
	$statement = $db->prepare('getPageById','admin_pages');
	$statement->execute(array(':id' => $pageId));
	$data->output['pageItem'] = $statement->fetch();
	if($data->output['pageItem'] == FALSE || !is_numeric($pageId))
	{
		$data->output['error'] = "The page you requested was not found";
		return;
	}
	
	// Do Sidebar Settings For This Page Exist? (Match Row Count with total sidebar count)
	$maxSidebarCount = $db->countRows('sidebars');
	$statement = $db->prepare('countSidebarsByPage','admin_pages');
	$statement->execute(array(':pageId' => $pageId));
	list($rowCount) = $statement->fetch();
	if($rowCount < $maxSidebarCount) {
        $i = $rowCount;
		// Get A List Of All Sidebars
		$statement = $db->prepare('getAllSidebars','admin_sidebars');
		$statement->execute();
		$sidebarList = $statement->fetchAll();
		foreach($sidebarList as $sidebarItem) {
			$i++;
			$statement = $db->prepare('createSidebarSetting','admin_pages');
			$statement->execute(array(
				':pageId' => $pageId,
				':sidebarId' => $sidebarItem['id'],
				':enabled' => $sidebarItem['enabled'],
				':sortOrder' => $i
			));
		}
	}
	
	// Does a change need to be made?
	switch($data->action[4]){
		case 'enable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('enableSidebar','admin_pages');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'disable':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('disableSidebar','admin_pages');
			$statement->execute(array(':id' => $settingId));
			break;
		case 'moveDown':
		case 'moveUp':
			$settingId = (int)$data->action[5];
			$statement = $db->prepare('getSidebarSetting','admin_pages');
			$statement->execute(array(':id' => $settingId));
			if(($sidebarItem = $statement->fetch()) === FALSE)
			{
				continue;
			}
			// Get The Current Count
			$statement = $db->prepare('countSidebarsByPage','admin_pages');
			$statement->execute(array(':pageId' => $pageId));
			list($rowCount) = $statement->fetch();
			if($data->action[4] == 'moveUp' && intval($sidebarItem['sortOrder']) > 1) {
				
				$query1 = 'shiftSidebarOrderUpRelative';
				$query2 = 'shiftSidebarOrderUpByID';
			} else if($data->action[4] == 'moveDown' && intval($sidebarItem['sortOrder']) < $rowCount) {
				$query1 = 'shiftSidebarOrderDownRelative';
				$query2 = 'shiftSidebarOrderDownByID';
			}
			if(isset($query1))
			{
				$statement = $db->prepare($query1,'pages');
				$statement->execute(array(
					':sortOrder' => $sidebarItem['sortOrder'],
					':pageId' => $pageId
				));
				$statement = $db->prepare($query2,'pages');
				$statement->execute(array(
					':id' => $sidebarItem['id']
				));
			}
			
		break;
	}
	
	// Get List Of All Sidebars
	$statement = $db->prepare('getSidebarsByPage','admin_pages');
	$statement->execute(array(':pageId' => $pageId));
	$data->output['sidebarList'] = $statement->fetchAll();
}

function admin_pagesShow($data)
{
	if(isset($data->output['error']))
	{
		echo $data->output['error'];
	} else {
		theme_sidebarsTableHead($data);
		$count=0;
		foreach($data->output['sidebarList'] as $sidebar)
		{
			$action = ($sidebar['enabled'] == 1) ? 'disable' : 'enable';
			theme_sidebarsTableList($data,$sidebar,$count,$action);
			$count++;
		}
		theme_sidebarsTableFoot();
	}
}


?>
