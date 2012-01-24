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
	// Check If Form Exists //	
	$formId = $data->action[3];
	$check = $db->prepare('getFormById', 'form'); 
	$check->execute(array(':id' => $formId));
	if(($data->output['formItem'] = $check->fetch()) === FALSE)
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>ID does not exist in database</h2>';
			return;
	}

	$form = $data->output['formForm'] = new formHandler('forms',$data,true);
	// Load List Of Plugins
	$statement = $db->prepare('getEnabledPlugins','plugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();
	
	foreach($pluginList as $pluginItem)
	{
		$option['text'] = $pluginItem['name'];
		$option['value'] = $pluginItem['name'];
			
		$data->output['formForm']->fields['api']['options'][] = $option;
	}
	// Handle Form Submission //
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm))
	{
		$data->output['formForm']->populateFromPostData();
		/**
		 * Set Up Short Name Check (ONLY if different from currently existing
		**/
		$shortName = common_generateShortName($_POST[$data->output['formForm']->formPrefix.'name']);
		$data->output['formForm']->sendArray[':shortName'] = $shortName;
		
		if($shortName == $data->output['formItem']['shortName'])
		{
			unset($data->output['formForm']->fields['name']['cannotEqual']);
		} else {
			// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
			$data->output['formForm']->sendArray[':shortName'] = $_POST[$data->output['formForm']->formPrefix.'name'] = $shortName;
			// Load All Existing SideBar ShortNames For Comparison
			$statement = $db->prepare('getExistingShortNames','form');
			$statement->execute();
			$formList = $statement->fetchAll();
			$existingShortNames = array();
			foreach($formList as $formItem)
			{
				$existingShortNames[] = $formItem['shortName'];
			}
			$data->output['formForm']->fields['name']['cannotEqual'] = $existingShortNames;
		}
		// Validate All Form Fields
		if ($data->output['formForm']->validateFromPost()) {
			// Save Menu Item //
			if($data->output['formForm']->sendArray[':showOnMenu'] == 1)
			{
				$rowCount = $db->countRows('main_menu');
				$saveMenuItem = $db->prepare('saveMenuItem','form');
				$saveMenuItem->execute(array(
					'name' => $data->output['formForm']->sendArray[':name'],
					'title' => $data->output['formForm']->sendArray[':menuTitle'],
					'shortName' => $data->output['formForm']->sendArray[':shortName'],
					'side' => 'left',
					'enabled' => $data->output['formForm']->sendArray[':enabled'],
					'module' => 'forms',
					'sortOrder' => $rowCount + 1
				));
			}
			unset($data->output['formForm']->sendArray[':menuTitle'],$data->output['formForm']->sendArray[':showOnMenu']);
			//----Parse---//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				
				$data->output['formForm']->sendArray[':parsedContentBefore'] = $data->plugins['bbcode']->parse($data->output['formForm']->sendArray[':rawContentBefore']);
				$data->output['formForm']->sendArray[':parsedContentAfter'] = $data->plugins['bbcode']->parse($data->output['formForm']->sendArray[':rawContentAfter']);
				$data->output['formForm']->sendArray[':parsedSuccessMessage'] = $data->plugins['bbcode']->parse($data->output['formForm']->sendArray[':rawSuccessMessage']);
			} else {
				$data->output['formForm']->sendArray[':parsedContentBefore'] = htmlspecialchars($data->output['formForm']->sendArray[':rawContentBefore']);
				$data->output['formForm']->sendArray[':parsedContentAfter'] = htmlspecialchars($data->output['formForm']->sendArray[':rawContentAfter']);
				$data->output['formForm']->sendArray[':parsedSuccessMessage'] = htmlspecialchars($data->output['formForm']->sendArray[':rawSuccessMessage']);
			}
			//------------//
			// Save To DB //
			$statement = $db->prepare('editForm', 'form');
			
			$data->output['formForm']->sendArray[':id'] = $formId;
			$statement->execute($data->output['formForm']->sendArray);
			
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
		theme_buildForm($data->output['formForm']);
	}
}
?>
