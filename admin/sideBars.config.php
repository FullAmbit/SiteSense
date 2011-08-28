<?php

function sideBars_config($data,$db) {
	if ($data->user['userLevel']>=USERLEVEL_WRITER) {
		$data->admin['menu'][]=array(
			'category'	=> 'CMS Settings',
			'command' 	=> 'sideBars/list',
			'name'			=> 'Sidebar',
			'sortOrder' => 3
		);
	}
}

?>