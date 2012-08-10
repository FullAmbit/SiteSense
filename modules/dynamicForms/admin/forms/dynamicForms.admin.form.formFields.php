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
$this->caption=$data->phrases['dynamic-forms']['captionFormFieldsAddField'];
$this->submitTitle=$data->phrases['dynamic-forms']['submitFormFields'];
$this->fields=array(
	'name' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsName'],
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['field']['name']) ? $data->output['field']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormFieldsName'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormFieldsName'].'
			</p>
		'
	),
	'description' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsDescription'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormFieldsDescription'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormFieldsDescription'].'
			</p>
		',
		'required' => false
	),
	'type' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsType'],
		'tag' => 'select',
		'options' => array(
			'textbox', 'textarea', 'checkbox', 'select' , 'timezone', 'password'
		),
		'value' => isset($data->output['field']['type']) ? $data->output['field']['type'] : '',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormFieldsType'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormFieldsType'].'
			</p>
		'
	),
	'apiFieldToMapTo' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsAPIFieldToMapTo'],
		'required' => false,
		'tag' => 'input',
		'value' => isset($data->output['field']['apiFieldToMapTo']) ? $data->output['field']['apiFieldToMapTo'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		)
	),
	'required' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsRequired'],
		'tag' => 'input',
		'value' => '1',
		'checked' => (isset($data->output['field']['required']) && $data->output['field']['required'] == '0') ? '' : 'checked',
		'params' => array(
			'type' => 'checkbox',
		)
	),
	'enabled' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsEnabled'],
		'tag' => 'input',
		'value' => 1,
		'checked' => (isset($data->output['field']['enabled']) && $data->output['field']['enabled'] == '0') ? '' : 'checked',
		'params' => array(
			'type' => 'checkbox',
		)
	),
	'isEmail' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsIsEmail'],
		'tag' => 'input',
		'value' => 1,
		'params' => array(
			'type' => 'checkbox',
			'checked' => (isset($data->output['field']['isEmail']) && $data->output['field']['isEmail'] == '1') ? 'checked' : '',
		)
	),
	'compareTo' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsCompareTo'],
		'tag' => 'select',
		'options' => $data->output['fieldList'],
		'value' => isset($data->output['field']['compareTo']) ? $data->output['field']['compareTo'] : '',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormFieldsCompareTo'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormFieldsCompareTo'].'
			</p>
		'
	),
	'moduleHook' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormFieldsModuleHook'],
		'tag' => 'select',
		'options' => array(
			array(
				'value' => NULL,
				'text' => 'No Hook'
			)
		),
		'value' => isset($data->output['field']['moduleHook']) ? $data->output['field']['moduleHook'] : '',
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormFieldsModuleHook'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormFieldsModuleHook'].'
			</p>
		'
	)
);
foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
	$this->fields['moduleHook']['options'][] = array(
		'text' => $moduleName,
		'value' => $moduleShortName
	);
}
