<?php

function theme_leftSideBar($data) {
	echo '
		<!-- #content, #contentWrapper --></div></div>';
	// Check If We Have Any Sidebars	
	if(empty($data->sidebarList['left']))
	{
		return;
	}
	echo '
		<div id="leftSidebar">';
	
		foreach($data->sidebarList['left'] as $sideBar) {
			theme_sideBarBoxHeader($sideBar['title']);
			echo $sideBar['parsedContent'];			
			theme_sideBarBoxFooter();
		}
	echo '
		<!-- .leftSideBar --></div>';

} // theme_leftSideBar

function theme_rightSideBar($data) {

	// Check If We Have Any Sidebars	
	if(empty($data->sidebarList['right']))
	{
		return;
	}
	echo '
		<div id="rightSidebar">';
	foreach($data->sidebarList['right'] as $sideBar) {
		theme_sideBarBoxHeader($sideBar['title']);
		echo $sideBar['parsedContent'];			
		theme_sideBarBoxFooter();
	}
	echo '
		<!-- .rightSideBar --></div>';

}
