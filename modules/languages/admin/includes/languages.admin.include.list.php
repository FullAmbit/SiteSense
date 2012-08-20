<?php

function languages_admin_list_build($data,$db){
	if(!checkPermission('list','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	// Get Installed Languages
	$statement = $db->query("getAllLanguages","admin_languages");
	$statement->execute();
	$data->output['languageList'] = $statement->fetchAll(PDO::FETCH_ASSOC);
}

function languages_admin_list_content($data){
	theme_languagesList($data);
}
?>