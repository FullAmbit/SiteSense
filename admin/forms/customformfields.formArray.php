<?php

$this->caption='Create/Edit Custom Form';
$this->submitTitle='Save';

$this->fields=array(
	'name' => array(
		'label' => 'Name',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['customfield']['name']) ? $data->output['customfield']['name'] : '',
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
	'type' => array(
		'label' => 'Type',
		'tag' => 'select',
		'options' => array(
			'textbox', 'textarea'
		),
		'value' => isset($data->output['customfield']['type']) ? $data->output['customfield']['type'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Type</b> - What is the field?
			</p>
		'
	)
);
