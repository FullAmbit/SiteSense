<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_sideBars_addQueries() {
	return array(
		'getSorted' => '
			SELECT id,sortOrder
			FROM !prefix!sideBars
			ORDER BY sortOrder,id
		',
		'getSortOrderById' => '
			SELECT id,sortOrder
			FROM !prefix!sidebars
			WHERE id = :id
		',
		'updateSortOrderById' => '
			UPDATE !prefix!sideBars
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
		'updateEnabledById' => '
			UPDATE !prefix!sidebars
			SET enabled = :enabled
			WHERE id = :id
		',
		'getSideBarNameByName' => '
			SELECT name FROM !prefix!sidebars
			WHERE name = :name
		',
		'insertSideBarSort' => '
			INSERT INTO !prefix!sidebars
			(name,enabled,fromFile,sortOrder) VALUES (:name,false,true,\'9999999999\')
		',
		'insertSideBar' => '
			INSERT INTO !prefix!sidebars
			(name,enabled,fromFile,title,titleURL,content,sortOrder) VALUES (:shortName, true, false, :title, :titleURL, :content, \'9999999999\')
		',
		'getFromFiles' => '
			SELECT * FROM !prefix!sidebars
			WHERE fromFile = TRUE
			ORDER BY sortOrder,ID ASC
		',
		'getFromFileById' => '
			SELECT fromFile FROM !prefix!sidebars
			WHERE id = :id
		',
		'deleteById' => '
			DELETE FROM !prefix!sidebars
			WHERE id = :id
		',
		'getAllOrdered' => '
			SELECT * FROM !prefix!sidebars
			ORDER BY sortOrder,ID ASC
		',
		'getById' => '
			SELECT * FROM !prefix!sidebars
			WHERE id = :id
		',
		'getIdByShortName' => '
			SELECT id FROM !prefix!sideBars
			WHERE name = :shortName
		',
		'updateShortNameById' => '
			UPDATE !prefix!sideBars
			SET name = :shortName
			WHERE id = :id
		',
		'updateById' => '
			UPDATE !prefix!sidebars
			SET
				name      = :shortName,
				title     = :title,
				titleURL  = :titleURL,
				content   = :content
			WHERE id = :id
		',

	);
}

?>