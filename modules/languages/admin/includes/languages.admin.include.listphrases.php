<?php

function languages_admin_listphrases_build($data,$db){
	if(!checkPermission('listPhrases','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	// Get Language Item
	$statement=$db->prepare('getLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => $data->language
	));
	if(($data->output['languageItem'] = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	// Get Phrases
	$statement = $db->prepare('getAllPhrasesByLanguage','admin_languages',array('!lang!'=>$data->language));
	$statement->execute();
	$data->output['phraseList'] = $statement->fetchAll(PDO::FETCH_ASSOC);
}

function languages_admin_listphrases_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_languages'.$data->output['themeOverride'];
		$func($data);
	}else{
		theme_languagesListPhrases($data);
	}
}