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
	$formId = $data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFormById','admin_dynamicForms');
	$statement->execute(array(':id' => $data->action[3]));
	$dbform = $statement->fetch();
	if($dbform === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Form Doesn\'t Exist</h2>';
		return;
	}
	$data->output['form'] = $dbform;
	$data->output['fieldList'][]=array(
		'text'  => 'Do Not Compare',
		'value' => '0'
	);
	$statement = $db->prepare('getFieldsByForm','admin_dynamicForms');
	$statement->execute(array(':form' => $data->action[3]));
	$fieldList = $statement->fetchAll();
	foreach($fieldList as $field) {
		$data->output['fieldList'][]=array(
			'text'  => $field['name'],
			'value' => $field['id']
		);
	}
	$form = $data->output['fromForm'] = new formHandler('formfields',$data,true);
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm)) {
		$form->caption = 'New Form Field';
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			if($form->sendArray[':isEmail'] && $form->sendArray[':type']!=='textbox') {
				$form->fields['isEmail']['error']=true;
				$form->fields['isEmail']['errorList'][]='Can only validate emails for textboxes.';
				return;
			}
			$form->sendArray[':form'] = $dbform['id'];
			
			//--Get SortOrder--//
			$statement = $db->prepare('countFieldsByForm','admin_dynamicForms');
			$statement->execute(array(':formId' => $formId));
			list($rowCount) = $statement->fetch();
			$sortOrder = $rowCount + 1;
			$form->sendArray[':sortOrder'] = $sortOrder;
			
			
			$statement = $db->prepare('newField','admin_dynamicForms');
			$result = $statement->execute($form->sendArray);
			if(!$result) {
				$data->output['abort'] = true;
				$data->output['abortMessage'] = '<h2>Unable to save to database</h2>';
				return;
			}
			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>Field Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/newField/' . $data->output['form']['id'] . '">
							Add New Field
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listFields/' . $data->output['form']['id'] . '">
							Return to Field List
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
