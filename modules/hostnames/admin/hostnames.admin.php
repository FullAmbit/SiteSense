<?php

function hostnames_admin_buildContent($data,$db){
	if(!checkPermission('access','hostnames',$data)) {
		$data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
			return;
	}
	
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$target='modules/hostnames/admin/includes/hostnames.admin.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		$data->output['function'] = $data->action[2];
		common_include($target);
	}
	$func = 'hostnames_admin_'.$data->action[2].'_build';
	
	if (function_exists($func)) $func($data,$db);
	$data->output['pageTitle']='Hostnames';
}

function hostnames_admin_content($data){
	$func = 'hostnames_admin_'.$data->action[2].'_content';
	if(function_exists($func)) $func($data);
}

?>