<?php

function messages_config($data) {
	if ($data->user['userLevel']>=USERLEVEL_ADMIN) {
		$data->admin['menu'][]=array(
			'category'  => 'User Management',
			'command'   => 'messages',
			'name'      => 'User PMs',
			'sortOrder' => 100
		);
	}
}

?>