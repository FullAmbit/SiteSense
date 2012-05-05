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
function page_getUniqueSettings($data) {
	$data->output['pageShortName']='forms';
}
function page_buildContent($data,$db) {
	require_once('libraries/forms.php');
	require_once($data->themeDir.'formGenerator.template.php');
	$form = false;
	if ($data->action[1] !== false){
		$statement=$db->prepare('getFormByShortName','form');
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
	// Load SideBars
	$statement = $db->prepare('getEnabledSideBarsByForm','form');
	$statement->execute(array(':formId' => $form['id']));
	$sideBars = $statement->fetchAll();
	$data->sideBarList = array();
	foreach($sideBars as $sideBar)
	{
		$data->sideBarList[$sideBar['side']][] = $sideBar;
	}
	// Process Fields //
	$statement = $db->prepare('getFieldsByForm', 'form');
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
		}
		$rawForm[$f['name']] = $f;
	}
	$customForm = $data->output['customform'] = new customFormHandler($rawForm, $form['shortName'], '', $data, false,$data->action[1]);
	$customForm->submitTitle = $data->output['form']['submitTitle'];
	$customForm->caption = $data->output['form']['name'];
	if (isset($_POST['fromForm']) && ($_POST['fromForm'] == $customForm->fromForm)){
		$customForm->populateFromPostData();
		if ($customForm->validateFromPost()) {
			$newRow = $db->prepare('newRow', 'form');
			$newRow->execute(array(':form' => $form['id']));
			$rowId = $db->lastInsertId();
			$statement = $db->prepare('newValue', 'form');
			$emailText = '';
			foreach($rawFields as $field){
				$fieldId = $field['id'];
				$statement->execute(array('row' => $rowId, 'field' => $fieldId, 'value' => $customForm->sendArray[':'.$fieldId]));
				$emailText .= $field['name'] . ': ' . $customForm->sendArray[':'.$fieldId] . "\n";
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
			// API Hook
			if(isset($form['api']{1}) && $form['api'] !== NULL)
			{
				if(!is_object($data->plugins[$form['api']]))
				{
					common_loadPlugin($data,$form['api']);
				}
				if(method_exists($data->plugins[$form['api']],'runFromCustomForm'))
				{
					$data->plugins[$form['api']]->runFromCustomForm($data,$db,$processedFields,$customForm->sendArray);
				}
			}
			// Are We E-Mailing This?
			if(isset($form['eMail']{0}))
			{
				$subject = $form['name'] . ' - Form Data';	
				$from = 'no-reply@fullambit.com';
				$header='From: '. $from . "\r\n";
				$recepients = explode(',',$form['eMail']);
				foreach($recepients as $index => $to)
				{
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
		theme_buildForm($data->output['customform']);
		echo htmlspecialchars_decode($data->output['form']['parsedContentAfter']);
		theme_contentBoxFooter();
	}
}

function loadPermissions($data) {
    $data->permissions['forms']=array(
        'admin' => 'User has form admin rights',
        'canDeleteFormField' => 'User can delete form fields',
        'canDeleteFormOption' => 'User can delete form options'
    );
}
?>