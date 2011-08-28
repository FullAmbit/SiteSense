<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_addQueries() {
	return array(
		'getPagesOnMenu' => '
			SELECT shortName,title,menuTitle
			FROM !prefix!pages
			WHERE showOnMenu = true
		',
		'getMainMenu' => '
			SELECT * FROM !prefix!mainMenu
		',
		'insertMenuItem' => '
			INSERT INTO !prefix!mainMenu
			(text,title,url,module,side,sortOrder) VALUES (:text,:title,:url,:module,\'left\',999999999)
		',
		'deleteMenuItemById' => '
			DELETE FROM !prefix!mainMenu
			WHERE id = :id
		',
		'getMenuItemsOrdered' => '
			SELECT * FROM !prefix!mainMenu
			ORDER BY side ASC, sortOrder ASC
		',
		'updateMenuSortOrder' => '
			UPDATE !prefix!mainMenu
			SET sortOrder = :sortOrder
			WHERE id = :id
		',



	);
}

?>