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
function page_buildContent($data,$db) {
	require_once('libraries/forms.php');
	$form = false;
	if ($data->action[1] !== false){
		$statement=$db->prepare('getFormByShortName','dynamicForms');
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
	$data->output['pageTitle']=$data->output['form']['name'];
	if($form['requireLogin'] == 1 && !isset($data->user['id'])){
		$data->action['error'] = 'accessDenied';
		return;
	}
	// Load Sidebars
	$statement = $db->prepare('getEnabledSidebarsByForm','dynamicForms');
	$statement->execute(array(':formId' => $form['id']));
	$sidebars = $statement->fetchAll();
	$data->sidebarList = array();
	foreach($sidebars as $sidebar)
	{
		$data->sidebarList[$sidebar['side']][] = $sidebar;
	}
	// Process Fields //
	$statement = $db->prepare('getFieldsByForm', 'dynamicForms');
	$statement->execute(array(':form' => $form['id']));
	$rawFields = $statement->fetchAll();
	$rawForm = array();
	foreach($rawFields as $field){
		if($field['enabled'] !== '1')
		{
			continue;
		}
		$f = array(
			'name' => $field['id'],
			'label' => $field['name'],
			'required' => true,
		);
		switch($field['type']){
			case 'textbox':
				$f['tag'] = 'input';
				$f['params'] = array('type' => 'text');
				$f['required'] = ($field['required'] == '0') ? false : true;
				$f['validate'] = ($field['isEmail'] == '1') ? 'eMail' : '';
				$f['eMailFailMessage'] = 'Invalid E-Mail Address';
				break;
			case 'textarea':
				$f['tag'] = 'textarea';
				$f['required'] = ($field['required'] == '0') ? false : true;
				$f['params'] = array('cols' => 40,'rows' => 12);
				$f['validate'] = ($field['isEmail'] == '1') ? 'eMail' : '';
				break;
			case 'checkbox':
				$f['tag'] = 'input';
				$f['params'] = array('type' => 'checkbox');
				$f['required'] = ($field['required'] == '0') ? false : true;
				$f['validate'] = ($field['isEmail'] == '1') ? 'eMail' : '';
				break;
			case 'select':
				$f['tag'] = 'select';
				$f['required'] = ($field['required'] == '0') ? false : true;
				$f['options'] = unserialize($field['options']);
				$f['validate'] = ($field['isEmail'] == '1') ? 'eMail' : '';
			case 'timezone':
				$f['tag'] = 'select';
				$f['required'] = ($field['required'] == '0') ? false : true;
				$f['type'] = 'timezone';
				break;
			case 'password':
				$f['tag'] = 'input';
				$f['params'] = array('type' => 'password');
				$f['required'] = ($field['required'] == '0') ? false : true;
		}
		$rawForm[$f['name']] = $f;
	}
	
	$moduleList = array_flip($data->output['moduleShortName']);
	$hookedModules = array();
	$data->output['customForm'] = new customFormHandler($rawForm, $form['shortName'], '', $data, false,$data->action[1]);
	$data->output['customForm']->submitTitle = $data->output['form']['submitTitle'];
	$data->output['customForm']->caption = $data->output['form']['name'];
	if(isset($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['customForm']->fromForm)){
		$data->output['customForm']->populateFromPostData();
		// Validate Form
		if($data->output['customForm']->validateFromPost()) {
			// Validate Using Module Hooks
			foreach($rawFields as $field){
				$fieldId = $field['id'];
				$fieldValue = $data->output['customForm']->sendArray[':'.$fieldId];
				// Is This Field Hooked And Is The Module Enabled?
				if($field['moduleHook'] !== NULL && isset($moduleList[$field['moduleHook']])){
					$moduleName = $moduleList[$field['moduleHook']];
					$hookedModules[] = $moduleName;
					$target = 'modules/'.$moduleName.'/'.$moduleName.'.dynamicForms.php';
					common_include($target);
					// Check To See What Function We Can Run
					$fieldCamelCase = common_camelBack($field['name']);
					$fieldFunction = $moduleName.'_validate'.$fieldCamelCase;
					$generalFunction = $moduleName.'_validateDynamicFormField';
					if(function_exists($fieldFunction)){
						$fieldFunction($data,$db,$field,$fieldValue);
					}else{
						$generalFunction($data,$db,$field,$fieldValue);
					}
				}
			}
		}
		// No Errors...Start Saving...
		if($data->output['customForm']->error==FALSE){
			$statement = $db->prepare('newValue', 'dynamicForms');
			$emailText = '';
			foreach($rawFields as $field){
				$fieldId = $field['id'];
				$fieldValue = $data->output['customForm']->sendArray[':'.$fieldId];
				// Is This Field Hooked And Is The Module Enabled?
				if($field['moduleHook'] !== NULL && isset($moduleList[$field['moduleHook']])){
					$moduleName = $moduleList[$field['moduleHook']];
					// Check To See What Function We Can Run
					$fieldCamelCase = common_camelBack($field['name']);
					$fieldFunction = $moduleName.'_save'.$fieldCamelCase;
					$generalFunction = $moduleName.'_saveDynamicFormField';
					if(function_exists($fieldFunction)){
						$fieldFunction($data,$db,$fieldCamelCase,$fieldValue);
					} else {
						$generalFunction($data,$db,$fieldCamelCase,$fieldValue);
					}					
				} else {
					// Do We Have A Row Yet For This New Custom Form Data?
					if(!isset($rowId)){
						$newRow = $db->prepare('newRow', 'dynamicForms');
						$newRow->execute(array(':form' => $form['id']));
						$rowId = $db->lastInsertId();
					}
					$statement->execute(array('row' => $rowId, 'field' => $fieldId, 'value' => $fieldValue));
					$emailText .= $field['name'] . ': ' . $data->output['customForm']->sendArray[':'.$fieldId] . "\n";
					/**
					// If the user wanted to be subscribed
					if($field['isNewsletterSignup'] == 1)
					{
						echo "WE GOT ONE";
						// API Call To E-Mail Service
						$wrap = new CS_REST_Subscribers('c92ba413fde76b3823c1cc9726a72f15', 'bd0fce03de47562ece19f1b2b1106ebd');
						$result = $wrap->add(array(
							'EmailAddress' => $data->output['commentForm']->sendArray[':email'],
							'Name' => $data->output['commentForm']->sendArray[':commenter'],
							'Resubscribe' => true
						));
					}
					**/
					$processedFields[$fieldId] = $field;
				}
			}
			// Call Hooks After Processing / Saving Form Fields
			foreach($hookedModules as $moduleName){
				$function = $moduleName.'_afterForm';
				if(function_exists($function)){
					$function($data,$db);
				}
			}
			// API Hook
			if(isset($form['api']{1}) && $form['api'] !== NULL) {
				common_loadPlugin($data,$form['api']);
				if(method_exists($data->plugins[$form['api']],'runFromCustomForm')) {
					$data->plugins[$form['api']]->runFromCustomForm($processedFields,$data->output['customForm']->sendArray);
				}
			}
			// Are We E-Mailing This?
			if(isset($form['eMail']{0})){
				$subject = $form['name'] . ' - Form Data';	
				$from = 'no-reply@fullambit.com';
				$header='From: '. $from . "\r\n";
				$recepients = explode(',',$form['eMail']);
				foreach($recepients as $index => $to){
					mail($to,$subject,wordwrap($emailText,70),$header);
				}
			}
			$data->output['success'] = htmlspecialchars_decode($form['parsedSuccessMessage']);
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
		theme_contentBoxHeader($data->output['form']['name']);
		echo $data->output['success'];
		theme_contentBoxFooter();
	}else{
		theme_contentBoxHeader($data->output['form']['name']);
		echo htmlspecialchars_decode($data->output['form']['parsedContentBefore']);
		$data->output['customForm']->build();
		echo htmlspecialchars_decode($data->output['form']['parsedContentAfter']);
		theme_contentBoxFooter();
	}
}
?>