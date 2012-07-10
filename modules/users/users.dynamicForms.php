<?php

function users_validateDynamicFormField($data,$db,$field,$fieldValue){
	$userColumns = array(
		'name',
		'password',
		'firstName',
		'lastName',
		'contactEMail',
		'publicEMail',
		'timeZone'
	);
	
	$fieldRef =& $data->output['customForm']->fields[$field['id']];
	$formError =& $data->output['customForm']->error;
				
	$camelCaseName = common_camelBack($field['name']);
	if(in_array($camelCaseName,$userColumns)){
		switch($camelCaseName){
			case 'name':
				$username = $fieldValue;
				// Check If UserName Exists...
				if($data->getUserIdByName($username)) {
					$formError = true;
                	$fieldRef['error']=true;
                	$fieldRef['errorList'][]='Name already exists';
                }
			break;
			case 'password':
			break;
		}
	}
}

function users_saveDynamicFormField($data,$db,$fieldName,$fieldValue){
	if($fieldName == 'name'){
		// Initial Save...Create User Row
		$statement = $db->prepare('createUserRow','users');
		$r = $statement->execute(array(
			':name' => $fieldValue
		));
		return $r;
	}
	// Add Seperate Field Data
	$statement = $db->prepare('updateUserField','users');
	$r=$statement->execute(array(
		':userID' => $db->lastInsertId(),
		':fieldName' => $fieldName,
		':fieldValue' => $fieldValue
	));
	return $r;
}
?>