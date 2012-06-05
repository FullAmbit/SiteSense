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
$this->action=$data->linkRoot.'users/register/';
$this->formPrefix='register_';
$this->caption='Become Part of Where Future Stars Come Frumm...<b></b>';
$this->submitTitle='Join Now';
$this->fromForm='register';
$this->fields=array(
	'firstName' => array(
		'label' => 'First Name',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['firstName']),
		'params' => array(
			'type' => 'text',
			'size' => 64,
			'maxlength' => 128
		)
	),
	'lastName' => array(
		'label' => 'Last Name',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']) ? '' : $data->output['viewUser']['lastName']),
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