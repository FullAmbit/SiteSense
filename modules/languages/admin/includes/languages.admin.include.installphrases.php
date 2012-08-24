<?php

common_include('modules/languages/admin/languages.admin.common.php');

function languages_admin_installphrases_build($data,$db){
	if(!checkPermission('installphrases','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	
	// Get The English Core File
	$coreFileTarget = 'modules/languages/phrases/languages.phrases.en_us.php';
	if(!file_exists($coreFileTarget)){
		$data->output['themeOverride']='EnglishNotFound';
		return;	
	}	
	if(isset($_POST['install'])){
		
		common_include($coreFileTarget);
		$corePhraseFunc = 'languages_core_en_us';
		$coreLanguage = $corePhraseFunc();
		
		if($_POST['action']==0){
			// Empty Table And Prep For Phrase Entry
			$statement=$db->query("truncatePhrases","admin_languages",array("!lang!"=>"en_us"));
		}
	
		// Save Admin & User-End Core Phrases
		if((language_admin_savePhrases($data,$db,"en_us",'',$coreLanguage['user_phrases']) === FALSE) ||
		   (language_admin_savePhrases($data,$db,"en_us",'',$coreLanguage['admin_phrases'],TRUE) === FALSE)){
		   	$data->output['themeOverride'] = 'InstallPhrasesEnglishError';
		   	return;
		}
		
		// Now Loop Through All Other Languages and only install phrases that DO NOT EXIST. This will keep phrases consistent between language tables.
		$originalAction = $_POST['action'];
		$_POST['action'] = 1;
		foreach($data->languageList as $languageItem){
			if($languageItem['shortName'] == 'en_us') continue;
			
			language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreLanguage['user_phrases']);
			language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreLanguage['admin_phrases'],TRUE);
		}
		
		// -- Now Handle Modular Phrases
		if(isset($_POST['updateModules']) && $_POST['updateModules']=='1') {
			// Move Langauges, Sidebars, and Modules To Head Of Array As They Have Priority
			$temp=array('languages' => 'languages','sidebars' => 'sidebars','modules' => 'modules');
			unset($data->output['moduleShortName']['sidebars'],$data->output['moduleShortName']['languages'],$data->output['moduleShortName']['modules']);
			$data->output['moduleShortName']=$temp+$data->output['moduleShortName'];
						
			foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
				$coreUserPhrases=$userPhrases=$coreAdminPhrases=$adminPhrases=array();
				$_POST['action'] = $originalAction;
				
				// For Each Module Now..Handle The English Phrases (User End AND Admin End)
				$userEndTarget = 'modules/'.$moduleName.'/languages/'.$moduleName.'.phrases.en_us.php';
				if(file_exists($userEndTarget)){
					common_include($userEndTarget);
					$userEndFunc = 'languages_'.$moduleName.'_en_us';
					if(function_exists($userEndFunc)){
						$userPhrases = $userEndFunc();
						// Do We Have Additional Core Phrases?...
						if(isset($userPhrases['core']) && is_array($userPhrases['core'])){
							$coreUserPhrases = $userPhrases['core'];
							unset($userPhrases['core']);
						}
					}
				}
				
				language_admin_savePhrases($data,$db,"en_us",'',$coreUserPhrases);
				language_admin_savePhrases($data,$db,"en_us",$moduleName,$userPhrases);
				
				//--Admin Phrases
				$adminEndTarget = 'modules/'.$moduleName.'/admin/languages/'.$moduleName.'.admin.phrases.en_us.php';
				if(file_exists($adminEndTarget)){
					common_include($adminEndTarget);
					$adminEndFunc = 'languages_'.$moduleName.'_admin_en_us';
					if(function_exists($adminEndFunc)){
						$adminPhrases = $adminEndFunc();
						if(is_array($adminPhrases['core'])){
							$coreAdminPhrases = $adminPhrases['core'];
							unset($adminPhrases['core']);
						}
						language_admin_savePhrases($data,$db,"en_us",$moduleName,$adminPhrases,TRUE);
					}
				}
				
				language_admin_savePhrases($data,$db,"en_us",'',$coreAdminPhrases,TRUE);
				language_admin_savePhrases($data,$db,"en_us",$moduleName,$adminPhrases,TRUE);
				
				//---Now Loop Through All OTHER Languages and Install New Phrases
				$_POST['action'] = 1;
				foreach($data->languageList as $languageItem){
					if($languageItem['shortName'] == 'en_us') continue;

					// Save User-End Core Phrases And Module Phrases
					if(isset($coreUserPhrases)) language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreUserPhrases);
					if(isset($userPhrases)) language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$userPhrases);
					
					// Save Admin-End Core Phrases And Module Phrases
					if(isset($coreAdminPhrases)) language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreAdminPhrases,TRUE);
					if(isset($adminPhrases)) language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$adminPhrases,TRUE);
				}
			}
			
			$data->output['responseMessage'] = 'Phrases were installed for the English language. Any phrases not-existing for other languages were also added with the default English value.';
		}
	}
}

function languages_admin_installphrases_content($data){
	if(isset($data->output['responseMessage'])) echo '<h2>'.$data->output['responseMessage'].'</h2>';
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesInstallPhrases($data);
	}
}

?>