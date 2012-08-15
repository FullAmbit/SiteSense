<?php

$this->submitTitle = $data->phrases['languages']['submitPhraseItemForm'];

$this->fields = array(
	'phrase' => array(
		'label' => $data->phrases['languages']['phrase'],
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['phrase'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	),
	'text' => array(
		'label' => $data->phrases['languages']['text'],
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['text'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => false
	),
	'module' => array(
		'label' => $data->phrases['languages']['module'],
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