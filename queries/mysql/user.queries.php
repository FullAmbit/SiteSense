<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function user_addQueries() {
	return array(
		'getById' => '
			SELECT * FROM !prefix!users
			WHERE id = :id
		',
		'updateUserByIdNoPw' => '
			UPDATE !prefix!users
			SET
				fullName = :fullName,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'updateUserById' => '
			UPDATE !prefix!users
			SET
				fullName = :fullName,
				password = :password,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'checkUserName' => '
			SELECT id FROM !prefix!users
			WHERE name = :name
		',
	);
}
?>