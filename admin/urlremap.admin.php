<?php

function admin_buildContent($data,$db) {
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	if ($data->action[2]=='list') {
		$statement=$db->query('getAllUrlRemaps','admin_urlremap');
		$data->output['urlremapList']=$statement->fetchAll();
	}
	$target='admin/urlremap.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_urlremapsBuild')) admin_urlremapsBuild($data,$db);
	$data->output['pageTitle']='URL Remaps';
}

function admin_content($data) {
	if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_urlremapsShow($data);
		} else admin_unknown();
	}
}
?>