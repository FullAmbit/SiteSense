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
function admin_formsBuild($data,$db)
{
	//permission check for forms edit
	if(!checkPermission('edit','admin_dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	// Check If Form Exists //	
	$formId = $data->action[3];
	$check = $db->prepare('getFormById', 'admin_dynamicForms');
	$check->execute(array(':id' => $formId));
	if(($data->output['formItem'] = $check->fetch()) === FALSE)
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>ID does not exist in database</h2>';
			return;
	}

	$form = $data->output['fromForm'] = new formHandler('forms',$data,true);
	// Load List Of Plugins
	$statement = $db->prepare('getEnabledPlugins','admin_plugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();
	
	foreach($pluginList as $pluginItem)
	{
		$option['text'] = $pluginItem['name'];
		$option['value'] = $pluginItem['name'];
			
		$data->output['fromForm']->fields['api']['options'][] = $option;
	}
	// Handle Form Submission //
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm))
	{
		$data->output['fromForm']->populateFromPostData();
		/**
		 * Set Up Short Name Check (ONLY if different from currently existing
		**/
		$shortName = common_generateShortName($_POST[$data->output['fromForm']->formPrefix.'name']);
		$data->output['fromForm']->sendArray[':shortName'] = $shortName;
		
		if($shortName == $data->output['formItem']['shortName'])
		{
			unset($data->output['fromForm']->fields['name']['cannotEqual']);
		} else {
			// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
			$data->output['fromForm']->sendArray[':shortName'] = $_POST[$data->output['fromForm']->formPrefix.'name'] = $shortName;
			// Load All Existing SideBar ShortNames For Comparison
			$statement = $db->prepare('getExistingShortNames','admin_dynamicForms');
			$statement->execute();
			$formList = $statement->fetchAll();
			$existingShortNames = array();
			foreach($formList as $formItem)
			{
				$existingShortNames[] = $formItem['shortName'];
			}
			$data->output['fromForm']->fields['name']['cannotEqual'] = $existingShortNames;
		}
		// Validate All Form Fields
		if ($data->output['fromForm']->validateFromPost()) {
			// Save Menu Item //
			if($data->output['fromForm']->sendArray[':showOnMenu'] == 1)
			{
				$rowCount = $db->countRows('main_menu');
				$saveMenuItem = $db->prepare('saveMenuItem','admin_dynamicForms');
				$saveMenuItem->execute(array(
					'name' => $data->output['fromForm']->sendArray[':name'],
					'title' => $data->output['fromForm']->sendArray[':menuTitle'],
					'shortName' => $data->output['fromForm']->sendArray[':shortName'],
					'side' => 'left',
					'enabled' => $data->output['fromForm']->sendArray[':enabled'],
					'module' => 'forms',
					'sortOrder' => $rowCount + 1
				));
			}
			unset($data->output['fromForm']->sendArray[':menuTitle'],$data->output['fromForm']->sendArray[':showOnMenu']);
			//----Parse---//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				
				$data->output['fromForm']->sendArray[':parsedContentBefore'] = $data->plugins['bbcode']->parse($data->output['fromForm']->sendArray[':rawContentBefore']);
				$data->output['fromForm']->sendArray[':parsedContentAfter'] = $data->plugins['bbcode']->parse($data->output['fromForm']->sendArray[':rawContentAfter']);
				$data->output['fromForm']->sendArray[':parsedSuccessMessage'] = $data->plugins['bbcode']->parse($data->output['fromForm']->sendArray[':rawSuccessMessage']);
			} else {
				$data->output['fromForm']->sendArray[':parsedContentBefore'] = htmlspecialchars($data->output['fromForm']->sendArray[':rawContentBefore']);
				$data->output['fromForm']->sendArray[':parsedContentAfter'] = htmlspecialchars($data->output['fromForm']->sendArray[':rawContentAfter']);
				$data->output['fromForm']->sendArray[':parsedSuccessMessage'] = htmlspecialchars($data->output['fromForm']->sendArray[':rawSuccessMessage']);
			}
			//------------//
			// Save To DB //
			$statement = $db->prepare('editForm', 'admin_dynamicForms');
			
			$data->output['fromForm']->sendArray[':id'] = $formId;
			$statement->execute($data->output['fromForm']->sendArray);
			
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>Form Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/forms/addform">
							Add New Form
						</a>
						<a href="'.$data->linkRoot.'admin/forms/list/">
							Return to Form List
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}
function admin_formsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['fromForm']);
	}
}
?>
