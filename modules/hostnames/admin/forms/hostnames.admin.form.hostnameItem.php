<?php

$this->submitTitle = $data->phrases['hostnames']['submitHostnameItemForm'];

$this->fields = array(
	'hostname' => array(
		'label' => $data->phrases['hostnames']['labelHostnameItemHostname'],
		'tag' => 'input',
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['hostname'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	),
	'defaultTheme' => array(
		'tag' => 'select',
		'label' => $data->phrases['hostnames']['labelHostnameItemDefaultTheme'],
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['defaultTheme'] : '',
		'options' => array(),
		'required' => true
	),
	'defaultLanguage' => array(
		'tag' => 'select',
		'label' => $data->phrases['hostnames']['labelHostnameItemDefaultLanguage'],
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['defaultLanguage'] : '',
		'options' => array(),
		'required' => true
	),
	'homepage' => array(
		'tag' => 'select',
		'label' => $data->phrases['hostnames']['labelHostnameItemHomepage'],
		'value' => (isset($data->output['hostnameItem'])) ? $data->output['hostnameItem']['homepage'] : '',
		'options' => array(),
		'required' => true
	)
);