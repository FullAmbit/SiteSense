<?php

$this->action=$data->linkRoot.'register';

$this->formPrefix='register_';
$this->caption='Become Part of Where Future Stars Come Frumm...<b></b>';
$this->submitTitle='Join Now';
$this->fromForm='register';

$this->fields=array(
	'fullName' => array(
		'label' => 'Your Name',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['fullName']),
		'params' => array(
			'type' => 'text',
			'size' => 64,
			'maxlength' => 128
		)
	),
	'name' => array(
		'label' => 'Desired Username',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['name']),
		'params' => array(
			'type' => 'text',
			'size' => 64,
			'maxlength' => 64,
		),
	),
	'contactEMail' => array(
		'label' => 'Contact E-Mail',
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['contactEMail']),
		'required' => true,
		'validate' => 'eMail',
		'params' => array(
			'type' => 'text',
			'size' => 64,
			'maxlength' => 255
		),
		'eMailFailMessage' => 'Invalid E-Mail Address'
	),
	'verifyEMail' => array(
		'label' => 'Retype E-Mail',
		'compareTo' => 'contactEMail',
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['publicEMail']),
		'validate' => 'eMail',
		'required' => true,
		'params' => array(
			'type' => 'text',
			'size' => 64,
			'maxlength' => 255
		),
		'compareFailMessage' => 'The E-Mails you entered do not match!',
		'eMailFailMessage' => 'Invalid E-Mail Address'
	),
	'password' => array(
		'label' => 'Password',
		'tag' => 'input',
		'value' => '',
		'required' => true,
		'params' => array(
			'type' => 'password',
			'size' => 64,
			'maxlength' => 128
		)
	),
	'password2' => array(
		'label' => 'Retype Password',
		'compareTo' => 'password',
		'tag' => 'input',
		'value' => '',
		'required' => true,
		'params' => array(
			'type' => 'password',
			'size' => 64,
			'maxlength' => 128
		),
		'compareFailMessage' => 'The passwords you entered do not match!'	)
);

$this->extraMarkup.='
	<p>
		By clicking on the "Join Now" button above you are stating that you accept our <a href="'.$data->linkRoot.'Registration_Agreement">registration agreement</a>.
	</p>
';