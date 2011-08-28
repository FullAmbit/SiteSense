<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_pages_addQueries() {
	return array(
		'getPageListOrdered' => '
			SELECT id,sortOrder,parent,shortName	
			FROM !prefix!pages
			ORDER BY parent,sortOrder,id
		',
		'updatePageSortOrderById' => '
			UPDATE !prefix!pages
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
		'getPageOrderById' => '
			SELECT id,parent,sortOrder
			FROM !prefix!pages
			WHERE id = :id
		',
		'getPageListOrphans' => '
			SELECT id,shortName,parent,sortOrder
			FROM !prefix!pages
			WHERE parent <= 0
			ORDER BY parent,sortOrder,id
		',
		'getPageListByParent' => '
			SELECT id,shortName,parent,sortOrder
			FROM !prefix!pages
			WHERE parent = :parent
			ORDER BY parent,sortOrder,id
		',
		'deletePageById' => '
			DELETE FROM !prefix!pages
			WHERE id = :id
		',
		'deletePageByParent' => '
			DELETE FROM !prefix!pages
			WHERE parent = :id
		',
		'getPageIdByShortName' => '
			SELECT id FROM !prefix!pages
			WHERE shortName = :shortName
		',
		'getPageIdByShortNameAndParent' => '
			SELECT id FROM !prefix!pages
			WHERE shortName = :shortName
			AND parent = :parent
		',
		'getIdShortNameOrphans' => '
			SELECT id,shortName FROM !prefix!pages
			WHERE parent <= 0
		',
		'insertPage' => '
			INSERT INTO !prefix!pages
			(shortName,title,showOnParent,parent,showOnMenu,menuTitle,content,sortOrder,live) VALUES (:shortName, :title, :showOnParent, :parent, :showOnMenu, :menuTitle, :content, \'9999999999\', :live)
		',
		'updateShortNameById' => '
			UPDATE !prefix!pages
			SET shortName = :shortName
			WHERE id = :id
		',
		'updatePageById' => '
			UPDATE !prefix!pages
			SET
				shortName 	 = :shortName,
				title 			 = :title,
				showOnParent = :showOnParent,
				parent			 = :parent,
				showOnMenu	 = :showOnMenu,
				menuTitle 	 = :menuTitle,
				content 		 = :content,
				live 		     = :live
			WHERE id = :id
		',
		'getLastPageId' => '
			SELECT id FROM !prefix!pages
			ORDER BY id DESC
			LIMIT 1
		',
		'getPageById' => '
			SELECT * FROM !prefix!pages
			WHERE id = :id
		'
	);
}

?>