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
	if(!checkPermission('access','sidebars',$data))
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '
			<h2>Insufficient Permissions</h2>
			You do not have the permissions to access this area';
			
			return;
	}
	
	/* first add any that are in the directory but not in the database */
	$files=glob('modules/*/sidebars/*.sidebar.php');
	$statement=$db->prepare('getSidebarNameByName','sidebars');
	
 	$wHandle=$db->prepare('insertSidebarFile','sidebars');
	foreach ($files as $fileName) {
		$targetName=substr(strrchr(str_replace('.sidebar.php','',$fileName),'/'),1);
		$statement->execute(array(
			':name' => $targetName
		));
		if (!$statement->fetch()) {
			$wHandle->execute(array(
				':name' => $targetName,
				':shortName' => common_generateShortName($targetName)
			));
			
			// Make Sidebar Settings //
			$sidebarId = $db->lastInsertId();
			$count = $db->countRows('sidebars');
			$sortOrder = $count;
			//---Pages---//
			$pageQ = $db->prepare('createSidebarSetting','pages');
			$statement = $db->prepare('getAllPageIds','pages');
			$statement->execute();
			$pageList = $statement->fetchAll();
		
			foreach($pageList as $pageItem)
			{
				$vars = array(
					':pageId' => $pageItem['id'],
					':sidebarId' => $sidebarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				
				$pageQ->execute($vars);
			}
			//---Modules---//
			$moduleQ = $db->prepare('createSidebarSetting','modules');
			$statement = $db->prepare('getAllModuleIds','modules');
			$statement->execute();
			$moduleList = $statement->fetchAll();
			foreach($moduleList as $moduleItem)
			{
				$vars = array(
					':moduleId' => $moduleItem['id'],
					':sidebarId' => $sidebarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				
				$moduleQ->execute($vars);
			}
			//---Forms---//
			$formQ = $db->prepare('createSidebarSetting','dynamicForms');
			$statement = $db->prepare('getAllFormIds','dynamicForms');
			$statement->execute();
			$formList = $statement->fetchAll();
			foreach($formList as $formItem)
			{
				$vars = array(
					':formId' => $formItem['id'],
					':sidebarId' => $sidebarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				
				$formQ->execute($vars);
			}
		}
		
	}
	/* now even tougher, remove any that are NOT listed */
	$statement=$db->query('getFromFiles','sidebars');
	$wHandle=$db->prepare('deleteById','sidebars');
	$data->output['sidebars']=array();
	while ($item = $statement->fetch()) {
		$testName='modules/'.$item['name'].'/sidebars/'.$item['name'].'.sidebar.php';
		if (!in_array($testName,$files)) {
			$wHandle->execute(array(
				':id' => $item['id']
			));
			//--Delete Form, Page, and Module Setting For Sidebar--//
			$vars = array(':sidebar' => $item['id']);
						
			$q1 = $db->prepare('deleteSidebarSettingBySidebar','dynamicForms');
			$q2 = $db->prepare('deleteSidebarSettingBySidebar','modules');
			$q3 = $db->prepare('deleteSidebarSettingBySidebar','pages');
			
			$q1->execute($vars);
			$q2->execute($vars);
			$q3->execute($vars);
		}
	}
	$statement=$db->query('getAllOrdered','sidebars');
	$data->output['sidebars']=$statement->fetchAll();
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$target='modules/sidebars/admin/include/sidebars.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_sidebarsBuild')) admin_sidebarsBuild($data,$db);
	$data->output['pageTitle']='Sidebars';
}
function admin_content($data) {
	if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_sidebarsShow($data);
		} else admin_unknown();
	}
}
?>