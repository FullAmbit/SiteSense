<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

common_include('modules/languages/admin/languages.admin.common.php');

function admin_modulesBuild($data,$db){
	if(!checkPermission('enable','modules',$data)) {
		$data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	if(!$data->action[3]) {
		$data->output['rejectError']=$data->phrases['modules']['insufficientParameter'];
		$data->output['rejectText']=$data->phrases['modules']['insufficientParameterModuleName'];
	} else {
		$statement=$db->prepare('getModuleByShortName','admin_modules');
		$statement->execute(
			array(
				':shortName' => $data->action[3]
			)
		);
		$result=$statement->fetch();
		if(empty($result)) {
			$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
			$data->output['rejectText']=$data->phrases['modules']['errorModuleNotFound'];
		}
		$name=$result['name'];
		// Include the install file for this module
		$targetInclude='modules/'.$name.'/'.$name.'.install.php';
		if(!file_exists($targetInclude)) {
			$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
			$data->output['rejectText']=$data->phrases['modules']['errorInstallFileNotFound'];
		} else {
			common_include($targetInclude);
			// Get the module's settings
			$targetFunction=$name.'_settings';
			if(!function_exists($targetFunction)) {
				$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
				$data->output['rejectText']=$data->phrases['modules']['errorSettingsFunctionNotFound'];
			} else {
				$moduleSettings=$targetFunction($db);
				// Update this module in the database
				if (!isset($moduleSettings['version'])) {
					$moduleSettings['version'] = '0.0';
				}
				$statement=$db->prepare('updateModule','admin_modules');
				$statement->execute(
					array(
						':name' => $moduleSettings['name'],
						':shortName' => $moduleSettings['shortName'],
						':version' => $moduleSettings['version'],
						':enabled' => 1
					)
				);
				// Run the module installation procedure
				$db->loadCommonQueryDefines(true);
				$targetFunction=$name.'_install';
				if(!function_exists($targetFunction)) {
					$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
					$data->output['rejectText']=$data->phrases['modules']['errorInstallFunctionNotFound'];
				} else {
					// Install Tables For Each Language
					foreach($data->languageList as $languageItem) {
						$targetFunction($db,false,TRUE,$languageItem['shortName']);
					}
				}
				$languageFileList = glob("modules/".$name."/languages/".$name.".phrases.*.php");
				$adminLanguageFileList = glob("modules/".$name."/admin/languages/".$name.".admin.phrases.*.php");
				
				// Install Language Phrases For Module (USER END)
				foreach($languageFileList as $languageFile){
					$matches = array();
					if(preg_match("/(.*?)\/(.*?)\/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$languageFile,$matches) === 0) continue;
					$languageShortName = $matches[4].'_'.$matches[5];
					
					common_include($languageFile); // Modular Language File
					
					// Check If Language Is Installed In Database...
					$statement=$db->prepare('getLanguage','admin_languages');

					if(!isset($data->languageList[$languageShortName])) continue;
					$data->output['languageExistsError']=FALSE;

					// Get Phrases For This Module
					$func = 'languages_'.$name.'_'.$languageShortName;
					if(!function_exists($func)) continue;
					
					$phrases = $func();
					// Check To See If We Have Any Of These Phrases In The Database Already For This Module
					// Because If We Do, We'll Need User Input To Decide What Action To Take.
					$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
					$statement->execute(array(
						':module' => $result['shortName'],
						':isAdmin' => 0
					));
					$existingModulePhraseList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
					
					$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
					$statement->execute(array(
						':module' => '',
						':isAdmin' => 0
					));
					$existingCorePhraseList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
					
					// Check Core Phrases
					if(isset($phrases['core']) && is_array($phrases['core'])){
						foreach($phrases['core'] as $phrase => $text){
							if(isset($existingCorePhraseList[$phrase])){
								$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
								$data->output['rejectText'] = $data->phrases['modules']['errorExistingPhrasesFound'].'&nbsp;<a href="'.$data->linkRoot.'admin/modules/languages/'.$result['id'].'">'.$data->phrases['modules']['linkSelectLanguageAction'].'</a>';
								return;
							}
						}
						$corePhrases = $phrases['core'];
						unset($phrases['core']);
					}
					
					// Check Modular Phrases
					foreach($phrases as $phrase => $text){
						if(isset($existingModulePhraseList[$phrase])){
							$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
							$data->output['rejectText'] = $data->phrases['modules']['errorExistingPhrasesFound'].'&nbsp;<a href="'.$data->linkRoot.'admin/modules/languages/'.$result['id'].'">'.$data->phrases['modules']['linkSelectLanguageAction'].'</a>';
							return;
						}
					}
						
					if($data->output['languageExistsError']==FALSE){
						
						// Put In The New Core Phrases
						$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageItem['shortName']));
						if(isset($corePhrases)){
							foreach($corePhrases as $phrase => $text){
								$result = $statement->execute(array(
									':phrase' => $phrase,
									':text' => $text,
									':module' => '',
									':isAdmin' => 0
								));
								if(!$result){
									$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
									$data->output['rejectText'] = 'There was an error adding the phrases for the language '.$languageItem['name'].'.';
									return;
								}
							}
						}
						
						// Put In Modular Phrases
						foreach($phrases as $phrase => $text){
							$result = $statement->execute(array(
								':phrase' => $phrase,
								':text' => $text,
								':module' => $result['shortName'],
									':isAdmin' => 0
							));
							if(!$result){
								$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
								$data->output['rejectText'] = 'There was an error adding the phrases for the language '.$languageItem['name'].'.';
								return;
							}
						}
					}
				}
				
				// Install Language Phrases For Module (ADMIN END)
				foreach($adminLanguageFileList as $languageFile){	
					$matches = array();
					if(preg_match("/(.*?)\/(.*?)\/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$languageFile,$matches) === 0) continue;
					$languageShortName = $matches[4].'_'.$matches[5];
					
					common_include($languageFile); // Modular Language File
					
					// Check If Language Is Installed In Database...
					$statement=$db->prepare('getLanguage','admin_languages');

					if(!isset($data->languageList[$languageShortName]));
					$data->output['languageExistsError']=FALSE;

					// Get Phrases For This Module
					$func = 'languages_'.$name.'_admin_'.$languageShortName;
					if(!function_exists($func)) continue;
					
					$phrases = $func();
					// Check To See If We Have Any Of These Phrases In The Database Already For This Module
					// Because If We Do, We'll Need User Input To Decide What Action To Take.
					$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
					$statement->execute(array(
						':module' => $result['shortName'],
						':isAdmin' => 1
					));
					$existingModulePhraseList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
					
					$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
					$statement->execute(array(
						':module' => '',
						':isAdmin' => 1
					));
					$existingCorePhraseList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
					// Check Core Phrases
					if(isset($phrases['core']) && is_array($phrases['core'])){
						foreach($phrases['core'] as $phrase => $text){
							if(isset($existingCorePhraseList[$phrase])){
								$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
								$data->output['rejectText'] = $data->phrases['modules']['errorExistingPhrasesFound'].'&nbsp;<a href="'.$data->linkRoot.'admin/modules/languages/'.$result['id'].'">'.$data->phrases['modules']['linkSelectLanguageAction'].'</a>';
								return;
							}
						}
						$corePhrases = $phrases['core'];
						unset($phrases['core']);
					}
					
					// Check Modular Phrases
					foreach($phrases as $phrase => $text){
						if(isset($existingModulePhraseList[$phrase])){
							$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
							$data->output['rejectText'] = $data->phrases['modules']['errorExistingPhrasesFound'].'&nbsp;<a href="'.$data->linkRoot.'admin/modules/languages/'.$result['id'].'">'.$data->phrases['modules']['linkSelectLanguageAction'].'</a>';
							return;
						}
					}
						
					if($data->output['languageExistsError']==FALSE){
											
						// Put In The New Core Phrases
						$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageItem['shortName']));
						if(isset($corePhrases)){
							foreach($corePhrases as $phrase => $text){
								$result = $statement->execute(array(
									':phrase' => $phrase,
									':text' => $text,
									':module' => '',
									':isAdmin' => 1
								));
								if(!$result){
									$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
									$data->output['rejectText'] = 'There was an error adding the phrases for the language '.$languageItem['name'].'.';
									return;
								}
							}
						}
						
						// Put In Modular Phrases
						foreach($phrases as $phrase => $text){
							$result = $statement->execute(array(
								':phrase' => $phrase,
								':text' => $text,
								':module' => $result['shortName'],
									':isAdmin' => 1
							));
							if(!$result){
								$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
								$data->output['rejectText'] = 'There was an error adding the phrases for the language '.$languageItem['name'].'.';
								return;
							}
						}
					}
				}
			}
		}
	}
}
function admin_modulesShow($data) {
	if (empty($data->output['rejectError'])) {
		theme_modulesInstallSuccess($data);
	} else {
		theme_rejectError($data);
	}
}
?>