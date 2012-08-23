<?php
common_include('libraries/forms.php');
function languages_admin_addphrase_build($data,$db){
	if(!checkPermission('addPhrase','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	
	$data->output['phraseForm'] = new formHandler('phraseItem',$data,true);
	if(isset($_POST['fromForm']) && $_POST['fromForm'] == $data->output['phraseForm']->fromForm){
		if($data->output['phraseForm']->validateFromPost()){
			// Populate Post Data
			$data->output['phraseForm']->populateFromPostData();
			
			// Check To See If Phrase Exists Already In The English Language
			$statement = $db->prepare('getPhraseByLanguageAndModule','admin_languages',array('!lang!'=>'en_us'));
			$statement->execute(array(
				':phrase' => $data->output['phraseForm']->sendArray[':phrase'],
				':module' => $data->output['phraseForm']->sendArray[':module']
			));
			
			if($statement->fetch() !== FALSE){
				// Throw Form Error, Phrase Taken.
				$data->output['phraseForm']->error = true;
				$data->output['phraseForm']->fields['phrase']['error'] = true;
					$data->output['phraseForm']->fields['phrase']['errorList'][] = $data->phrases['languages']['phraseExistsForModule'];
				return;
			}
			$error = FALSE;
			// Add This Phrase For All The Languages
			foreach($data->languageList as $languageItem){
				$statement = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$languageItem['shortName']));
				$result = $statement->execute(array(
					':isAdmin' => 0,
					':phrase' => $data->output['phraseForm']->sendArray[':phrase'],
					':text' => (isset($data->output['phraseForm']->sendArray[':text_'.$languageItem['shortName']]{1})) ? $data->output['phraseForm']->sendArray[':text_'.$languageItem['shortName']] : $data->output['phraseForm']->sendArray[':text_en_us'],
					':module' => $data->output['phraseForm']->sendArray[':module']
				));
				if($result == FALSE){
					$error = TRUE;
					$data->output['phraseForm']->fields['text_'.$languageItem['shortName']]['error'] = true;
					$data->output['phraseForm']->fields['text_'.$languageItem['shortName']]['errorList'][] = "There was an error in saving this translation.";
				}
			}
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>'en_us'));

			if($error===FALSE){
				$data->output['themeOverride'] = 'AddPhraseSuccess';
			}else{
				$data->output['responseMessage'] = $data->phrases['languages']['saveToDBError'];
			}			
		}
	}
}

function languages_admin_addphrase_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesAddPhrase($data);
	}
}