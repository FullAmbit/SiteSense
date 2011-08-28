<?php

function pages_config($data,$db) {
	if ($data->user['userLevel']>=USERLEVEL_WRITER) {
		$data->admin['menu'][]=array(
			'category'	=> 'CMS Settings',
			'command' 	=> 'pages/list',
			'name'			=> 'Static Pages',
			'sortOrder' => 2
		);
	}
	$qHandle=$db->query('getPagesOnMenu','admin');
	while ($item=$qHandle->fetch()) {
		$data->menuSource[]=array(
			'text' 		  	=> $item['menuTitle'],
			'title'       => $item['title'],
			'url' 				=> $item['shortName'],
			'module'			=> 'pages'
		);
	}
}

?>