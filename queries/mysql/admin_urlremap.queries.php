<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_urlremap_addQueries() {
	return array(
		'getAllUrlRemaps' => '
			SELECT * FROM !prefix!urlremap ORDER BY id ASC
		',
		'getUrlRemapById' => '
			SELECT * FROM !prefix!urlremap WHERE id = :id
		',
		'editUrlRemap' => '
			UPDATE !prefix!urlremap
			SET `match` = :match, `replace` = :replace, `redirect` = :redirect
			WHERE id = :id
		',
		'insertUrlRemap' => '
			INSERT INTO !prefix!urlremap
			SET `match` = :match, `replace` = :replace, `redirect` = :redirect
		',
		'deleteUrlRemap' => '
			DELETE FROM !prefix!urlremap WHERE id = :id
		'
	);
}
?>