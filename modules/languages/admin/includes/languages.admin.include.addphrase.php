<?php
common_include('libraries/forms.php');
function languages_admin_addphrase_build($data,$db){
	// Get Language Item
	$statement=$db->prepare('getLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => $data->action[3]
	));
	if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	
	$data->output['phraseForm'] = new formHandler('phraseItem',$data,true);
	if(isset($_POST['fromForm']) && $_POST['fromForm'] == $data->output['phraseForm']->fromForm){
		if($data->output['phraseForm']->validateFromPost()){
			// Populate Post Data
			$data->output['phraseForm']->populateFromPostData();
			// Check To See If Phrase Exists Already
			$statement = $db->prepare('getPhraseByLanguageAndModule','admin_languages',array('!lang!'=>$data->output['languageItem']['shortName']));
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
			// Save To Database
			$statement = $db->prepare('addPhraseByLanguage','admin_languages',array('!lang!'=>$data->output['languageItem']['shortName']));
			$result = $statement->execute($data->output['phraseForm']->sendArray);
			if($result){
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