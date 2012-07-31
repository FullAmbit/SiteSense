<?php

function languages_admin_config($data){
	if(checkPermission('access','languages',$data)){
		$data->admin['menu'][]=array(
			'category'  => 'Site Management',
			'command'   => 'languages/list',
			'name'      => 'Languages',
			'sortOrder' => 13
		);
	}
}