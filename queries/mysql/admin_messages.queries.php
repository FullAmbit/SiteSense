<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_messages_addQueries() {
	return array(
		'getListLimited' => '
			SELECT p.*, u1.name from_name, u2.name to_name FROM !prefix!userpms p
				INNER JOIN !prefix!users u1
					ON u1.id = p.`from`
				INNER JOIN !prefix!users u2
					ON u2.id = p.`to`
			ORDER BY sent DESC
			LIMIT :start, :count
		',
		'getMessage' => '
			SELECT p.*, u1.name from_name, u2.name to_name FROM !prefix!userpms p
				INNER JOIN !prefix!users u1
					ON u1.id = p.`from`
				INNER JOIN !prefix!users u2
					ON u2.id = p.`to`
			WHERE p.id = :id
		',
		'deleteMessageById' => '
			UPDATE !prefix!userpms
			SET deleted = 1
			WHERE id = :id
		'
	);
}
?>