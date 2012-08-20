<?php


function hostnames_admin_config($data){
	$data->permissions['hostnames'] = array(
		'access' => $data->phrases['core']['permission_hostnames_access'],
		'add'    => $data->phrases['core']['permission_hostnames_add'],
		'edit'   => $data->phrases['core']['permission_hostnames_edit'],
		'delete' => $data->phrases['core']['permission_hostnames_delete']
	);
	
	if(checkPermission('access','hostnames',$data)) {
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'hostnames/list',
			'name'      => $data->phrases['core']['hostnames'],
			'sortOrder' => 11
		);
	}
}