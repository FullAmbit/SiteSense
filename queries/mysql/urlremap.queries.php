<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function urlremap_addQueries() {
	return array(
		'findReplacement' => '
			SELECT * FROM !prefix!urlremap WHERE :url RLIKE `match`
		'
	);
}
?>