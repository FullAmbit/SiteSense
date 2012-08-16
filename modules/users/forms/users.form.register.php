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

$this->submitTitle=$data->phrases['users']['joinNow'];
$this->fromForm='register';
$this->fields=array(
	'firstName' => array(
		'label' => $data->phrases['users']['firstName'],
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
		'label' => $data->phrases['users']['lastName'],
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
		'label' => $data->phrases['users']['desiredUsername'],
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
		'label' => $data->phrases['users']['contactEmail1'],
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
		'eMailFailMessage' => $data->phrases['users']['invalidEmail']
	),
	'password' => array(
		'label' => $data->phrases['users']['password1'],
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
		'label' => $data->phrases['users']['retypePassword1'],
		'compareTo' => 'password',
		'tag' => 'input',
		'value' => '',
		'required' => true,
		'params' => array(
			'type' => 'password',
			'size' => 64,
			'maxlength' => 128
		),
		'compareFailMessage' => $data->phrases['users']['passwordsDoNotMatch']
	),
    'timeZone' => array(
        'label' => $data->phrases['users']['timeZone'],
        'required' => true,
        'tag' => 'select',
        'value' => (empty($data->output['userForm']['timeZone']) ? $data->settings['defaultTimeZone'] : $data->output['userForm']['timeZone']),
        'options' => $data->output['timeZones']
    )
);
$this->extraMarkup.='
	<p>
		'.$data->phrases['users']['registrationText'].'<a href="'.$data->linkRoot.'Registration_Agreement">'.$data->phrases['users']['registrationAgreement'].'</a>.
	</p>
';
?>