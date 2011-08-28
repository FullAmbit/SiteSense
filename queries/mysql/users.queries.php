<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function users_addQueries() {
	return array(
		'getUserById' => '
			SELECT * FROM !prefix!users
			WHERE id = :userId
		',
		'getUserByName' => '
			SELECT * FROM !prefix!users
			WHERE name = :name
		',
		'getAllUsers' => '
			SELECT * FROM !prefix!users
		'
	);
}

?>