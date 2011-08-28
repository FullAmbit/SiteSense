<?php

function default_startup($data,$db) {
	$data->menuSource[]=array(
		'text'        => 'Home',
		'title'       => '',
		'url' 				=> '',
		'module'			=> 'home'
	);
	$data->loadModuleTemplate('blogs');
}

?>