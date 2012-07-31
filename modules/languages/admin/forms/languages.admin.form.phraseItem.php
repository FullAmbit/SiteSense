<?php

$this->submitTitle = 'Save';

$this->fields = array(
	'phrase' => array(
		'label' => 'Phrase',
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['phrase'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	),
	'text' => array(
		'label' => 'Text',
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['text'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => false
	),
	'module' => array(
		'label' => 'Module',
		'tag' => 'select',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['module'] : '',
		'options' => array(
			array(
				'text' => 'Global',
				'value' => ''
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