<?php

function modules_config($data,$db) {
	if ($data->user['userLevel']>=USERLEVEL_WRITER) {
		$data->admin['menu'][]=array(
			'category'	=> 'CMS Settings',
			'command' 	=> 'modules',
			'name'			=> 'Modules',
			'sortOrder' => 4
		);
	}
}

?>