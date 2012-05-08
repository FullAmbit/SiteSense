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
function friends_addQueries() {
	return array(
		'getAllRelationshipsByUser' => '
			SELECT
				u.*,
				f.confirmed confirmed,
				IF(f.user1 = :user, \'to\', \'from\') direction
			FROM
				!prefix!users u
			INNER JOIN
				!prefix!friends f
			ON
				(u.id = f.user1 AND f.user2 = :user)
				OR
				(u.id = f.user2 AND f.user1 = :user)
		',
		'getFriendsByUser' => '
			SELECT
				*
			FROM
				!prefix!users
			WHERE id IN
				(
					SELECT
						user1 user
					FROM
						!prefix!friends
					WHERE user2 = :user
					AND confirmed = 1
					
					UNION
					
					SELECT
						user2 user
					FROM
						!prefix!friends
					WHERE user1 = :user
					AND confirmed = 1
				)
			ORDER BY name ASC
		',
		'getRequestsByUser' => '
				SELECT
					u.*
				FROM
					!prefix!users u
				INNER JOIN
					!prefix!friends f
					ON u.id = f.user1
				WHERE
					f.confirmed = 0
				AND
					f.user2 = :user 
				ORDER BY
					name ASC
		',
		'findFriends' => '
			SELECT *
			FROM !prefix!users
			WHERE name
			LIKE :name
		',
        'findFriendsByAllFields' => '
			SELECT *
			FROM !prefix!users
			WHERE name LIKE :name
			OR
			fullName LIKE :fullName
			OR
			publicEmail LIKE :publicEmail
		',
		'makeRequest' => '
			INSERT INTO !prefix!friends
			SET user1 = :user1,
			    user2 = :user2
		',
		'acceptRequest' => '
			UPDATE !prefix!friends
			SET confirmed = 1
			WHERE user1 = :user1
			AND user2 = :user2
		',
		'ignoreRequest' => '
			DELETE FROM !prefix!friends
			WHERE user1 = :user1
			AND user2 = :user2
		',
		'isFriend' => '
			SELECT
				*
			FROM
				!prefix!users
			WHERE id IN
				(
					SELECT
						user1
					FROM
						!prefix!friends
					WHERE
						user1 = :user1
					AND
						confirmed = 1
					
					UNION
					
					SELECT
						user2 user
					FROM
						!prefix!friends
					WHERE
						user1 = :user1
					AND
						confirmed = 1
				)
			ORDER BY name ASC
			WHERE user.id = :user2 
		'
	);
}