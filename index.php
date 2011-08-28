<?php
ob_start(); //This is used to prevent errors causing g-zip compression problems before g-zip is started.

require_once('dbSettings.php');
require_once('libraries/common.php');
require_once('libraries/defines.php');
require_once('libraries/db.php');

function main() {
	$data=dbSettings(); /* get SQL/PDO login info */
	if (
		(strrchr($_SERVER['REQUEST_URI'],'/')=='/install') ||
		(strrchr($_SERVER['REQUEST_URI'],'?')=='?install')
	) {
		$data=db_init($data); /* immediately login and destroy login info! */
		require_once('admin/install.php');
		die;
	}

	
	$data=new dataHandler($data);
	theme_header($data);
	page_content($data);
	theme_sidebar($data);
	if (function_exists('theme_secondSideBar')) {
		theme_secondSideBar($data);
	}

	theme_footer($data);
}
main();
?>