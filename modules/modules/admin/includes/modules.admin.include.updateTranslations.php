<?php

common_include('modules/languages/admin/languages.admin.common.php');

function admin_modulesBuild($data,$db){

	// Get The Module
	$statement = $db->prepare('getModuleById','admin_modules');
	$statement->execute(array(
		':id' => $data->action[3]
	));
	if(($data->output['moduleItem']=$statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride']='NotFound';
		return;
	}
	
	$moduleName = $data->output['moduleItem']['name'];
	$_POST['action'] = 5; // Update ONLY Ones That Are Still Defaulted To English and Install Missing Phrases.
	
	foreach($data->languageList as $languageItem){					
		// Handle The English Phrases (User End AND Admin End)
		$data->output['userErrors']=$data->output['adminErrors']=$data->output['userNew']=$data->output['adminNew']=array();
		$coreAdminPhrases=$coreUserPhrases=$userPhrases=$adminPhrases=array();
		//---User Phrases
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
		// Update Core User Phrases
		list($userErrors,$userNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreUserPhrases);
		$data->output['userErrors'] += $userErrors;
		$data->output['userNew'] += $userNew;
		// Update Module-Specific User Phrases
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
		// Update Core Admin Phrases
		list($adminErrors,$adminNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],'',$coreAdminPhrases,TRUE);
		$data->output['adminErrors'] += $adminErrors;
		$data->output['adminNew'] += $adminNew;	
		// Update Module-Specific Admin Phrases
		list($adminErrors,$adminNew) = language_admin_savePhrases($data,$db,$languageItem['shortName'],$moduleName,$adminPhrases,TRUE);
		$data->output['adminErrors'] += $adminErrors;
		$data->output['adminNew'] += $adminNew;	

		$data->output['themeOverride'] = 'UpdateTranslationSuccess';
	}
}

function admin_modulesShow($data){
	if(isset($data->output['responseMessage'])) echo '<h2>'.$data->output['responseMessage'].'</h2>';
	if(isset($data->output['themeOverride'])){
		$func = 'theme_modules'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_modulesUpdateTranslation($data);
	}
}
?>