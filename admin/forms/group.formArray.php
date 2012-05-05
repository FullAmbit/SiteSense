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
global $languageText;

$this->formPrefix='viewUser_';
$this->caption='Editing Group: '.(
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
	'fullName' => array(
		'label' => 'Full Name',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']['fullName']{0}) ? '' : $data->output['viewUser']['fullName']),
		'params' => array(
			'type' => 'text',
			'size' => 128,
		),
		'description' => '
			<p>
				<b>Full Name</b> - The full name of the user.
			</p>
		'
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