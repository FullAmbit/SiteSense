<?php
function languages_settings(){
	return array(
		'name' => 'languages',
		'shortName' => 'languages'
	);
}
function languages_install($db,$drop = FALSE,$firstInstall=FALSE,$lang='en_us'){
	$structures = array(
		'languages' => array(
			'shortName'  =>	'VARCHAR(5) NOT NULL',
			'name'		   =>	SQR_name,
			'isDefault'	 =>	SQR_boolean
		),
		'languages_phrases' => array(
			'id'         => SQR_IDKey,
		    'phrase'     => 'VARCHAR(255) NOT NULL',
		    'text'       => 'TEXT NOT NULL',
		    'module'     => SQR_moduleName,
		    'isAdmin'    => SQR_boolean,
			'override'   => SQR_boolean . ' DEFAULT 0',
		    'UNIQUE KEY `phrase` (`phrase`,`module`,`isAdmin`)'
		)
	);
	
	if($firstInstall){
		
		$db->createTable('languages',$structures['languages'],false);
		$db->createTable('languages_phrases',$structures['languages_phrases'],$lang);
		
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
		
		$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>"en_us"));
		foreach($languageItem['user_phrases'] as $phrase => $text) {
			$statement->execute(array(
				':phrase' => $phrase,
				':text' => $text,
				':module' => '',
				':isAdmin' => 0
			));
		}
		foreach($languageItem['admin_phrases'] as $phrase => $text) {
			$statement->execute(array(
				':phrase' => $phrase,
				':text' => $text,
				':module' => '',
				':isAdmin' => 1
			));
		}
	}else{
		$db->createTable('languages_phrases',$structures['languages_phrases'],$lang);
	}
}
function languages_uninstall($db){
	
}
?>