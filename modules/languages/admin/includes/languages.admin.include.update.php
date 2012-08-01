<?php

common_include('modules/languages/admin/languages.admin.common.php');

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
		if(!isset($languageList[$_POST['language']])){
			$data->output['responseMessage'] = 'The language you selected does not have a core installer file.';
			return;
		}
		
		// Check If Language Is Already Installed....
		$statement=$db->prepare('getLanguage','admin_languages');
		$statement->execute(array(
			':shortName' => $_POST['language']
		));
		if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
			// Language Not Installed...Create Table
			$statement = $db->prepare("createLanguageTable","admin_languages","languages_phrases_".$_POST['language']);
			$result = $statement->execute();
			if($result == FALSE){
				$data->output['responseMessage'] = 'There was an error in creating the table for the langauge.';
				return;
			}

			// add to list of installed langauges.
			$statement = $db->prepare('addLanguage','admin_languages');
			$statement->execute(array(
				':shortName' => $_POST['language'],
				':name' => $languageList[$_POST['language']]['name']
			));
			
			if($result == FALSE){
				$data->output['responseMessage'] = 'There was an error in adding the language to the database.';
				return;
			}
		}
		// Add Core Phrases
		if(language_admin_savePhrases($data,$db,$_POST['language'],'',$languageList[$_POST['language']]['phrases']) === FALSE) return;
		
		if(isset($_POST['updateModules']) && $_POST['updateModules']=='1'){		
			// Get The Modular Installation Files For This Language
			$dirNames = array_flip($data->output['moduleShortName']);
			$dirSearch = implode(',',$data->output['moduleShortName']);
			$modularFileList = glob("modules/{".$dirSearch."}/languages/{".$dirSearch."}.phrases.".$_POST['language'].".php",GLOB_BRACE);
			foreach($modularFileList as $modularFile){
				$matches=array();
				if(preg_match("/(.*?)\/(.*?)\/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$modularFile,$matches) == 0) continue;
				$moduleName = $matches[2];
				
				$func = 'languages_'.$moduleName.'_'.$_POST['language'];
				common_include($modularFile);
				if(!function_exists($func)) continue;
				// Get Phrases For This Module
				$modulePhrases = $func();
				// Save These Phrases
				if(language_admin_savePhrases($data,$db,$_POST['language'],$moduleName,$modulePhrases) === FALSE) return;			
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