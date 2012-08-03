<?php

function languages_settings(){
	return array(
		'name' => 'languages',
		'shortName' => 'languages'
	);
}

function languages_install($db,$drop = FALSE){
	$structures = array(
		'languages'	=>	array(
			'shortName' =>	'VARCHAR(64) CHARACTER SET utf8 NOT NULL DEFAULT ""',
			'name'		=>	'VARCHAR(64) CHARACTER SET utf8 NOT NULL DEFAULT""',
			'isDefault'	=>	'TINYINT(1) NOT NULL DEFAULT "0"'
		)
	);
	
	$db->createTable('languages',$structures['languages'],false);
	
	// Install English //
	common_include('modules/languages/admin/languages.admin.common.php');
	common_include('modules/languages/phrases/languages.phrases.en_us.php');
	
	$func = 'languages_core_en_us';
	$languageItem = $func();
	// add to list of installed langauges.
	$statement = $db->prepare('addDefaultLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => 'en_us',
		':name' => $languageItem['name']
	));

	// Install Phrases (creat lang table first)
	$create = $db->prepare("createLanguageTable","admin_languages",array("!lang!"=>"en_us"));
	$create->execute();
	
	$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>"en_us"));
	foreach($languageItem['phrases'] as $phrase => $text){
		$statement->execute(array(
			':phrase' => $phrase,
			':text' => $text,
			':module' => ''
		));
	}
}

function languages_uninstall($db){
}

?>