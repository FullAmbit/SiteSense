<?php

$this->submitTitle = $data->phrases['languages']['submitPhraseItemForm'];

$this->fields = array(
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
	),
	'phrase' => array(
		'label' => $data->phrases['languages']['phrase'],
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['phrase'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	)
);

foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
	$this->fields['module']['options'][] = array(
		'text' => $moduleName,
		'value' => $moduleShortName
	);
}

foreach($data->languageList as $languageItem){
	$this->fields['text_'.$languageItem['shortName']]=array(
		'label' => $languageItem['name'].'&nbsp;'.$data->phrases['languages']['text'],
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem'])) ? $data->output['phraseItem']['text'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => ($languageItem['shortName'] == 'en_us' ) ? true : false,
	);
}

?>