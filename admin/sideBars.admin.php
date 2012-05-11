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
function admin_buildContent($data,$db) {
	/**
	 *	Permissions: Writers + Admin Only
	**/
	if(!checkPermission('canAccessSideBarAdminPanel','core',$data))
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '
			<h2>Insufficient Permissions</h2>
			You do not have the permissions to access this area';
			
			return;
	}
	
	/* first add any that are in the directory but not in the database */
	$files=glob('sideBars/*.sideBar.php');
	$statement=$db->prepare('getSideBarNameByName','admin_sideBars');
	
 	$wHandle=$db->prepare('insertSideBarFile','admin_sideBars');
	foreach ($files as $fileName) {
		$targetName=substr(strrchr(str_replace('.sideBar.php','',$fileName),'/'),1);
		$statement->execute(array(
			':name' => $targetName
		));
		if (!$statement->fetch()) {
			$wHandle->execute(array(
				':name' => $targetName,
				':shortName' => common_generateShortName($targetName)
			));
			
			// Make Sidebar Settings //
			$sideBarId = $db->lastInsertId();
			$count = $db->countRows('sidebars');
			$sortOrder = $count;
			//---Pages---//
			$pageQ = $db->prepare('createSideBarSetting','admin_pages');
			$statement = $db->prepare('getAllPageIds','admin_pages');
			$statement->execute();
			$pageList = $statement->fetchAll();
		
			foreach($pageList as $pageItem)
			{
				$vars = array(
					':pageId' => $pageItem['id'],
					':sideBarId' => $sideBarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				
				$pageQ->execute($vars);
			}
			//---Modules---//
			$moduleQ = $db->prepare('createSideBarSetting','modules');
			$statement = $db->prepare('getAllModuleIds','modules');
			$statement->execute();
			$moduleList = $statement->fetchAll();
			foreach($moduleList as $moduleItem)
			{
				$vars = array(
					':moduleId' => $moduleItem['id'],
					':sideBarId' => $sideBarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				
				$moduleQ->execute($vars);
			}
			//---Forms---//
			$formQ = $db->prepare('createSideBarSetting','form');
			$statement = $db->prepare('getAllFormIds','form');
			$statement->execute();
			$formList = $statement->fetchAll();
			foreach($formList as $formItem)
			{
				$vars = array(
					':formId' => $formItem['id'],
					':sideBarId' => $sideBarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				
				$formQ->execute($vars);
			}
		}
		
	}
	/* now even tougher, remove any that are NOT listed */
	$statement=$db->query('getFromFiles','admin_sideBars');
	$wHandle=$db->prepare('deleteById','admin_sideBars');
	$data->output['sideBars']=array();
	while ($item = $statement->fetch()) {
		$testName='sideBars/'.$item['name'].'.sideBar.php';
		if (!in_array($testName,$files)) {
			$wHandle->execute(array(
				':id' => $item['id']
			));
			//--Delete Form, Page, and Module Setting For Sidebar--//
			$vars = array(':sidebar' => $item['id']);
						
			$q1 = $db->prepare('deleteSideBarSettingBySideBar','form');
			$q2 = $db->prepare('deleteSideBarSettingBySideBar','modules');
			$q3 = $db->prepare('deleteSideBarSettingBySideBar','admin_pages');
			
			$q1->execute($vars);
			$q2->execute($vars);
			$q3->execute($vars);
		}
	}
	$statement=$db->query('getAllOrdered','admin_sideBars');
	$data->output['sideBars']=$statement->fetchAll();
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$target='admin/sideBars.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_sideBarsBuild')) admin_sideBarsBuild($data,$db);
	$data->output['pageTitle']='SideBars';
}
function admin_content($data) {
	if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_sideBarsShow($data);
		} else admin_unknown();
	}
}
?>