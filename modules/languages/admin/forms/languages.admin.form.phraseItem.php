<?php

$this->submitTitle = $data->phrases['languages']['submitPhraseItemForm'];

$this->fields = array(
	'module' => array(
		'label' => $data->phrases['languages']['module'],
		'tag' => 'select',
		'value' => (isset($data->output['phraseItem']['en_us'])) ? $data->output['phraseItem']['en_us']['module'] : '',
		'options' => array(
			array(
				'text' => 'Global',
				'value' => ''
			)
		)
	),
	'isAdmin' => array(
		'label' => 'Admin Phrase',
		'tag' => 'input',
		'value' => 1,
		'params' => array(
			'type' => 'checkbox'
		)
	),
	'phrase' => array(
		'label' => $data->phrases['languages']['phrase'],
		'tag' => 'input',
		'value' => (isset($data->output['phraseItem']['en_us'])) ? $data->output['phraseItem']['en_us']['phrase'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => true
	)
);

if(isset($data->output['phraseItem']['en_us']) && $data->output['phraseItem']['en_us']['isAdmin'] == '1'){
	$this->fields['isAdmin']['params']['checked'] = "checked";
}

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
		'value' => (isset($data->output['phraseItem'][$languageItem['shortName']])) ? $data->output['phraseItem'][$languageItem['shortName']]['text'] : '',
		'params' => array(
			'type' => 'text'
		),
		'required' => ($languageItem['shortName'] == 'en_us' ) ? true : false,
	);
}

?>