<?php

function admin_buildContent($data,$db) {

	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$target='admin/modules.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_modulesBuild')) admin_modulesBuild($data,$db);
	$data->output['pageTitle']='Modules';
}

function admin_content($data) {
	if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_modulesShow($data);
		} else admin_unknown();
	}
}
?>