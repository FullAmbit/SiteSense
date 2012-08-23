<?php
common_include('libraries/queries/defines.mysql.php');
common_include('modules/languages/admin/languages.admin.common.php');

function languages_admin_newlanguage_build($data,$db){
	if(!checkPermission('newLanguage','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	// What Core Language Files Do We Have?...
	$installerFiles = glob("modules/languages/phrases/languages.phrases.*.php",GLOB_BRACE);
	$data->output['languageList'] = array();
	// Loop Through And Create List Of All Available Core Languages
	foreach($installerFiles as $installerFile){
		$matches = array();
		if(preg_match("/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$installerFile,$matches) == 0){
			continue;
		}
		$languageShortName = $matches[2].'_'.$matches[3];
		$exists = FALSE;
		// Check To See If We Have This One Installed..
		foreach($data->languageList as $languageItem){
			if($languageItem['shortName'] == $languageShortName){
				$exists = TRUE;
				break;
			}
		}
		if($exists) continue;
		
		common_include($installerFile);
		$func = 'languages_core_'.$languageShortName;
		$data->output['languageList'][$languageShortName] = $func();
	}
	
	if(isset($_POST['install'])){
		if(!isset($data->output['languageList'][$_POST['newLanguage']])){
			$data->output['responseMessage'] = 'The language you selected could not be found.';
			return;
		}
		$languageItem = $data->output['languageList'][$_POST['newLanguage']];
		
		// Alright, first thing first. Add This Language To Our List Of Installed Languages
		$statement = $db->prepare('addLanguage','admin_languages');
		$statement->execute(array(
			':shortName' => $languageItem['shortName'],
			':name' =>  $languageItem['name']
		));
		
		$data->output['userErrors']=$data->output['adminErrors']=$data->output['userNew']=$data->output['adminNew']=array();
		// Now...we can't install any phrases without first creating tables, so we'll need to run the modular installation files first
		// Move Languages To Front, So That We Can Create The Phrases Table Right Away
		$temp=array('languages' => 'languages','sidebars' => 'sidebars','modules' => 'modules'); 
		unset($data->output['moduleShortName']['sidebars'],$data->output['moduleShortName']['languages'],$data->output['moduleShortName']['modules']);
		$data->output['moduleShortName']=$temp+$data->output['moduleShortName'];
		
		foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
			$_POST['action'] = 4;
			
			
			// Get Installer File And Install The Module (For This Language)
			$installFile = 'modules/'.$moduleName.'/'.$moduleName.'.install.php';
			if (file_exists($installFile)) {
				common_include($installFile);
				$installFunc = $moduleName.'_install';
				if (function_exists($installFunc)) {
					$installFunc($db, FALSE, FALSE, $languageItem['shortName']);
				}
			}
			
			// For Each Module Now..Handle The English Phrases (User End AND Admin End)
			$coreAdminPhrases=$coreUserPhrases=$userPhrases=$adminPhrases=array();
			
			$userEndTarget = 'modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.'.$languageItem['shortName'].'.php';
			if(file_exists($userEndTarget)){
				common_include($userEndTarget);
				$userEndFunc = 'languages_'.$moduleName.'_'.$languageItem['shortName'];
				if(function_exists($userEndFunc)){
					$userPhrases = $userEndFunc();
					// Do We Have Additional Core Phrases?...
					if(isset($userPhrases['core']) && is_array($userPhrases['core'])){
						$coreUserPhrases = $userPhrases['core'];
						unset($userPhrases['core']);
					}
				}
			}
			
			list($userErrors,$userNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreUserPhrases);
			$data->output['userErrors'] += $userErrors;
			$data->output['userNew'] += $userNew;
			
			list($userErrors,$userNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$userPhrases);
			$data->output['userErrors'] += $userErrors;
			$data->output['userNew'] += $userNew;
			
			//--Admin Phrases
			$adminEndTarget = 'modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.'.$languageItem['shortName'].'.php';
			if(file_exists($adminEndTarget)){
				common_include($adminEndTarget);
				$adminEndFunc = 'languages_'.$moduleName.'_admin_'.$languageItem['shortName'];
				if(function_exists($adminEndFunc)){
					$adminPhrases = $adminEndFunc();
					if(is_array($adminPhrases['core'])){
						$coreAdminPhrases = $adminPhrases['core'];
						unset($adminPhrases['core']);
					}
				}
			}
			
			// Save Additional Core Phrases
			list($adminErrors,$adminNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreAdminPhrases,TRUE);
			$data->output['adminErrors'] += $adminErrors;
			$data->output['adminNew'] += $adminNew;		
			// Save Modular Phrases
			list($adminErrors,$adminNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$adminPhrases,TRUE);
			$data->output['adminErrors'] += $adminErrors;
			$data->output['adminNew'] += $adminNew;	
		}
		// Add The CORE Language Phrases Now
			list($userErrors,$userNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$languageItem['user_phrases']);
			$data->output['userErrors'] += $userErrors;
			$data->output['userNew'] += $userNew;
			
			list($adminErrors,$adminNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$languageItem['admin_phrases'],TRUE);
			$data->output['adminErrors'] += $adminErrors;
			$data->output['adminNew'] += $adminNew;
			
		$data->output['themeOverride'] = 'NewLanguageSuccess';
	}
}

function languages_admin_newlanguage_content($data){
	if(isset($data->output['responseMessage'])) echo '<h2>'.$data->output['responseMessage'].'</h2>';
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesNewLanguage($data);
	}
}
?>