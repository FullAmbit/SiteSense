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
		// Do We Have A Phrase For This? If So...Add It To List Of Phrase Languages
		if (file_exists('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$languageItem['shortName'].'.php')) {
			common_include('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$languageItem['shortName'].'.php');
		}
	}

	// Installation Logic
	if (isset($_POST['install'])) {
		// Check If Language Exists In Our List
		if (!isset($data->languageList[$_POST['updateLanguage']])) {
			$data->output['responseMessage']="The language you specified could not be found.";
		}
		// Create A Duplicate Table For This Module For Installation Purposes
		$installFile = 'modules/'.$moduleName.'/'.$moduleName.'.install.php';
		if (file_exists($installFile)) {
			common_include($installFile);
			$installFunc = $moduleName.'_install';
			if (function_exists($installFunc)) {
				$installFunc($db, FALSE, FALSE, $_POST['updateLanguage']);
				$data->output['responseMessage']='The language tables for this module were created.<br />';
			}
		}

		// Load The Phrases
		$func = 'languages_'.$moduleName.'_'.$_POST['updateLanguage'];
		if (!function_exists($func)) {
			$data->output['responseMessage'].='The phrase installation file for this module is either corrupt or missing the function.';
			return;
		}
		$modulePhrases = $func($data, $db);

		// Save The Phrases
		if (language_admin_savePhrases($data, $db, $_POST['updateLanguage'], $moduleName, $modulePhrases) === FALSE) return;

		$data->output['themeOverride']='LanguageSuccess';
	}
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