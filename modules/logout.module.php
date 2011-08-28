<?php

function page_content($data) {
	common_redirect_local($data, '');
	/* theme_contentBoxHeader('You Have Been Logged Out');
	$data->loadModuleTemplate('loginForm');
	theme_loginForm($data);
	theme_contentBoxFooter(); */
}

?>