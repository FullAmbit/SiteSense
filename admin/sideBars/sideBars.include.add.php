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
common_include('libraries/forms.php');

function admin_sideBarsBuild($data,$db) {
    if(!checkPermission('sidebars_add','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	// Load Form
	$data->output['sideBarForm'] = new formHandler('sideBars',$data,true);
	
	if(!empty($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['sideBarForm']->fromForm)) {
		$data->output['sideBarForm']->populateFromPostData();
		/**
		 * Set up Short Name Check
		**/
		$shortName = common_generateShortName($_POST[$data->output['sideBarForm']->formPrefix.'name']);
		// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
		$data->output['sideBarForm']->sendArray[':shortName'] = $_POST[$data->output['sideBarForm']->formPrefix.'name'] = $shortName;
		// Load All Existing SideBar ShortNames For Comparison
		$statement = $db->prepare('getExistingShortNames','admin_sideBars');
		$statement->execute();
		$sideBarList = $statement->fetchAll();
		$existingShortNames = array();
		foreach($sideBarList as $sideBarItem)
		{
			$existingShortNames[] = $sideBarItem['shortName'];
		}
		$data->output['sideBarForm']->fields['name']['cannotEqual'] = $existingShortNames;
		/*----------------*/
		if($data->output['sideBarForm']->validateFromPost())
		{
			//--Parsing--//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				$data->output['sideBarForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['sideBarForm']->sendArray[':rawContent']);
			} else {
				$data->output['sideBarForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['sideBarForm']->sendArray[':rawContent']);
			}
			// Save To DB
			$statement = $db->prepare('insertSideBar','admin_sideBars');
			$result = $statement->execute($data->output['sideBarForm']->sendArray);
			$sideBarId = $db->lastInsertId();	
			
			if($result == FALSE)
			{
				$data->output['error'] = TRUE;
				return;
			}
			// Generate Settings For Pages, Forms, and Modules
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
		
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$data->linkRoot.'admin/sideBars/add/">
						Add New SideBar
					</a>
					<a href="'.$data->linkRoot.'admin/sideBars/list/">
						Return to SideBar List
					</a>
				</div>';
		} else {
			// Throw Form Error //
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}

function admin_sideBarsShow($data)
{
	if(isset($data->output['error']) && $data->output['error'] === TRUE)
	{
		echo 'There was an error in saving your sidebar at this time.';
	} 
	else if(isset($data->output['savedOkMessage']))
	{
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['sideBarForm']);
	}
}

?>