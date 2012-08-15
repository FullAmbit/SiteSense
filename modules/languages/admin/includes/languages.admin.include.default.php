<?php

function languages_admin_default_build($data,$db){
	// Get Language Item
	$statement=$db->prepare('getLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => $data->action[3]
	));
	if(($languageItem = $statement->fetch(PDO::FETCH_ASSOC))==FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	// Check If Default Already
	if($languageItem['isDefault']=='1'){
		$data->output['responseMessage'] = $data->phrases['languages']['alreadyDefault'];
		return;
	}
	// Remove The Other Default Language
	$remove = $statement=$db->query('disableDefaultLanguage','admin_languages');
	if(!$remove){
		$data->output['responseMessage'] = $data->phrases['languages']['disableDefaultError'];
		return;
	}
	// Make This The Default Language
	$result = $statement=$db->prepare('setNewDefaultLanguage','admin_languages');
	$statement->execute(array(
		':shortName' => $languageItem['shortName']
	));
	if(!$result){
		$data->output['responseMessage'] = $data->phrases['languages']['setDefaultError'].' Language: '.$languageItem['name'];
	}else{
		$data->output['responseMessage'] = $data->phrases['languages']['setDefaultSuccess'].' Languages: '.$languageItem['name'];
	}
}

function languages_admin_default_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_language'.$data->output['themeOverride'];
		$func($data);
		return;
	}
	if(!isset($data->output['responseMessage'])) common_redirect($data->linkRoot.'admin/languages/');
	
	echo $data->output['responseMessage'];
}

?>