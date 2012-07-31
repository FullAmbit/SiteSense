<?php

$this->submitTitle = 'Save';

$this->fields = array(
	'phrase' => array(
		'label' => 'Phrase',
		'tag' => 'input',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	),
	'text' => array(
		'label' => 'Text',
		'tag' => 'input',
		'params' => array(
			'type' => 'text'
		),
		'required' => false
	),
	'module' => array(
		'label' => 'Module',
		'tag' => 'select',
		'options' => array(
			array(
				'text' => 'Global',
				'value' => NULL
			)
		)
	)
);

foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
	$this->fields['module']['options'][] = array(
		'text' => $moduleName,
		'value' => $moduleShortName
	);
}

?>