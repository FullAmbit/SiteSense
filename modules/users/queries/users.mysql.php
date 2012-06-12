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
			SELECT * FROM !prefix!users ORDER BY id ASC
		',
		'getByName' => '
			SELECT
				*
			FROM
				!prefix!users 
			WHERE
				name = :name
		',
		'getById' => '
			SELECT * FROM !prefix!users
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
        // Register
        'insertUser' => '
			INSERT INTO !prefix!users
			(name,password,firstName,lastName,registeredDate,registeredIP,lastAccess,contactEMail,publicEMail,emailVerified)
			VALUES
			(:name,:password,:firstName,:lastName,:registeredDate,:registeredIP,:lastAccess,:contactEMail,:publicEMail,:emailVerified)
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
		'
	);
}
?>