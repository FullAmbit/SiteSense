<?php

common_include('modules/languages/admin/languages.admin.common.php');

function admin_modulesBuild($data,$db){
	// Get Module Item
	$moduleId = intval($data->action[3]);
	$statement = $db->prepare('getModuleById','admin_modules');
	$statement->execute(array(
		':id' => $moduleId
	));
	if(($data->output['moduleItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride']='NotFound';
		return;
	}
	// Get Modular Language Files And Make Sure They Are Installed in DB First
	$moduleName = $data->output['moduleItem']['name'];
	$languageFileList = glob("modules/".$moduleName."/languages/".$moduleName.".phrases.*.php");
	foreach($languageFileList as $languageFile){
		$matches = array();
		if(preg_match("/(.*?)\/(.*?)\/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$languageFile,$matches) === 0) continue;
		$languageShortName = $matches[4].'_'.$matches[5];

		// Check If Language Is Installed In Database...
		$statement=$db->prepare('getLanguage','admin_languages');
		$statement->execute(array(
			':shortName' => $languageShortName
		));
		if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE) continue;
		
		common_include($languageFile);
		$data->output['languageList'][$languageShortName] = $data->output['languageItem']['name'];
	}
	// Installation Logic
	if(isset($_POST['install'])){
		// Check If Language Exists In Our List
		if(!isset($data->output['languageList'][$_POST['language']])){
			$data->output['responseMessage']="The language you specified could not be found.";
		}
		// Load The Phrases
		$func = 'languages_'.$moduleName.'_'.$_POST['language'];
		if(!function_exists($func)){
			$data->output['responseMessage']='The language installer function for this module is missing or corrupt.';
			return;
		}
		$modulePhrases = $func($data,$db);
		
		// Save The Phrases
		if(language_admin_savePhrases($data,$db,$_POST['language'],$moduleName,$modulePhrases) === FALSE) return;
		
		$data->output['themeOverride']='LanguageSuccess';		
	}
}

function admin_modulesShow($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_modules'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_modulesLanguages($data);
	}
}


?>