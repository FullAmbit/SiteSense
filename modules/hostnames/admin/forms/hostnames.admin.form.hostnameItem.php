<?php

$this->submitTitle = 'Save';

$this->fields = array(
	'hostname' => array(
		'label' => 'Hostname',
		'tag' => 'input',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	),
	'defaultTheme' => array(
		'tag' => 'select',
		'label' => 'Default Theme',
		'options' => array(),
		'required' => true
	),
	'homepage' => array(
		'tag' => 'select',
		'label' => 'Homepage',
		'options' => array(),
		'required' => true
	)
);