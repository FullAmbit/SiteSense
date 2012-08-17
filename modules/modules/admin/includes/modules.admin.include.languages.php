<?php

common_include('modules/languages/admin/languages.admin.common.php');
common_include('libraries/queries/defines.mysql.php');

function admin_modulesBuild($data, $db) {
	// Get Module Item
	$moduleId = intval($data->action[3]);
	$statement = $db->prepare('getModuleById', 'admin_modules');
	$statement->execute(array(
			':id' => $moduleId
		));
	if (($data->output['moduleItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE) {
		$data->output['themeOverride']='NotFound';
		return;
	}
	// Get Modular Language Files And Make Sure They Are Installed in DB First
	$moduleName = $data->output['moduleItem']['name'];

	foreach ($data->languageList as $languageItem) {
		$data->output['languageList'][$languageItem['shortName']] = $languageItem['name'];
		$userEndFile='modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$languageItem['shortName'].'.php';
		$adminEndFile='modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.'.$languageItem['shortName'].'.php';
		
		// Do We Have A Phrase For This? If So...Add It To List Of Phrase Languages
		if (file_exists($userEndFile)) {
			common_include($userEndFile);
		}
		
		if (file_exists($adminEndFile)) {
			common_include($adminEndFile);
		}
	}

	// Installation Logic
	if (isset($_POST['install'])) {
		$error = false;
		// Check If Language Exists In Our List
		if (!isset($data->output['languageList'][$_POST['updateLanguage']])) {
			$data->output['responseMessage']=$data->phrases['modules']['languageNotFound'];
			return;
		}
		// Create A Duplicate Table For This Module For Installation Purposes
		$installFile = 'modules/'.$moduleName.'/'.$moduleName.'.install.php';
		if (file_exists($installFile)) {
			common_include($installFile);
			$installFunc = $moduleName.'_install';
			if (function_exists($installFunc)) {
				$installFunc($db, FALSE, FALSE, $_POST['updateLanguage']);
				$data->output['responseMessage']=$data->phrases['modules']['languageTablesCreated'].'<br />';
			}
		}
		
		if($_POST['action']==0){
			// Empty Table And Prep For Phrase Entry
			$statement=$db->prepare("deletePhrasesByModuleAndLanguage","admin_languages",array("!lang!"=>$_POST['updateLanguage']));
			$statment->execute(array(
				':module' => $moduleName 
			));
		}

		// Load The Phrases (USER END)
		$func = 'languages_'.$moduleName.'_'.$_POST['updateLanguage'];
		if (!function_exists($func)) {
			$data->output['responseMessage'].=$data->phrases['modules']['installLanguageErrorMissingUserEnd'].'<br />';
			$error = true;
		}else{
			$phrases = $func($data, $db);
			if(isset($phrases['core']) && is_array($phrases['core']) && !empty($phrases['core'])){
				// Add Additional Core Phrases
				language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$phrases['core']);
				unset($phrases['core']);
			}
			// Save The Phrases
			if (language_admin_savePhrases($data, $db, $_POST['updateLanguage'], $moduleName, $phrases) === FALSE){
				$data->output['responseMessage'] .= $data->phrases['modules']['installLanguageErrorSaveUserEnd'].'<br />';
				$error = true;
			}else{
				$data->output['responseMessage'] .= $data->phrases['modules']['installLanguageSuccessUserEnd'];
			}
		}
		
		// Load The Phrases (ADMIN END)
		$func = 'languages_'.$moduleName.'_admin_'.$_POST['updateLanguage'];
		if(!function_exists($func)){
			$data->output['responseMessage'].=$data->phrases['modules']['installLanguageErrorMissingAdminEnd'].'<br />';
			$error = true;
		}else{
			$phrases = $func($data, $db);
			if(isset($phrases['core']) && is_array($phrases['core']) && !empty($phrases['core'])){
				// Add Additional Core Phrases
				language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$phrases['core'],TRUE);
				unset($phrases['core']);
			}
			// Save The Phrases
			if (language_admin_savePhrases($data, $db, $_POST['updateLanguage'], $moduleName, $phrases,TRUE) === FALSE){
				$data->output['responseMessage'] .= $data->phrases['modules']['installLanguageErrorSaveAdminEnd'].'<br />';
				$error = true;
			}else{
				$data->output['responseMessage'] .= $data->phrases['modules']['installLanguageSuccessAdminEnd'];
			}
		}
	}
	
	if(!$error) $data->output['themeOverride']='LanguageSuccess';
}

function admin_modulesShow($data) {
	if (isset($data->output['themeOverride'])) {
		$func = 'theme_modules'.$data->output['themeOverride'];
		$func($data);
	}else {
		theme_modulesLanguages($data);
	}
}


?>