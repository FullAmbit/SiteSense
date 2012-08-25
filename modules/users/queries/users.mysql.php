<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/
function users_addQueries() {
	return array(
		'getAllUsers' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(registeredDate,"+00:00")) AS registeredDate,
			UNIX_TIMESTAMP(CONCAT(lastAccess,"+00:00")) AS lastAccess
			FROM !prefix!users ORDER BY id ASC
		',
		'getByName' => '
			SELECT
				*,
				UNIX_TIMESTAMP(CONCAT(registeredDate,"+00:00")) AS registeredDate,
				UNIX_TIMESTAMP(CONCAT(lastAccess,"+00:00")) AS lastAccess
			FROM
				!prefix!users 
			WHERE
				name = :name
		',
		'getById' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(registeredDate,"+00:00")) AS registeredDate,
			UNIX_TIMESTAMP(CONCAT(lastAccess,"+00:00")) AS lastAccess
			FROM !prefix!users
			WHERE id = :id
		',
		'updateUserByIdNoPw' => '
			UPDATE !prefix!users
			SET
				firstName = :firstName,
				lastName = :lastName,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'updateUserById' => '
			UPDATE !prefix!users
			SET
				firstName = :firstName,
				lastName = :lastName,
				password = :password,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'checkUserName' => '
			SELECT id FROM !prefix!users
			WHERE name = :name
		',
		'activateUser' => '
			UPDATE !prefix!users
			SET activated=1
			WHERE id=:userId
		',
        // Register
        'insertUser' => '
			INSERT INTO !prefix!users
			(name, password, firstName, lastName, registeredIP, lastAccess, contactEMail, publicEMail, emailVerified, timeZone)
			VALUES
			(:name,:password,:firstName,:lastName,:registeredIP,CURRENT_TIMESTAMP,:contactEMail,:publicEMail,:emailVerified,:timeZone)
		',
        'getRegistrationEMail' => '
			SELECT parsedContent FROM !prefix!pages
			WHERE shortName = \'registration-email\'
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
			SELECT UNIX_TIMESTAMP(CONCAT(expires,"+00:00")) AS expires
			FROM !prefix!activations
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
        'updateEmailVerification' => '
			UPDATE !prefix!users SET emailVerified = 1 WHERE id = :userId
		',
		'getNameById' => '
			SELECT
				name
			FROM
				!prefix!users 
			WHERE
				id = :userId
			LIMIT 1
		',
		'createUserRow' => '
			INSERT INTO
				!prefix!users 
				(name)
			VALUES
				(:name)
		',
		'updateUserField' => '
			UPDATE
				!prefix!users 
			SET 
				!column1! = :fieldValue 
			WHERE
				name = :name
		',
		'updateIPDateAndAccess' => '
			UPDATE
				!prefix!users
			SET 
				registeredIP = :registeredIP,
				lastAccess = CURRENT_TIMESTAMP
			WHERE
				id = :userID
		',
		'addDynamicUserField' => '
			INSERT INTO
				!prefix!users_dynamic_fields
				(userId,name,value)
			VALUES
				(:userId,:name,:value)
		'
	);
}
?>