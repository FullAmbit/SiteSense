<?php
common_include('libraries/forms.php');
function languages_admin_editphrase_build($data,$db){
	// Get Language Item
	$statement=$db->prepare('getLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => $data->action[3]
	));
	if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	// Get Phrase Item
	$statement=$db->prepare('getPhraseByLanguageAndId','admin_languages',array('!lang!'=>$data->output['languageItem']['shortName']));
	$statement->execute(array(
		':id' => $data->action[4]
	));
	if(($data->output['phraseItem']=$statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride']='PhraseNotFound';
		return;
	}
	
	$data->output['phraseForm'] = new formHandler('phraseItem',$data,true);
	if(isset($_POST['fromForm']) && $_POST['fromForm'] == $data->output['phraseForm']->fromForm){
		if($data->output['phraseForm']->validateFromPost()){
			// Populate Post Data
			$data->output['phraseForm']->populateFromPostData();
			// Check To See If Phrase Exists Already But Only If Different Than Before
			if($data->output['phraseForm']->sendArray[':phrase'] !== $data->output['phraseItem']['phrase']){
				$statement = $db->prepare('getPhraseByLanguageAndModule','admin_languages',array('!lang!'=>$data->output['languageItem']['shortName']));
				$statement->execute(array(
					':phrase' => $data->output['phraseForm']->sendArray[':phrase'],
					':module' => $data->output['phraseForm']->sendArray[':module']
				));
				if($statement->fetch() !== FALSE){
					// Throw Form Error, Phrase Taken.
					$data->output['phraseForm']->error = true;
					$data->output['phraseForm']->fields['phrase']['error'] = true;
					$data->output['phraseForm']->fields['phrase']['errorList'][] = 'The phrase you specified already exists for this module.';
					return;
				}
			}
			// Save To Database
			$statement = $db->prepare('updatePhraseByLanguage','admin_languages',array('!lang!'=>$data->output['languageItem']['shortName']));
			$result = $statement->execute(array(
				':id' => $data->output['phraseItem']['id'],
				':phrase' => $data->output['phraseForm']->sendArray[':phrase'],
				':text' => $data->output['phraseForm']->sendArray[':text'],
				':module' => $data->output['phraseForm']->sendArray[':module']
			));
			if($result){
				$data->output['themeOverride'] = 'EditPhraseSuccess';
			}else{
				$data->output['responseMessage'] = 'There was an error in saving to the database.';
			}			
		}
	}
}

function languages_admin_editphrase_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesEditPhrase($data);
	}
}