<?php

function languages_admin_list_build($data,$db){
	// Get Installed Languages
	$statement = $db->query("getAllLanguages","admin_languages");
	$statement->execute();
	$data->output['languageList'] = $statement->fetchAll(PDO::FETCH_ASSOC);
}

function languages_admin_list_content($data){
	theme_languagesList($data);
}
?>