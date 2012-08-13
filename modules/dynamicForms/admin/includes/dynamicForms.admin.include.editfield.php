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
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}	
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFieldById','admin_dynamicForms');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['field'] = $field = $statement->fetch();
	if($field === false){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	$data->output['fieldList'][]=array(
		'text'  => 'Do Not Compare',
		'value' => '0'
	);
	$statement = $db->prepare('getFieldsByForm','admin_dynamicForms');
	$statement->execute(array(':form' => $field['form']));
	$fieldList = $statement->fetchAll();
	foreach($fieldList as $value) {
		if($value['id']!=$data->action[3]) {
			$data->output['fieldList'][]=array(
				'text'  => $value['name'],
				'value' => $value['id']
			);
		}
	}
	$statement = $db->prepare('getFormById','admin_dynamicForms');
	$statement->execute(array(':id' => $field['form']));
	$dbform = $statement->fetch();
	if($dbform === false){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	$data->output['form'] = $dbform;
	$form = $data->output['fromForm'] = new formHandler('formfields',$data,true);
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm))
	{
		$form->caption = 'New Custom Form';
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			if($form->sendArray[':isEmail'] && $form->sendArray[':type']!=='textbox') {
				$form->fields['isEmail']['error']=true;
				$form->fields['isEmail']['errorList'][]='Can only validate emails for textboxes.';
				return;
			}
			$form->sendArray[':id'] = $field['id'];
			$statement = $db->prepare('editField','admin_dynamicForms');
			$statement->execute($form->sendArray) or die(var_dump($statement->errorInfo()));
			
			// -- Push The Constant Fields Across Other Languages
			common_updateAcrossLanguageTables($data,$db,'form_fields',array('id'=>$field['id']),array(
				'type' => $data->output['fromForm']->sendArray[':type'],
				'apiFieldToMapTo' => $form->sendArray[':apiFieldToMapTo'],
				'required' =>  $form->sendArray[':required'],
				'enabled' =>  $form->sendArray[':enabled'],
				'isEmail' =>  $form->sendArray[':isEmail'],
				'compareTo' =>  $form->sendArray[':compareTo'],
				'moduleHook' =>  $form->sendArray[':moduleHook'],
			));
			
			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['dynamic-forms']['saveFieldSuccessHeading'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/newField/' . $data->output['form']['id'] . '">
							'.$data->phrases['dynamic-forms']['addField'].'
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listFields/' . $data->output['form']['id'] . '">
							'.$data->phrases['dynamic-forms']['returnToFields'].'
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
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
