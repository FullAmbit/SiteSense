<?php

function languages_admin_buildContent($data,$db){
	if(!checkPermission('access','languages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$data->action[2] = strtolower($data->action[2]);
	$target='modules/languages/admin/includes/languages.admin.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		$data->output['function'] = $data->action[2];
		common_include($target);
	}
	$func = 'languages_admin_'.$data->action[2].'_build';
	
	if (function_exists($func)) $func($data,$db);
	$data->output['pageTitle']='languages';
}

function languages_admin_content($data){
	$func = 'languages_admin_'.$data->action[2].'_content';
	if(function_exists($func)){
		$func($data);
	} else{
		echo "The action you specified could not be found.";
	}
}

?>