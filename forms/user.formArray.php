<?php

$this->caption='Editing Your Account';
$this->submitTitle='Save Changes';

$this->fields=array(
	'fullName' => array(
		'label' => 'Full Name',
		'required' => true,
		'tag' => 'input',
		'value' => $data->user['fullName'],
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Full Name</b>
			</p>
		'
	),
	'contactEMail' => array(
		'label' => 'Contact E-Mail',
		'tag' => 'input',
		'value' => $data->user['contactEMail'],
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
		'value' => $data->user['publicEMail'],
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Public E-Mail</b> - E-mail shown to the public on your profile.
			</p>
		'
	),
	'password' => array(
		'label' => 'Change Password',
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
		'
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