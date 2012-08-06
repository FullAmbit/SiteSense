<?php

function language_admin_savePhrases($data,$db,$languageShortName,$moduleName,$modulePhrases){
	$moduleShortName = (isset($data->output['moduleShortName'][$moduleName])) ? $data->output['moduleShortName'][$moduleName] : '';
	switch($_POST['action']){
		case 0:
			// Clear Table And Start Fresh
			$statement=$db->prepare("deletePhrasesByModuleAndLanguage","admin_languages",array("!lang!"=>$languageShortName));
			$statement->execute(array(
				':module' => $moduleShortName
			));
			// Put In The New Phrases
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageShortName));
			foreach($modulePhrases as $phrase => $text){
				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName
				));
				if($result == FALSE){
					$data->output['responseMessage'] = 'There was an error while inserting the phrases. It aborted at: '.$phrase.' for the module '.$moduleName;
					return FALSE;
				}
			}
		break;
		case 1:
			// Install Non-Existing Phrases, Ignore Pre-Existing Ones
			$statement = $db->prepare('getPhrasesByModule','admin_languages',array("!lang!"=>$languageShortName));
			$statement->execute(array(
				':module' => $moduleShortName
			));
			$existingModuleList = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

			// Put In The New Phrases
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array("!lang!"=>$languageShortName));
			foreach($modulePhrases as $phrase => $text){
				if(isset($existingModuleList[$phrase])) continue; // Ignore Pre-Existing One

				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName
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
				':module' => $moduleShortName
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
						':id' => $existingModuleList[$phrase][0]['id']
					));
				}else{
					// insert New One
					$result = $insert->execute(array(
						':phrase' => $phrase,
						':text' => $text,
						':module' => $moduleShortName
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