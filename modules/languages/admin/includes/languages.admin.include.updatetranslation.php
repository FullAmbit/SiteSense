<?php

common_include('modules/languages/admin/languages.admin.common.php');

function languages_admin_updatetranslation_build($data,$db){
	// Get Language Item
	$statement = $db->prepare('getLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => $data->action[3]
	));
	if(($data->output['languageItem'] = $languageItem = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	
	// Get The Core File
	$coreFileTarget = 'modules/languages/phrases/languages.phrases.'.$languageItem['shortName'].'.php';
	if(!file_exists($coreFileTarget)){
		$data->output['themeOverride']='NotFound';
		return;	
	}
	
	if(isset($_POST['install'])){
		$_POST['action'] = 3; // Update ONLY.
		common_include($coreFileTarget);
		$corePhraseFunc = 'languages_core_'.$languageItem['shortName'];
		$coreLanguage = $corePhraseFunc();
	
		// Save Admin & User-End Core Phrases
		$data->output['userErrors']=$data->output['adminErrors']=array();
		
		$data->output['userErrors'] += language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreLanguage['user_phrases']);
		$data->output['adminErrors'] += language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreLanguage['admin_phrases'],TRUE);

		// Now Are We Handling Modules??
		if(isset($_POST['updateModules']) && $_POST['updateModules']=='1') {
			// Move Langauges, Sidebars, and Modules To Head Of Array As They Have Priority
			$temp=array('languages' => 'languages','sidebars' => 'sidebars','modules' => 'modules');
			unset($data->output['moduleShortName']['sidebars'],$data->output['moduleShortName']['languages'],$data->output['moduleShortName']['modules']);
			$data->output['moduleShortName']=$temp+$data->output['moduleShortName'];
			
			foreach($data->output['moduleShortName'] as $moduleName => $moduleShortName){
				// For Each Module Now..Handle The English Phrases (User End AND Admin End)
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
							$data->output['userErrors'] += language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreUserPhrases);
						}
						$data->output['userErrors'] += language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$userPhrases);
					}
				}
				
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
							$data->output['adminErrors'] += language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreAdminPhrases,TRUE);
						}
						
						$data->output['adminErrors'] += language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$adminPhrases,TRUE);
					}
				}
			}
			
			$data->output['themeOverride'] = 'UpdateTranslationSuccess';
		}
	}
}

function languages_admin_updatetranslation_content($data){
	if(isset($data->output['responseMessage'])) echo '<h2>'.$data->output['responseMessage'].'</h2>';
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesUpdateTranslation($data);
	}
}
?>