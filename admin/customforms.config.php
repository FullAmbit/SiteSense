<?php

function customforms_config($data,$db) {
	if ($data->user['userLevel']>=USERLEVEL_WRITER) {
		$data->admin['menu'][]=array(
			'category'	=> 'CMS Settings',
			'command' 	=> 'customforms',
			'name'			=> 'Custom Forms',
			'sortOrder' => 3
		);
	}
}

?>