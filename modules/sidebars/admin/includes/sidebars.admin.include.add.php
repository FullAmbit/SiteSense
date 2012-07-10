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

function admin_sidebarsBuild($data,$db) {
    if(!checkPermission('add','sidebars',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	// Load Form
	$data->output['sidebarForm'] = new formHandler('sidebars',$data,true);
	
	if(!empty($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['sidebarForm']->fromForm)) {
		$data->output['sidebarForm']->populateFromPostData();
		/**
		 * Set up Short Name Check
		**/
		$shortName = common_generateShortName($_POST[$data->output['sidebarForm']->formPrefix.'name']);
		// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
		$data->output['sidebarForm']->sendArray[':shortName'] = $_POST[$data->output['sidebarForm']->formPrefix.'name'] = $shortName;
		// Load All Existing Sidebar ShortNames For Comparison
		$statement = $db->prepare('getExistingShortNames','admin_sidebars');
		$statement->execute();
		$sidebarList = $statement->fetchAll();
		$existingShortNames = array();
		foreach($sidebarList as $sidebarItem)
		{
			$existingShortNames[] = $sidebarItem['shortName'];
		}
		$data->output['sidebarForm']->fields['name']['cannotEqual'] = $existingShortNames;
		/*----------------*/
		if($data->output['sidebarForm']->validateFromPost())
		{
			//--Parsing--//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				$data->output['sidebarForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['sidebarForm']->sendArray[':rawContent']);
			} else {
				$data->output['sidebarForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['sidebarForm']->sendArray[':rawContent']);
			}
            $data->output['sidebarForm']->sendArray[':sortOrder']=admin_sortOrder_new($db,'sidebars');
			// Save To DB
			$statement = $db->prepare('insertSidebar','admin_sidebars');
			$result = $statement->execute($data->output['sidebarForm']->sendArray);
			$sidebarId = $db->lastInsertId();
			
			if($result == FALSE)
			{
				$data->output['error'] = TRUE;
				return;
			}
            $sortOrder=$data->output['sidebarForm']->sendArray[':sortOrder'];
			//---Pages---//
			$pageQ = $db->prepare('createSidebarSetting','admin_pages');
			$statement = $db->prepare('getAllPageIds','admin_pages');
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
			$moduleQ = $db->prepare('createSidebarSetting','admin_modules');
			$statement = $db->prepare('getAllModuleIds','admin_modules');
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
			$formQ = $db->prepare('createSidebarSetting','admin_dynamicForms');
			$statement = $db->prepare('getAllFormIds','admin_dynamicForms');
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
		
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$data->linkRoot.'admin/sidebars/add/">
						Add New Sidebar
					</a>
					<a href="'.$data->linkRoot.'admin/sidebars/list/">
						Return to Sidebar List
					</a>
				</div>';
		} else {
			// Throw Form Error //
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}

function admin_sidebarsShow($data)
{
	if(isset($data->output['error']) && $data->output['error'] === TRUE)
	{
		echo 'There was an error in saving your sidebar at this time.';
	} 
	else if(isset($data->output['savedOkMessage']))
	{
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['sidebarForm']);
	}
}

?>