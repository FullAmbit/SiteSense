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

function admin_dynamicFormsBuild($data,$db) {
	//permission check for forms edit
	if(!checkPermission('edit','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFieldById','admin_dynamicForms');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['fieldItem'] = $statement->fetch();
	if($data->output['fieldItem']  === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Field Doesn\'t Exist</h2>';
		return;
	}
	// Get Options
	$statement = $db->prepare('getOptionsByFieldId','admin_dynamicForms');
	$statement->execute(array(':fieldId' => $data->output['fieldItem']['id']));
	$optionsSerialized = $statement->fetch();
	$data->output['optionList'] = unserialize($optionsSerialized[0]);
	
	// Does Our Option Exist?
	if(!isset($data->output['optionList'][$data->action[4]]))
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Option Not Found</h2>';
		return;
	}
	
	$form = $data->output['fromForm'] = new formHandler('formoptions',$data,true);
	
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		$form->caption = 'New Option';
		$form->populateFromPostData();
		
		if ($form->validateFromPost()) {
			
			$form->sendArray[':fieldId'] = $data->output['fieldItem']['id'];
			$data->output['optionList'][$data->action[4]]['text'] = $form->sendArray[':text'];
			$data->output['optionList'][$data->action[4]]['value'] = $form->sendArray[':value'];
			
			$form->sendArray[':options'] = serialize($data->output['optionList']);
			unset($form->sendArray[':text'],$form->sendArray[':value']);
			
			$statement = $db->prepare('updateOptions','admin_dynamicForms');
			$statement->execute($form->sendArray) or die($statement->errorInfo());
			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>Option Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addoption/' . $data->output['fieldItem']['id'] . '">
							Add New Option
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listoptions/' . $data->output['fieldItem']['id'] . '">
							Return to Options List
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}
function admin_dynamicFormsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['fromForm']);
	}
}
?>
