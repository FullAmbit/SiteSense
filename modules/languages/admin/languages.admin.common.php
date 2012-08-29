<?php
function language_admin_savePhrases($data,$db,$languageShortName,$moduleName,$modulePhrases,$isAdmin = FALSE){
	$moduleShortName = (isset($data->output['moduleShortName'][$moduleName])) ? $data->output['moduleShortName'][$moduleName] : '';
	switch($_POST['action']){
		case 0:
			// Put In The New Phrases
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$languageShortName));
			if(!is_array($modulePhrases)) break;
			foreach($modulePhrases as $phrase => $text){
				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
			}
		break;
		case 1:
			if(empty($modulePhrases)) break;
			// Put In The New Phrases
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$languageShortName));
			$check = $db->prepare('getPhraseByUniqueParams','admin_languages',array('!lang!'=>$languageShortName));
			foreach($modulePhrases as $phrase => $text){
				// Check If Phrase Exists Already For This Module By IsAdmin
				$check->execute(array(
					':phrase' => $phrase,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
				if($check->fetch()!==FALSE) continue;
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
			$existingModuleList = languages_admin_getPhrasesByModule($db,$isAdmin,$moduleShortName,$languageShortName);
			// Put In The New Phrases
			$insert = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$languageShortName));
			$update = $db->prepare('updatePhraseByLanguage','admin_languages',array('!lang!'=>$languageShortName));
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
		case 3:
			$returnList = array();
			// Only Update Existing Phrases (Phrases Must Exist In English Language) Return List Of Phrases That Do Not Exist
			$existingModuleList = languages_admin_getPhrasesByModule($db,$isAdmin,$moduleShortName);
			$statement = $db->prepare('updatePhraseTextByLanguage','admin_languages',array('!lang!' => $languageShortName));
			foreach($modulePhrases as $phrase => $text){
				// If No English Source Found...Skip
				if(!isset($existingModuleList[$phrase])){				
					$returnList[(($moduleShortName=='') ? "core" : $moduleShortName)][] = $phrase;
					continue;
				}
				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
				if($result == false) var_dump($statement->errorInfo());
			}
		
			return $returnList;
		break;
		case 4:
			$errorList=$newList=array();
			// Add All Phrases Supplied That Have An English CounterPart
			// Also Add Any Missing Phrases That Are Found In English But Not Here
			$englishPhraseList = languages_admin_getPhrasesByModule($db,$isAdmin,$moduleShortName);
			$statement = $db->prepare('insertOrUpdatePhrase','admin_languages',array('!lang!' => $languageShortName));
			foreach($modulePhrases as $phrase => $text){
				// If No English Source Found...Skip
				if(!isset($englishPhraseList[$phrase])){
					$errorList[(($moduleShortName=='') ? "core" : $moduleShortName)][] = $phrase;
					continue;
				}
				$result = $statement->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
				if($result == false) var_dump($statement->errorInfo());
				unset($englishPhraseList[$phrase]);
			}
			// All The Remaining Existing Module Phrases Need To Be Added Into This Language Now
			$insert = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$languageShortName));
			foreach($englishPhraseList as $phrase => $text){
				$text = $text[0]['text'];
				$newList[(($moduleShortName=='') ? "core" : $moduleShortName)][] = $phrase;
				$insert->execute(array(
					':phrase' => $phrase,
					':text' => $text,
					':module' => $moduleShortName,
					':isAdmin' => $isAdmin
				));
			}
			return array($errorList,$newList);
		break;
		case 5:
			// Only Install New Phrases (But Make Sure They Have An English CounterPart)
			// Update Any Existing Phrases Equivalen To Their English CounterPart
			$errorList=$newList=array();
		
			// Start By Getting A List Of All English Phrases For This "Module"
			$englishPhraseList = languages_admin_getPhrasesByModule($db,$isAdmin,$moduleShortName);
			
			// Now Get A List Of Existing Phrases For The Current Language Within This Module
			$existingPhraseList = languages_admin_getPhrasesByModule($db,$isAdmin,$moduleShortName,$languageShortName);
			
			$update = $db->prepare('updatePhraseTextByLanguage','admin_languages',array('!lang!'=>$languageShortName));
			$insert = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$languageShortName));
			
			// Loop Through Our Phrases From The Language File Pack
			foreach($modulePhrases as $phrase => $text){
				// Check To See If It Has A English Counter-Part, Skip If It Doesn't.
				if(!isset($englishPhraseList[$phrase])){
					$errorList[(($moduleShortName=='') ? "core" : $moduleShortName)][] = $phrase;
					continue;
				}
				
				// Check To See If It Exists Already, If So Check If Equivalent To English Phrase
				if(isset($existingPhraseList[$phrase])){
					if($existingPhraseList[$phrase][0]['text'] == $englishPhraseList[$phrase][0]['text']){
						// Update To New Value From Language-File System
						$update->execute(array(
							':phrase' => $phrase,
							':text' => $text,
							':module' => $moduleShortName,
							':isAdmin' => $isAdmin
						));
					}
				}else{
					// Does Not Exist..Add New Phrase
					$insert->execute(array(
						':phrase' => $phrase,
						':text' => $text,
						':module' => $moduleShortName,
						':isAdmin' => $isAdmin
					));
					$newList[(($moduleShortName=='') ? "core" : $moduleShortName)][] = $phrase;
				}
			}
			
			return(array($errorList,$newList));
			
		break;
	}
}
function languages_admin_getPhrasesByModule($db,$isAdmin,$module,$lang='en_us') {
	$statement = $db->prepare('getPhrasesByModule','admin_languages',array('!lang!'=>$lang));
	$statement->execute(array(
		':module' => $module,
		':isAdmin' => $isAdmin
	));
	return $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
}
?>