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
$this->action = $data->localRoot . '/' . $data->output['newsList'][0]['shortName'] . '/#commentSuccess';
$this->formPrefix='comment_';
$this->caption='Add Your Comment';
$this->submitTitle='Add My Comment';
$this->fromForm='commentForm';
$this->fields=array(
	'author' => array(
		'label' => 'Your Name',
		'required' => true,
		'value' => isset($data->user['fullName']) ? $data->user['fullName'] : '',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
		'description' => '
			<p>
				<b>Your Name</b> - Who are you?
			</p>
		'
	),
	'post' => array(
		'params' => array(
			'type' => 'hidden'
		)
	),
	'loggedIP' => array(
		'value' => $_SERVER['REMOTE_ADDR'],
		'params' => array(
			'type' => 'hidden'
		)
	),
	'email' => array(
		'label' => 'Your Email Address',
		'required' => true,
		'value' => '',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '<p><b>Your E-Mail Address</b></p>',
		'validate' => 'eMail'
	),
	'rawContent' => array(
		'label' => 'Your Comment',
		'required' => true,
		'tag' => 'textarea',
		'params' => array(
			'cols' => 80,
			'rows' => 20
		)
	)
);