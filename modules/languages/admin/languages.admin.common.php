<?php

function language_admin_savePhrases($data,$db,$languageShortName,$moduleName,$modulePhrases,$isAdmin = FALSE){
	$moduleShortName = (isset($data->output['moduleShortName'][$moduleName])) ? $data->output['moduleShortName'][$moduleName] : '';
	switch($_POST['action']){
		case 0:
			/**
			DEPRECATED...A Truncate Is Run In Language.update.php
			// Clear Table And Start Fresh ONLY If Not A Core Module, Else Previous Core Entries From Other Modules Will Be Erased.
			// The initial erase is handled in the language update file.
			if($moduleName = ''){
				$statement=$db->prepare("deletePhrasesByModuleAndLanguage","admin_languages",array("!lang!"=>$languageShortName));
				$statement->execute(array(
					':module' => $moduleShortName
				));
			}
			**/
			
			// Put In The New Phrases
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageShortName));
			foreach($modulePhrases as $phrase => $text){
				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
				if($result == FALSE){
					$data->output['responseMessage'] = 'There was an error while inserting the phrases. It aborted at: '.$phrase.' for the module '.(($moduleName == '') ? 'core' : $moduleName);
					return FALSE;
				}
			}
		break;
		case 1:
			// Install Non-Existing Phrases, Ignore Pre-Existing Ones
			$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
			$statement->execute(array(
				':module' => $moduleShortName,
				':isAdmin' => $isAdmin
			));
			$existingModuleList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

			// Put In The New Phrases
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageShortName));
			foreach($modulePhrases as $phrase => $text){
				if(isset($existingModuleList[$phrase])) continue; // Ignore Pre-Existing One

				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
				
				if($result == FALSE){
					$data->output['responseMessage'] = 'There was an error while inserting the phrases. It aborted at: '.$phrase.' for the module '.$moduleName;
					return FALSE;
				}
			}
		break;
		case 2:
			// Update Existing Ones, Install New Ones
			$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
			$statement->execute(array(
				':module' => $moduleShortName,
				':isAdmin' => 1
			));
			$existingModuleList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

			// Put In The New Phrases
			$insert = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageShortName));
			$update = $db->prepare('updatePhraseByLanguage','admin_languages',array("!lang!"=>$languageShortName));
			foreach($modulePhrases as $phrase => $text){
				if(isset($existingModuleList[$phrase])){
					// Update old one
					$result = $update->execute(array(
						':phrase' => $phrase,
						':text' => $text,
						':module' => $moduleShortName,
						':id' => $existingModuleList[$phrase][0]['id'],
						':isAdmin' => $isAdmin
					));
				}else{
					// insert New One
					$result = $insert->execute(array(
						':phrase' => $phrase,
						':text' => $text,
						':module' => $moduleShortName,
						':isAdmin' => $isAdmin
					));
				}
				
				if($result == FALSE){
					$data->output['responseMessage'] = 'There was an error while saving the phrases. It aborted at: '.$phrase.' for the module '.$moduleName;
					return FALSE;
				}
			}

		break;
		default:
			$data->output['responseMessage'] = 'The action you specified was invalid.';
			return FALSE;
		break;
	}
}

?>