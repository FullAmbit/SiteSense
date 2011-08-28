<?php

function cmsSettings_config($data) {
	if ($data->user['userLevel']>=USERLEVEL_ADMIN) {
		$data->admin['menu'][]=array(
			'category'  => 'CMS Settings',
			'command'   => 'cmsSettings',
			'name'      => 'Global Settings',
			'sortOrder' => 1
		);
	}
}

?>