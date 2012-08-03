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
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
		return;
	}
	if(!$data->action[3]) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No module name was entered to be enabled';
	} else {
		$statement=$db->prepare('getModuleByShortName','admin_modules');
		$statement->execute(
			array(
				':shortName' => $data->action[3]
			)
		);
		$result=$statement->fetch();
		if(empty($result)) {
			$data->output['rejectError']='Error';
			$data->output['rejectText']='Module Not Found';
		}
		$name=$result['name'];
		// Include the install file for this module
		$targetInclude='modules/'.$name.'/'.$name.'.install.php';
		if(!file_exists($targetInclude)) {
			$data->output['rejectError']='Module installation file does not exist';
			$data->output['rejectText']='The module installation could not be found.';
		} else {
			common_include($targetInclude);
			// Get the module's settings
			$targetFunction=$name.'_settings';
			if(!function_exists($targetFunction)) {
				$data->output['rejectError']='Improper installation file';
				$data->output['rejectText']='The module settings function could not be found within the module installation file.';
			} else {
				$moduleSettings=$targetFunction($db);
				// Update this module in the database
				$statement=$db->prepare('updateModule','admin_modules');
				$statement->execute(
					array(
						':name' => $moduleSettings['name'],
						':shortName' => $moduleSettings['shortName'],
						':enabled' => 1
					)
				);
				// Run the module installation procedure
				$db->loadCommonQueryDefines(true);
				$targetFunction=$name.'_install';
				if(!function_exists($targetFunction)) {
					$data->output['rejectError']='Improper installation file';
					$data->output['rejectText']='The module install function could not be found within the module installation file.';
				} else $targetFunction($db);
				
				// Install Language Files For Module
				$languageFileList = glob("modules/".$name."/languages/".$name.".phrases.*.php");
				foreach($languageFileList as $languageFile){
					$matches = array();
					if(preg_match("/(.*?)\/(.*?)\/(.*?).phrases.([a-z]{2})_([a-z]{2}).php/",$languageFile,$matches) === 0) continue;
					
					$languageShortName = $matches[4].'_'.$matches[5];
					
					common_include($languageFile); // Modular Language File
					
					// Check If Language Is Installed In Database...
					$statement=$db->prepare('getLanguage','admin_languages');
					$statement->execute(array(
						':shortName' => $languageShortName
					));
					if(($languageItem = $statement->fetch(PDO::FETCH_ASSOC))==FALSE) continue;
					
					// Create Table For These Languages
					$targetFunction($db,false,$languageShortName);
					
					// Get Phrases For This Module
					$func = 'languages_'.$name.'_'.$languageShortName;
					if(!function_exists($func)) continue;
					
					$modulePhrases = $func();
					// Check To See If We Have Any Of These Phrases In The Database Already For This Module
					// Because If We Do, We'll Need User Input To Decide What Action To Take.
					$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
					$statement->execute(array(
						':module' => $result['shortName']
					));
					$existingPhraseList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
					$data->output['languageExistsError']=FALSE;
					foreach($modulePhrases as $phrase => $text){
						if(isset($existingPhraseList[$phrase])){
							$data->output['rejectError']='Language Installation Error';
							$data->output['rejectText'] = 'Existing phrases were found for the language '.$languageItem['name'].'. Please <a href="'.$data->linkRoot.'admin/modules/languages/'.$result['id'].'">click here</a> to select an action.';
							return;
						}
					}
					if($data->output['languageExistsError']==FALSE){
						// Put In The New Phrases
						$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageItem['shortName']));
						foreach($modulePhrases as $phrase => $text){
							$result = $statement->execute(array(
								':phrase' => $phrase,
								':text' => $text,
								':module' => $result['shortName']
							));
							$data->output['rejectError']='Language Installation Error';
							$data->output['rejectText'] = 'There was an error adding the phrases for the language '.$languageItem['name'].'.';
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