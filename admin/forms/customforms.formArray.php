<?php

$this->caption='Create/Edit Custom Form';
$this->submitTitle='Save';

$this->fields=array(
	'shortName' => array(
		'label' => 'URL',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['customform']['shortName']) ? $data->output['customform']['shortName'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>URL</b> - Give this a unique name so users can access it
			</p>
		'
	),
	'name' => array(
		'label' => 'Name',
		'tag' => 'input',
		'value' => isset($data->output['customform']['name']) ? $data->output['customform']['name'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Name</b> - The name which the user sees when filling out the form.
			</p>
		'
	),
	'successMessage' => array(
		'label' => 'Success Message',
		'tag' => 'textarea',
		'useEditor' => true,
		'value' => isset($data->output['customform']['successMessage']) ? $data->output['customform']['successMessage'] : '',
		'description' => '
			<p>
				<b>Success Message</b> - What do you want to display to the user when the form has been submitted?
			</p>
		'
	),
	'requireLogin' => array(
		'label' => 'Require Login?',
		'tag' => 'input',
		'checked' => ((isset($data->output['customform']['requireLogin']) && $data->output['customform']['requireLogin']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				Do you want the browser to redirect? Otherwise the user will see the original url but a different page. 
			</p>
		'
	)
);
