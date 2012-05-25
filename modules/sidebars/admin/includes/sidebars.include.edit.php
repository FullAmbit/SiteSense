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
    if(!checkPermission('edit','sidebars',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	$aRoot=$data->linkRoot.'admin/sidebars/';
	
	// Check To See If Sidebar Exists
	$sidebarId = $data->action[3];

	$statement=$db->prepare('getById','admin_sidebars');
	$statement->execute(array(
		':id' => $sidebarId
	));
	$data->output['sidebarItem'] = $item = $statement->fetch();
	if($data->output['sidebarItem'] == FALSE)
	{
		$data->output['pagesError'] = 'unknown function';
		return;
	}
	// Load Form
	$data->output['sidebarForm']=new formHandler('sidebars',$data,true);
	// Populate With Data
	foreach ($data->output['sidebarForm']->fields as $key => $value) {
		if (
			(!empty($value['params']['type'])) &&
			($value['params']['type']=='checkbox')
		) {
			$data->output['sidebarForm']->fields[$key]['checked']=(
				$item[$key] ? 'checked' : ''
			);
		} else {
			
			$data->output['sidebarForm']->fields[$key]['value']=$item[$key];
		}
	}
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['sidebarForm']->fromForm))
	{
		$data->output['sidebarForm']->populateFromPostData();
		
		/**
		 * Set Up Short Name Check (ONLY if different from currently existing
		**/
		$shortName = common_generateShortName($_POST[$data->output['sidebarForm']->formPrefix.'name']);
		$data->output['sidebarForm']->sendArray[':shortName'] = $shortName;
		
		if($shortName == $data->output['sidebarItem']['shortName'])
		{
			unset($data->output['sidebarForm']->fields['name']['cannotEqual']);
		} else {
			// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
			$_POST[$data->output['sidebarForm']->formPrefix.'name'] = $shortName;
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
		}
		
		if ($data->output['sidebarForm']->validateFromPost()) {
			
			//--Parsing--//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				$data->output['sidebarForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['sidebarForm']->sendArray[':rawContent']);
			} else {
				$data->output['sidebarForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['sidebarForm']->sendArray[':rawContent']);
			}
			
			// Save TO DB
			$statement=$db->prepare('updateById','admin_sidebars');
			$data->output['sidebarForm']->sendArray[':id'] = $data->action[3];
			$statement->execute($data->output['sidebarForm']->sendArray);
				
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'edit/new">
						Add New Page
					</a>
					<a href="'.$aRoot.'list/">
						Return to Page List
					</a>
				</div>';
		} else {
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}
function admin_sidebarsShow($data) {
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['sidebarForm']);
	}
}
?>