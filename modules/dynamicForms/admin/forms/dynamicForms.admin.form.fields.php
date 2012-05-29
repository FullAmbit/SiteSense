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
$this->caption='Create/Edit Custom Form';
$this->submitTitle='Save';
$this->fields=array(
	'name' => array(
		'label' => 'Name',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['field']['name']) ? $data->output['field']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Name</b> - What is the field called?
			</p>
		'
	),
	'description' => array(
		'label' => 'Description',
		'tag' => 'input',
		'params' => array(
			'type' => 'text'
		),
		'description' => 'This is the text that will go in this popup here. It is not required.',
		'required' => false
	),
	'type' => array(
		'label' => 'Type',
		'tag' => 'select',
		'options' => array(
			'textbox', 'textarea', 'checkbox', 'select'
		),
		'value' => isset($data->output['field']['type']) ? $data->output['field']['type'] : '',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<b>Type</b> - What is the field?
			</p>
		'
	),
	'apiFieldToMapTo' => array(
		'label' => 'API Field',
		'required' => false,
		'tag' => 'input',
		'value' => isset($data->output['field']['apiFieldToMapTo']) ? $data->output['field']['apiFieldToMapTo'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		)
	),
	'required' => array(
		'label' => 'Required',
		'tag' => 'input',
		'value' => '1',
		'checked' => (isset($data->output['field']['required']) && $data->output['field']['required'] == '0') ? '' : 'checked',
		'params' => array(
			'type' => 'checkbox',
		)
	),
	'enabled' => array(
		'label' => 'Enabled',
		'tag' => 'input',
		'value' => 1,
		'checked' => (isset($data->output['field']['enabled']) && $data->output['field']['enabled'] == '0') ? '' : 'checked',
		'params' => array(
			'type' => 'checkbox',
		)
	),
	'isEmail' => array(
		'label' => 'E-Mail Validaton',
		'tag' => 'input',
		'value' => 1,
		'checked' => (isset($data->output['field']['isEmail']) && $data->output['field']['isEmail'] == '0') ? '' : 'checked',
		'params' => array(
			'type' => 'checkbox'
		)
	)
);
