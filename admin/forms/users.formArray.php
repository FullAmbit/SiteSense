<?php
global $languageText;
$this->action=$data->linkRoot.'admin/users/edit/'.(
	(is_numeric($data->action[3])) ?
	$data->action[3] : 'new'
);

$this->formPrefix='viewUser_';
$this->caption='Editing User: '.(
	empty($data->output['viewUser']) ? '' : $data->output['viewUser']['name']
);
$this->submitTitle='Save Changes';
$this->fromForm='viewUser';

$levelOptions = array();
foreach($languageText['userLevels'] as $value => $text){
	$levelOptions[] = array('value' => $value, 'text' => $text);
}
$this->fields=array(
	'id' => array(
		'label' => 'ID #',
		'tag' => 'span',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['id'])
	),
	'name' => array(
		'label' => 'Username',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['name']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Username</b> - The name the user logs in with. This is different from displayName.
			</p>
		'
	),
	'userLevel' => array(
		'label' => 'User Access Level',
		'tag' => 'select',
		'options' => $levelOptions,
		'value' => (empty($data->output['viewUser']) ? '0' : $data->output['viewUser']['userLevel']),
		'description' => '
			<p>
				<b>User Level</b> - Determines what the user can/cannot do on the system.
			</p>
		',
	),
	'registeredDate' => array(
		'label' => 'Registered on',
		'tag' => 'span',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['registeredDate']),
	),
	'registeredIP' => array(
		'label' => 'Registered From',
		'tag' => 'span',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['registeredIP']),
	),
	'lastAccess' => array(
		'label' => 'Last Access',
		'tag' => 'span',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['lastAccess']),
	),
	'contactEMail' => array(
		'label' => 'Contact E-Mail',
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['contactEMail']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Contact E-Mail</b> - E-mail Staff can use to contact user.
			</p>
		'
	),
	'publicEMail' => array(
		'label' => 'Public E-Mail',
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['publicEMail']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Public E-Mail</b> - E-mail shown to the public on the user\'s profile.
			</p>
		'
	),
	'password' => array(
		'label' => 'Password',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Password</b> - What the user logs in with for a password
			</p>
		',
	),
	'password2' => array(
		'label' => 'Retype Password',
		'compareTo' => 'password',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Retype Password</b> - Enter the new password a second time to verify changes.
			</p>
		',
		'compareFailMessage' => 'The passwords you entered do not match!'
	)
);
