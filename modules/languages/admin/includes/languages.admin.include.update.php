<?php

common_include('modules/languages/admin/languages.admin.common.php');
common_include('libraries/queries/defines.mysql.php');

function languages_admin_update_build($data,$db){
	// Build List Of Languages Available To You (Must Have Core Phrase File In modules/langauges/phrases/)
	$installerFiles = glob("modules/languages/phrases/languages.phrases.*.php",GLOB_BRACE);
	$languageList = array();
	// Loop Through And Create List Of All Available Core Languages
	foreach($installerFiles as $installerFile){
		$matches = array();
		if(preg_match("/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$installerFile,$matches) == 0){
			continue;
		}
		$languageShortName = $matches[2].'_'.$matches[3];
		common_include($installerFile);
		
		$func = 'languages_core_'.$languageShortName;
		if(function_exists($func)){
			$languageList[$languageShortName] = $func();
		}
	}
	$data->output['languageList'] = $languageList;
	
	if(isset($_POST['install'])){
		// Make Sure The Language We Specified Has A Core File
		if(!isset($languageList[$_POST['updateLanguage']])){
			$data->output['responseMessage'] = 'The language you selected does not have a core installer file.';
			return;
		}
		
		// Check If Language Is Already Installed....
		$statement=$db->prepare('getLanguage','admin_languages');
		$statement->execute(array(
			':shortName' => $_POST['updateLanguage']
		));
		if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
			// Language Not Installed...Create Table For Phrases
			$statement = $db->prepare("createLanguageTable","admin_languages",array("!lang!"=>$_POST['updateLanguage']));
			$result = $statement->execute();
			if($result == FALSE){
				$data->output['responseMessage'] = 'There was an error in creating the phrases table for the langauge.';
				return;
			}

			$statement = $db->prepare('addLanguage','admin_languages');
			$statement->execute(array(
				':shortName' => $_POST['updateLanguage'],
				':name' => $languageList[$_POST['updateLanguage']]['name']
			));
			
			if($result == FALSE){
				$data->output['responseMessage'] = 'There was an error in adding the language to the database.';
				return;
			}
		}
		// Add Core Phrases
		if(language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$languageList[$_POST['updateLanguage']]['phrases']) === FALSE) return;
		
		if(isset($_POST['updateModules']) && $_POST['updateModules']=='1') {
			// Loop Through All Installed Modules And Create A Table For Each One, And Install Phrases
			$temp=array('sidebars' => $data->output['moduleShortName']['sidebars']);
			unset($data->output['moduleShortName']['sidebars'],$data->output['moduleShortName']['languages'],$data->output['moduleShortName']['users'],$data->output['moduleShortName']['dynamicURLs'],$data->output['moduleShortName']['modules']);
			$data->output['moduleShortName']=$temp+$data->output['moduleShortName'];
			
			foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
				// Create A Duplicate Table For This Module For Installation Purposes
				$installFile = 'modules/'.$moduleName.'/'.$moduleName.'.install.php';
				if (file_exists($installFile)) {
					common_include($installFile);
					$installFunc = $moduleName.'_install';
					if (function_exists($installFunc)) {
						$installFunc($db, FALSE, FALSE, $_POST['updateLanguage']);
					}
				}
				
				// Load The Phrases
				if (file_exists('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$_POST['updateLanguage'].'.php')) {
					common_include('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$_POST['updateLanguage'].'.php');
					$func = 'languages_'.$moduleName.'_'.$_POST['updateLanguage'];
					if (function_exists($func)) {
						$modulePhrases = $func($data, $db);
						// Save The Phrases
						language_admin_savePhrases($data, $db, $_POST['updateLanguage'], $moduleName, $modulePhrases);
					}
				}
			}	
		}
		$data->output['themeOverride'] = 'UpdateSuccess';
	}	
}	

function languages_admin_update_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesUpdate($data);
	}
}
?>