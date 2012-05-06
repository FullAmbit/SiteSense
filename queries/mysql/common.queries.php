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
function common_addQueries() {
	return array(
		'tableExists' => '
			SHOW TABLES LIKE \'!prefix!!table!\'
		',
		'countRows' => '
			SELECT COUNT(*) AS COUNT
			FROM !prefix!!table!
		',
		'logoutSession' => '
			DELETE FROM !prefix!sessions
			WHERE sessionID = :sessionID
		',
		'purgeExpiredSessions' => '
			DELETE FROM !prefix!sessions
			WHERE expires < CURRENT_TIMESTAMP
		',
		'getSessionById' => '
			SELECT * FROM !prefix!sessions
			WHERE sessionId = :sessionId
		',
		'pullUserInfoById' => '
			SELECT * FROM !prefix!users
			WHERE id = :userId
		',
		'getUserIdByName' => '
			SELECT id FROM !prefix!users
			WHERE name = :name
		',
		'updateSessionExpiration' => '
			UPDATE !prefix!sessions
			SET expires = :expires
			WHERE sessionId = :sessionId
		',
		'updateLastAccess' => '
			UPDATE !prefix!users
			SET lastAccess = CURRENT_TIMESTAMP
			WHERE id = :id
		',
		'checkPassword' => '
			SELECT * FROM !prefix!users
			WHERE name = :name
			AND password = :passphrase
		',
		'purgeSessionByUserId' => '
			DELETE FROM !prefix!sessions
			WHERE userId = :userId
		',
		'updateUserSession' => '
			INSERT INTO !prefix!sessions
			(sessionId,userId,expires,ipAddress,userAgent)
			VALUES
			(:sessionId,:userId,:expires,:ipAddress,:userAgent)
		',
		'getSettings' => '
			SELECT * FROM !prefix!settings
		',
		'getNewsId' => "
			SELECT id FROM !prefix!blogs
			WHERE name='news'
		",
		'getAllNews' => "
			SELECT * FROM !prefix!blogs
			WHERE name='news'
		",
		'getMainMenuOrder' => '
			SELECT * FROM !prefix!main_menu
			ORDER BY sortOrder ASC
		',
		'getMainMenuOrderLeft' => "
			SELECT * FROM !prefix!main_menu
			WHERE side = 'left'
			ORDER BY sortOrder ASC
		",
		'getMainMenuOrderRight' => "
			SELECT * FROM !prefix!main_menu
			WHERE side = 'right'
			ORDER BY sortOrder ASC
		",
		'getEnabledMainMenuOrderLeft' => "
			SELECT * FROM !prefix!main_menu
			WHERE side = 'left'
			AND enabled = 1
			ORDER BY sortOrder ASC
		",
		'getEnabledMainMenuOrderRight' => "
			SELECT * FROM !prefix!main_menu
			WHERE side = 'right'
			AND enabled = 1
			ORDER BY sortOrder ASC
		",
		'getSidebars' => '
			SELECT *
			FROM !prefix!sidebars
			WHERE enabled = TRUE
			ORDER BY sortOrder ASC
		',
		'getSidebarById' => '
			SELECT *
			FROM !prefix!sidebars
			WHERE id = :id
			ORDER BY sortOrder ASC
		',
		'deleteFromSidebarsById' => '
			DELETE FROM !prefix!sidebars
			WHERE id = :id
		',
		//No column "showOnParent" in table !prefix!pages
        'getHomePagePages' => "
			SELECT *
			FROM !prefix!pages
			WHERE parent=-4096
			AND showOnParent = TRUE
			ORDER BY sortOrder ASC
		",
		'getHomePageSideBarPages' => "
			SELECT *
			FROM !prefix!pages
			WHERE parent=-4097
			AND showOnParent = TRUE
			ORDER BY sortOrder ASC
		",
		'getSideBarPages' => "
			SELECT *
			FROM !prefix!pages
			WHERE parent=-4098
			AND showOnParent = TRUE
			ORDER BY sortOrder ASC
		",
	    //^^^
        // Permissions
        'getGroupsByUserID' => '
			SELECT * FROM !prefix!user_permission_groups
			WHERE userID = :userID
		',
        'getPermissionsByGroupName' => '
			SELECT * FROM !prefix!group_permissions
			WHERE groupName = :groupName
		',
        'getUserPermissionsByUserID' => '
			SELECT * FROM !prefix!user_permissions
			WHERE userID = :userID
		',
    );
}

?>