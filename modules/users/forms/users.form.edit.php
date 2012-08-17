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
$this->caption='Editing Your Account';
$this->submitTitle='Save Changes';
$this->fields=array(
	'firstName' => array(
		'label' => $data->phrases['users']['firstName'],
		'required' => true,
		'tag' => 'input',
		'value' => (isset($data->user['firstName'])) ? $data->user['firstName'] : NULL,
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>',$data->phrases['users']['fullName'],'</b>
			</p>
		'
	),
	'lastName' => array(
		'label' => 'Last Name',
		'required' => true,
		'tag' => 'input',
		'value' => (isset($data->user['lastName'])) ? $data->user['lastName'] : NULL,
		'params' => array(
			'type' => 'text',
			'size' => 128
		)
	),
	'contactEMail' => array(
		'label' => $data->phrases['users']['contactEmail'],
		'tag' => 'input',
		'value' => (isset($data->user['contactEmail1'])) ? $data->user['contactEMail'] : NULL,
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>',$data->phrases['users']['contactEmail'],'</b>',$data->phrases['users']['contactEmail2'],'
			</p>
		'
	),
	'publicEMail' => array(
		'label' => $data->phrases['users']['publicEmail'],
		'tag' => 'input',
		'value' => (isset($data->user['publicEmail'])) ? $data->user['publicEMail'] : NULL,
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>',$data->phrases['users']['publicEmail1'],'</b>',$data->phrases['users']['publicEmail2'],'
			</p>
		'
	),
	'password' => array(
		'label' => $data->phrases['users']['changePassword'],
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>',$data->phrases['users']['password1'],'</b>',$data->phrases['users']['password2'],'
			</p>
		'
	),
	'password2' => array(
		'label' => $data->phrases['users']['retypePassword1'],
		'compareTo' => 'password',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>',$data->phrases['users']['retypePassword1'],'</b>',$data->phrases['users']['retypePassword2'],'
			</p>
		',
		'compareFailMessage' => $data->phrases['users']['passwordsDoNotMatch']
	)
);