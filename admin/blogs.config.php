<?php

function blogs_config($data,$db) {
	if ($data->user['userLevel']>=USERLEVEL_WRITER) {
		$data->admin['menu'][]=array(
			'category'	=> 'CMS Settings',
			'command' 	=> 'blogs/list',
			'name'			=> 'Blogs',
			'sortOrder' => 3
		);
	}
}

?>