<?php

function gallery_startup($data,$db) {
	$data->menuSource[]=array(
		'text'      => 'Gallery',
		'title'     => 'Your pictures and photos',
		'url' 		=> 'gallery',
		'module'	=> 'gallery'
	);
}

?>