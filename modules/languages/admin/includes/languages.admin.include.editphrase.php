<?php
common_include('libraries/forms.php');
function languages_admin_editphrase_build($data,$db){
	if(!checkPermission('editPhrase','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	// Get The English Phrase
	$statement=$db->prepare('getPhraseByLanguageAndId','admin_languages',array('!lang!'=>"en_us"));
	$statement->execute(array(
			':id' => $data->action[3]
	));
	$data->output['phraseItem']["en_us"]=$enPhraseItem=$statement->fetch(PDO::FETCH_ASSOC);	
	// Get This Phrase For All Other Languages
	foreach($data->languageList as $languageItem){
		if($languageItem['shortName']=='en_us') continue;
		// Get Phrase Item
		$statement=$db->prepare('getPhraseByUniqueParams','admin_languages',array('!lang!'=>$languageItem['shortName']));
		$statement->execute(array(
			':module' => $enPhraseItem['module'],
			':phrase' => $enPhraseItem['phrase'],
			':isAdmin' => $enPhraseItem['isAdmin']
		));
		
		$data->output['phraseItem'][$languageItem['shortName']] = $statement->fetch(PDO::FETCH_ASSOC);
	}
		
	$data->output['phraseForm'] = new formHandler('phraseItem',$data,true);
	if(isset($_POST['fromForm']) && $_POST['fromForm'] == $data->output['phraseForm']->fromForm){
		if($data->output['phraseForm']->validateFromPost()){
			// Populate Post Data
			$data->output['phraseForm']->populateFromPostData();
			// Check To See If Phrase Exists Already But Only If Different Than Before
			if($data->output['phraseForm']->sendArray[':phrase'] !== $enPhraseItem['phrase']){
				$found = common_checkUniqueValueAcrossLanguages($data,$db,'languages_phrases','id',array(
					'phrase' => $data->output['phraseForm']->sendArray[':phrase'],
					'module' => $data->output['phraseForm']->sendArray[':module'],
					'isAdmin' => $data->output['phraseForm']->sendArray[':isAdmin'],
				));
				if($found){
					// Throw Form Error, Phrase Taken.
					$data->output['phraseForm']->error = true;
					$data->output['phraseForm']->fields['phrase']['error'] = true;
					$data->output['phraseForm']->fields['phrase']['errorList'][] = $data->phrases['languages']['phraseExistsForModule'];
					return;
				}
			}
			// Save To Database (For Each Language)
			foreach($data->languageList as $languageItem){
				$statement = $db->prepare('updatePhraseByLanguageOverride','admin_languages',array('!lang!'=>$languageItem['shortName']));
				$result = $statement->execute(array(
					':id' => $data->output['phraseItem'][$languageItem['shortName']]['id'],
					':phrase' => $data->output['phraseForm']->sendArray[':phrase'],
					':text' => $data->output['phraseForm']->sendArray[':text_'.$languageItem['shortName']],
					':module' => $data->output['phraseForm']->sendArray[':module'],
					':isAdmin' => $data->output['phraseForm']->sendArray[':isAdmin'],
					':override' => $data->output['phraseForm']->sendArray[':override']
				));
			}
			if($result){
				$data->output['themeOverride'] = 'EditPhraseSuccess';
			}else{
				$data->output['responseMessage'] = $data->phrases['languages']['saveToDBError'];
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