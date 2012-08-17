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
			$data->output['responseMessage'] = $data->phrases['languages']['missingCoreInstallerFile'];
			return;
		}
		
		// Check If Language Is Already Installed....
		$statement=$db->prepare('getLanguage','admin_languages');
		$statement->execute(array(
			':shortName' => $_POST['updateLanguage']
		));
		if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
			
			$statement = $db->prepare('addLanguage','admin_languages');
			$statement->execute(array(
				':shortName' => $_POST['updateLanguage'],
				':name' => $languageList[$_POST['updateLanguage']]['name']
			));
			
			if($result == FALSE){
				$data->output['responseMessage'] = $data->phrases['languages']['addLanguageDBError'];
				return;
			}
		}
		// Add Core Phrases
		if($_POST['action']==0){
			// Empty Table And Prep For Phrase Entry
			$statement=$db->query("truncatePhrases","admin_languages",array("!lang!"=>$languageShortName));
		}
		// Save Admin & User-End Core Phrases
		if((language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$languageList[$_POST['updateLanguage']]['user_phrases']) === FALSE) ||
		   (language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$languageList[$_POST['updateLanguage']]['admin_phrases'],TRUE) === FALSE)){
		   	return;
		}
		
		if(isset($_POST['updateModules']) && $_POST['updateModules']=='1') {
			// Loop Through All Installed Modules And Create A Table For Each One, And Install Phrases
			$temp=array('languages' => 'languages','sidebars' => $data->output['moduleShortName']['sidebars']);
			unset($data->output['moduleShortName']['sidebars'],$data->output['moduleShortName']['modules'],$data->output['moduleShortName']['languages']);
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
				// Load The Phrases ( Front End )
				if (file_exists('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$_POST['updateLanguage'].'.php')) {
					common_include('modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$_POST['updateLanguage'].'.php');
					$func = 'languages_'.$moduleName.'_'.$_POST['updateLanguage'];
					if (function_exists($func)) {
						$phrases = $func($data, $db);
						
						if(isset($phrases['core']) && is_array($phrases['core']) && !empty($phrases['core'])){
							// Add Additional Core Phrases
							language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$phrases['core']);
							unset($phrases['core']);
						}
						// Save The Phrases
						language_admin_savePhrases($data, $db, $_POST['updateLanguage'], $moduleName, $phrases);
					}
				}
				
				// Load The Phrases (Admin End)
				if (file_exists('modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.'.$_POST['updateLanguage'].'.php')) {
					common_include('modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.'.$_POST['updateLanguage'].'.php');
					$func = 'languages_'.$moduleName.'_admin_'.$_POST['updateLanguage'];
					if (function_exists($func)) {
						$phrases = $func($data, $db);
						if(isset($phrases['core']) && is_array($phrases['core']) && !empty($phrases['core'])){
							// Add Additional Core Phrases, set isADMIN To True
							language_admin_savePhrases($data,$db,$_POST['updateLanguage'],'',$phrases['core'],TRUE);
							unset($phrases['core']);
						}
						// Save The Modular Phrases, Set isADMIN To True
						language_admin_savePhrases($data, $db, $_POST['updateLanguage'], $moduleName, $phrases, TRUE);
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