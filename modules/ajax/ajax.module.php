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
function ajax_buildContent($data,$db) {
	// URL Remapping (Taken from Common.PHP)
	$newAction = $data->action;
	$newAction = array_unique(array_slice($newAction,1));
				
	$url = implode('/',$newAction);			
			
	$rewrite = $db->prepare('findReplacement','admin_dynamicURLs');
	$rewrite->execute(array(':url' => $url));
		
	// We Got A ReMap
	if(FALSE !== ($row = $rewrite->fetch()))
	{
		$url = preg_replace('~' . $row['match'] . '~',$row['replace'],$url); // Our New URL
	}
	$url = explode('/',$url);
	array_pop($url);
	$url = array_pad($url,12,false);
	$data->action = $url;
	// What Module Are We Calling?
	$module = ($data->action[0]) ? $data->action[0] : 'default';
	// Check If In Database And Enabled
	$statement = $db->prepare('getModuleByShortName','admin_modules');
	$statement->execute(array(':shortName' => $module));
	$moduleData = $statement->fetch();
	// Module Doesn't Exist, or not enabled
	if($module == '' || $moduleData['enabled'] == '0' || $moduleData === FALSE)
	{
		echo 'The page you requested was not found';
		return;
	}
	// Load Sidebars //
	$sidebarQuery = $db->prepare('getEnabledSidebarsByModule', 'admin_modules');
	$sidebarQuery->execute(array(
		':module' => $moduleData['id']
	));
	$sidebars = $sidebarQuery->fetchAll();
	$data->sidebarList = array();
	foreach($sidebars as $sidebar)
	{
		$data->sidebarList[$sidebar['side']][]=$sidebar;
	}
	
	// Load The AJAX Version Of Our Module (Our journey begins...)---------------------------------------
	common_include('modules/'.$module.'/'.$module.'.module.php');
	$data->loadModuleTemplate($module);
	page_getUniqueSettings($data);
	page_buildContent($data,$db);	
}
?>