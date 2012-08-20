<?php

function languages_admin_config($data){
	$data->permissions['languages'] = array(
		'access'      => $data->phrases['core']['permission_languages_access'],
		'list'        => $data->phrases['core']['permission_languages_list'],
		'addPhrase'   => $data->phrases['core']['permission_languages_addPhrase'],
		'default'     => $data->phrases['core']['permission_languages_default'],
		'editPhrase'  => $data->phrases['core']['permission_languages_editPhrase'],
		'listPhrases' => $data->phrases['core']['permission_languages_listPhrases'],
		'update'      => $data->phrases['core']['permission_languages_update']
	);
	if(checkPermission('access','languages',$data)){
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'languages/list',
			'name'      => $data->phrases['core']['languages'],
			'sortOrder' => 13
		);
	}
}