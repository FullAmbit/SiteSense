<?php

function page_getUniqueSettings($data) {
	$data->output['pageShortName']='forms';
}

function page_buildContent($data,$db) {
	require_once('libraries/forms.php');
	require_once($data->themeDir.'formGenerator.template.php');
	$form = false;
	if ($data->action[1] !== false){
		$statement=$db->prepare('getFormByShortName','customform');
		$statement->execute(array(
			':shortName' => $data->action[1]
		));
		$form=$statement->fetch();
	}
	if($form === false){
		$data->action['error'] = 'notFound';
		return;
	}
	$data->output['form'] = $form;
	if($form['requireLogin'] == 1 && !isset($data->user['id'])){
		$data->action['error'] = 'accessDenied';
		return;
	}
	$statement = $db->prepare('getFieldsByForm', 'customform');
	$statement->execute(array(':form' => $form['id']));
	$rawFields = $statement->fetchAll();
	$rawForm = array();
	foreach($rawFields as $field){
		$f = array(
			'name' => $field['id'],
			'label' => $field['name'],
			'required' => true,
		);
		switch($field['type']){
			case 'textbox':
				$f['tag'] = 'input';
				$f['params'] = array('type' => 'text');
				break;
			case 'textarea':
				$f['tag'] = 'textarea';
				break;
		}
		$rawForm[$f['name']] = $f;
	}
	$customForm = $data->output['customform'] = new customFormHandler($rawForm, $form['shortName'], $form['name'], $data, false);
	if (isset($_POST['fromForm']) && ($_POST['fromForm'] == $customForm->fromForm)){
		$customForm->populateFromPostData();
		if ($customForm->validateFromPost()) {
			$newRow = $db->prepare('newRow', 'customform');
			$newRow->execute(array(':form' => $form['id']));
			$rowId = $db->lastInsertId();
			$statement = $db->prepare('newValue', 'customform');
			foreach($rawFields as $field){
				$fieldId = $field['id'];
				$statement->execute(array('row' => $rowId, 'field' => $fieldId, 'value' => $customForm->sendArray[':'.$fieldId]));
			}
			$data->output['success'] = $form['successMessage'];
		}
	}
}

function page_content($data) {
	if(isset($data->action['error'])){
		switch($data->action['error']){	
			case 'notFound':
				echo "Not Found";
				return;
				break;
			case 'accessDenied':
				echo "Access Denied";
				return;
		}
	}else if(isset($data->output['success'])){
		echo $data->output['success'];
	}else{
		theme_buildForm($data->output['customform']);
	}
}

?>