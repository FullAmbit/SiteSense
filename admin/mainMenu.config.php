<?php

function mainMenu_config($data) {
	if ($data->user['userLevel']>=USERLEVEL_ADMIN) {
		$data->admin['menu'][]=array(
			'category'  => 'CMS Settings',
			'command'   => 'mainMenu',
			'name'      => 'Main Menu',
			'sortOrder' => 4
		);
	}
}

?>