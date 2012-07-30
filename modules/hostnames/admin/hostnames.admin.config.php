<?php


function hostnames_admin_config($data){
	if(checkPermission('access','hostnames',$data)) {
		$data->admin['menu'][]=array(
			'category'  => 'Site Management',
			'command'   => 'hostnames/list',
			'name'      => 'Host Names',
			'sortOrder' => 11
		);
	}
}