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
$this->caption='Create Form';
$this->submitTitle='Save';
$this->fields=array(
	'name' => array(
		'label' => 'Name',
		'tag' => 'input',
		'value' => isset($data->output['formItem']['name']) ? $data->output['formItem']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'required' => true,
		'description' => '
			<p>
				<b>Name</b> - A unique name used for accessing the form.
			</p>
		'
	),
	'title' => array(
		'label' => 'Title',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['formItem']['title']) ? $data->output['formItem']['title'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Title</b> - The title of the form to be seen by users
			</p>
		'
	),
	'submitTitle' => array(
		'label' => 'Submit Title',
		'tag' => 'input',
		'value' => isset($data->output['formItem']['submitTitle']) ? $data->output['formItem']['submitTitle'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Submit Title</b> - The text displayed on the submit button.
			</p>
		'
	),
	'eMail' => array(
		'label' => 'Email Data To:',
		'tag' => 'input',
		'required' => false,
		'params' => array(
			'type' => 'text'
		),
		'value' => isset($data->output['formItem']['eMail']) ? $data->output['formItem']['eMail'] : '',
		'description' => '<p><b>Email</b> - Specify an email address to send submitted form data to. Multiple addresses should be seperated by commas with no spaces.</p>'
	),
	'api' => array(
		'label' => 'API',
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
		'label' => 'Create Main Menu Link',
		'tag' => 'input',
		'hideChild' => 'menuTitle',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Show on Menu</b> - Adds this static page to the main menu
			</p>
		'
	),
	'menuTitle' => array(
		'label' => 'Menu Link Text',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => '128',
			'maxLength' => '128'
		),
		'description' => '
			<p>
				<b>Menu Title</b> - if this element is to be shown on the menu, this is the text that will be shown inside it\'s anchor.
			</p>
		'
	),
	'rawContentBefore' => array(
		'label' => 'Content Before Form',
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['formItem']['rawContentBefore']) ? $data->output['formItem']['rawContentBefore'] : '',
		'description' => '
			<p>
				<b>Content Before Form</b> - What will the user read before the form?
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContentBefore')
	),
	'rawContentAfter' => array(
		'label' => 'Content After Form',
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['formItem']['rawContentAfter']) ? $data->output['formItem']['rawContentAfter'] : '',
		'description' => '
			<p>
				<b>Content After Form</b> - What will the user read after the form?
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContentAfter')
	),
	'rawSuccessMessage' => array(
		'label' => 'Success Message',
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['formItem']['rawSuccessMessage']) ? $data->output['formItem']['rawSuccessMessage'] : '',
		'description' => '
			<p>
				<b>Success Message</b> - What do you want to display to the user when the form has been submitted?
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawSuccessMessage')
	),
	'requireLogin' => array(
		'label' => 'Require Login?',
		'tag' => 'input',
		'checked' => ((isset($data->output['formItem']['requireLogin']) && $data->output['formItem']['requireLogin']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				Do you want the browser to redirect? Otherwise the user will see the original url but a different page. 
			</p>
		'
	),
	'topLevel' => array(
		'label' => 'Make Form a Top-Level Page?',
		'tag' => 'input',
		'checked' => ((isset($data->output['formItem']['topLevel']) && $data->output['formItem']['topLevel']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				Check this if you want the url /form-name as an alternative to /forms/form-name 
			</p>
		'
	),
	'enabled' => array(
		'label' => 'Enabled',
		'tag' => 'input',
		'value' => 1,
		'params' => array(
			'type' => 'checkbox',
			'checked' => 'checked'
		)
	)
);
