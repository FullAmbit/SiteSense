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
$this->caption=$data->phrases['dynamic-forms']['captionFormsEditForm'];
$this->submitTitle=$data->phrases['dynamic-forms']['submitFormsTitle'];
$this->fields=array(
	'name' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsName'],
		'tag' => 'input',
		'value' => isset($data->output['formItem']['name']) ? $data->output['formItem']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'required' => true,
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsName'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsName'].'
			</p>
		'
	),
	'title' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsTitle'],
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['formItem']['title']) ? $data->output['formItem']['title'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsTitle'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsTitle'].'
			</p>
		'
	),
	'submitTitle' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsSubmitTitle'],
		'tag' => 'input',
		'value' => isset($data->output['formItem']['submitTitle']) ? $data->output['formItem']['submitTitle'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsSubmitTitle'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsSubmitTitle'].'
			</p>
		'
	),
	'eMail' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsEmail'],
		'tag' => 'input',
		'required' => false,
		'params' => array(
			'type' => 'text'
		),
		'value' => isset($data->output['formItem']['eMail']) ? $data->output['formItem']['eMail'] : '',
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsEmail'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsEmail'].'
			</p>'
	),
	'api' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsAPI'],
		'tag' => 'select',
		'required' => false,
		'value' => (isset($data->output['formItem']['api'])) ? $data->output['formItem']['api'] : 0,
		'options' => array(
			array(
				'text' => 'No API',
				'value' => 0
			)
		)
	),
	'showOnMenu' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsShowOnMenu'],
		'tag' => 'input',
		'hideChild' => 'menuTitle',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsShowOnMenu'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsShowOnMenu'].'
			</p>
		'
	),
	'menuTitle' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsMenuTitle'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => '128',
			'maxLength' => '128'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsMenuTitle'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsMenuTitle'].'
			</p>
		'
	),
	'rawContentBefore' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsRawContentBefore'],
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['formItem']['rawContentBefore']) ? $data->output['formItem']['rawContentBefore'] : '',
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsRawContentBefore'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsRawContentBefore'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContentBefore')
	),
	'rawContentAfter' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsRawContentAfter'],
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['formItem']['rawContentAfter']) ? $data->output['formItem']['rawContentAfter'] : '',
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsRawContentAfter'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsRawContentAfter'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContentAfter')
	),
	'rawSuccessMessage' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsRawSuccessMessage'],
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['formItem']['rawSuccessMessage']) ? $data->output['formItem']['rawSuccessMessage'] : '',
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsRawSuccessMessage'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsRawSuccessMessage'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawSuccessMessage')
	),
	'requireLogin' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsRequireLogin'],
		'tag' => 'input',
		'checked' => ((isset($data->output['formItem']['requireLogin']) && $data->output['formItem']['requireLogin']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsRequireLogin'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsRequireLogin'].' 
			</p>
		'
	),
	'topLevel' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsTopLevel'],
		'tag' => 'input',
		'checked' => ((isset($data->output['formItem']['topLevel']) && $data->output['formItem']['topLevel']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['dynamic-forms']['labelFormsTopLevel'].'</b><br />
				'.$data->phrases['dynamic-forms']['descriptionFormsTopLevel'].'
			</p>
		'
	),
	'enabled' => array(
		'label' => $data->phrases['dynamic-forms']['labelFormsEnabled'],
		'tag' => 'input',
		'value' => 1,
		'params' => array(
			'type' => 'checkbox',
			'checked' => 'checked'
		)
	)
);
