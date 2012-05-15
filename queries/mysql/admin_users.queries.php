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
function admin_users_addQueries() {
	return array(
		'getListLimited' => '
			SELECT * FROM !prefix!users
			LIMIT :start, :count
		',
		'activate' => '
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
				fullName = :fullName,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'updateUserById' => '
			UPDATE !prefix!users
			SET
				name = :name,
				fullName = :fullName,
				password = :password,
				contactEMail = :contactEMail,
				publicEMail = :publicEMail
			WHERE id = :id
		',
		'insertUser' => '
			INSERT INTO !prefix!users
			(name,fullName,password,registeredIP,contactEMail,publicEMail)
			VALUES
			(:name,:fullName,:password,:registeredIP,:contactEMail,:publicEMail)
		',
		'checkUserName' => '
			SELECT id FROM !prefix!users
			WHERE name = :name
		',
		'deleteUserById' => '
			DELETE FROM !prefix!users
			WHERE id = :id
		',
        'deleteUserFromUserGroups' => '
			DELETE FROM !prefix!user_groups
			WHERE userID = :userID
		',
        'deleteUserFromUserPermissions' => '
			DELETE FROM !prefix!user_permissions
			WHERE userID = :userID
		',
        'getAllGroups' => '
			SELECT DISTINCT groupName
			FROM !prefix!user_group_permissions
		',
        'getPermissionsByGroupName' => '
			SELECT * FROM !prefix!user_groups
			WHERE groupName = :groupName
		',
        'getGroupName' => '
            SELECT groupName FROM !prefix!user_groups
            WHERE groupName = :groupName
        ',
        'removeGroupFromGroup_permissions' => '
			DELETE FROM !prefix!user_group_permissions
			WHERE groupName = :groupName
		',
        'removeGroupFromUsersPermission_groups' => '
			DELETE FROM !prefix!user_groups
			WHERE groupName = :groupName
		'
	);
}
?>