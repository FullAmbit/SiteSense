<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function page_addQueries() {
	return array(
		'getPagesByShortName' => '
			SELECT * FROM !prefix!pages
			WHERE shortName = :shortName
		',
		'getPagesByParent' => '
			SELECT *
			FROM !prefix!pages
			WHERE parent = :parent
		',
		'getPageByShortNameAndParent' => '
			SELECT * FROM !prefix!pages
			WHERE shortName = :shortName
			AND parent = :parent
		'
	);
}

?>