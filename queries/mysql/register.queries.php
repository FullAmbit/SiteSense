<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function register_addQueries() {
	return array(
		'insertUser' => '
			INSERT INTO !prefix!users
			(name,password,fullName,registeredDate,registeredIP,lastAccess,userLevel,contactEMail,publicEMail)
			VALUES
			(:name,:password,:fullName,:registeredDate,:registeredIP,:lastAccess,:userLevel,:contactEMail,:publicEMail)
		',
		'getRegistrationEMail' => '
			SELECT content FROM !prefix!pages
			WHERE shortName = \'Registration_EMail\'
		',
		'insertActivationHash' => '
			INSERT INTO !prefix!activations
			(userId,hash,expires)
			VALUES
			(:userId,:hash,:expires)
		',
		'getExpiredActivations' => '
			SELECT userID from !prefix!activations
			WHERE expires <= :expireTime
		',
		'deleteUserById' => '
			DELETE FROM !prefix!users
			WHERE id = :userId
		',
		'expireActivationHashes' => '
			DELETE FROM !prefix!activations
			WHERE expires <= :expireTime
		',
		'checkActivationHash' => '
			SELECT expires FROM !prefix!activations
			WHERE
				userId = :userId
			AND
				hash = :hash
		',
		'deleteActivation' => '
			DELETE FROM !prefix!activations
			WHERE
				userId = :userId
			AND
				hash = :hash
		',
		'activateUser' => '
			UPDATE !prefix!users
			SET userLevel = '.USERLEVEL_USER.'
			WHERE id = :userId
		'
	);
}

?>