<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_mainMenu_addQueries() {
	return array(
		'getSideById' => '
			SELECT id,side
			FROM !prefix!mainMenu
			WHERE id = :id
		',
		'updateSideById' => '
			UPDATE !prefix!mainMenu
			SET side = :side
			WHERE id = :id
		',
		'getSortOrderById' => '
			SELECT id,sortOrder
			FROM !prefix!mainMenu
			WHERE id = :id
		',
		'updateOrderById' => '
			UPDATE !prefix!mainMenu
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
		'getMenuItemsOrdered' => '
			SELECT * FROM !prefix!mainMenu
			ORDER BY side ASC,sortOrder ASC
		',
	);
}

?>