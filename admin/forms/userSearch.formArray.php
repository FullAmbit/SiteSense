<?php
$this->action=$data->linkRoot.'admin/users/search/';

$this->formPrefix='searchUser_';
$this->caption='Searching Users';
$this->submitTitle='Search';
$this->fromForm='searchUser';
global $languageText;
$levelOptions = array(array('value' => '-100', 'text' => 'All'));
foreach($languageText['userLevels'] as $value => $text){
	$levelOptions[] = array('value' => $value, 'text' => $text);
}
$this->fields=array(
	'name' => array(
		'label' => 'Username',
		'required' => false,
		'tag' => 'input',
		'value' => '%',
		'params' => array(
			'type' => 'text',
			'size' => 128
		)
	),
	'fullName' => array(
		'label' => 'Full Name',
		'required' => false,
		'tag' => 'input',
		'value' => '%',
		'params' => array(
			'type' => 'text',
			'size' => 128
		)
	),
	'userLevel' => array(
		'label' => 'User Access Level',
		'required' => false,
		'tag' => 'select',
		'value' => '-100',
		'options' => $levelOptions,
	),
);
