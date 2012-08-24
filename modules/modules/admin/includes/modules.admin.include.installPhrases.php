<?php

common_include('modules/languages/admin/languages.admin.common.php');

function admin_modulesBuild($data,$db){
	if(!checkPermission('installphrases','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	
	// Get The Module
	$statement = $db->prepare('getModuleById','admin_modules');
	$statement->execute(array(
		':id' => $data->action[3]
	));
	if(($data->output['moduleItem']=$statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride']='NotFound';
		return;
	}
	
	if(isset($_POST['install'])){
	
		if($_POST['action']==0){
			// Empty Table And Prep For Phrase Entry
			$statement=$db->query("truncatePhrases","admin_languages",array("!lang!"=>"en_us"));
		}
		
		$coreUserPhrases=$userPhrases=$coreAdminPhrases=$adminPhrases=array();
		$moduleName = $data->output['moduleItem']['name'];
		
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
			
		$data->output['responseMessage'] = 'Phrases were installed for the English language. Any phrases not-existing for other languages were also added with the default English value.';
	}
}

function admin_modulesShow($data){
	if(isset($data->output['responseMessage'])) echo '<h2>'.$data->output['responseMessage'].'</h2>';
	if(isset($data->output['themeOverride'])){
		$func = 'theme_modules'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_modulesInstallPhrases($data);
	}
}

?>