<?php

$this->submitTitle = 'Save';

$this->fields = array(
	'hostname' => array(
		'label' => 'Hostname',
		'tag' => 'input',
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['hostname'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	),
	'defaultTheme' => array(
		'tag' => 'select',
		'label' => 'Default Theme',
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['defaultTheme'] : '',
		'options' => array(),
		'required' => true
	),
	'defaultLanguage' => array(
		'tag' => 'select',
		'label' => 'Default Language',
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['defaultLanguage'] : '',
		'options' => array(),
		'required' => true
	),
	'homepage' => array(
		'tag' => 'select',
		'label' => 'Homepage',
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['homepage'] : '',
		'options' => array(),
		'required' => true
	)
);