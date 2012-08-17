<?php


function hostnames_admin_config($data){
	if(checkPermission('access','hostnames',$data)) {
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'hostnames/list',
			'name'      => $data->phrases['core']['hostnames'],
			'sortOrder' => 11
		);
	}
}