<?php

function languages_admin_config($data){
	if(checkPermission('access','languages',$data)){
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'languages/list',
			'name'      => $data->phrases['core']['languages'],
			'sortOrder' => 13
		);
	}
}