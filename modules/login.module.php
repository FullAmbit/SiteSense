<?php

function page_getUniqueSettings($data) {
	$data->output['pageShortName']='login';
}

function page_content($data) {
	theme_contentBoxHeader('User Login');
	$data->loadModuleTemplate('loginForm');
	theme_loginForm($data);
	theme_contentBoxFooter();
}

?>