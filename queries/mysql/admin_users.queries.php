<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_users_addQueries() {
	return array(
		'getListLimited' => '
			SELECT * FROM !prefix!users
			LIMIT :start, :count
		',
		'getListLimitedStaff' => '
			SELECT * FROM !prefix!users
			WHERE userLevel > 126
			LIMIT :start, :count
		',
		'getListActivations' => '
			SELECT * FROM !prefix!users u
				INNER JOIN
					!prefix!activations a
				ON
					u.id = a.userId
			LIMIT :start, :count
		',
		'activate' => '
			DELETE FROM !prefix!activations
				WHERE userId = :id
		',
		'searchUsers_IncludingLevel' => '
			SELECT * FROM !prefix!users
			WHERE
				userLevel = :userLevel
				AND
				name LIKE :name
				AND
				fullName LIKE :fullName
		',
		'searchUsers_NotIncludingLevel' => '
			SELECT * FROM !prefix!users
			WHERE
				name LIKE :name
				AND
				fullName LIKE :fullName
		',
		'getById' => '
			SELECT * FROM !prefix!users
			WHERE id = :id
		',
		'updateUserByIdNoPw' => '
			UPDATE !prefix!users
			SET
				name = :name,
				userLevel = :userLevel,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'updateUserById' => '
			UPDATE !prefix!users
			SET
				name = :name,
				password = :password,
				userLevel = :userLevel,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'insertUser' => '
			INSERT INTO !prefix!users
			(name,password,registeredDate,registeredIP,lastAccess,userLevel,contactEMail,publicEMail)
			VALUES
			(:name,:password,:registeredDate,:registeredIP,:lastAccess,:userLevel,:contactEMail,:publicEMail)
		',
		'checkUserName' => '
			SELECT id FROM !prefix!users
			WHERE name = :name
		',
		'deleteUserById' => '
			DELETE FROM !prefix!users
			WHERE id = :id
		'
	);
}
?>