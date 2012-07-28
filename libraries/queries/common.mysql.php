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
    	'setTimeZone' => '
    		SET time_zone = \'+0:00\'
    	',
    	'testRun' => '
    		SELECT CURRENT_TIMESTAMP
    	',
        'tableExists' => '
			SHOW TABLES LIKE \'!table!\'
		',
        'countRows' => '
			SELECT COUNT(*) AS COUNT
			FROM !table!
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
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(expires,"+00:00")) AS expires
			FROM !prefix!sessions
			WHERE sessionId = :sessionId
		',
        'pullUserInfoById' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(registeredDate,"+00:00")) AS registeredDate,
			UNIX_TIMESTAMP(CONCAT(lastAccess,"+00:00")) AS lastAccess
			FROM !prefix!users
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
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(registeredDate,"+00:00")) AS registeredDate,
			UNIX_TIMESTAMP(CONCAT(lastAccess,"+00:00")) AS lastAccess FROM !prefix!users
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
        'getNewsId' => '
			SELECT id FROM !prefix!blogs
			WHERE name="news"
		',
        'getAllNews' => '
			SELECT * FROM !prefix!blogs
			WHERE name="news"
		',
        'getMainMenuOrder' => '
			SELECT * FROM !prefix!main_menu
			ORDER BY sortOrder ASC
		',
        'getMainMenuOrderLeft' => '
			SELECT * FROM !prefix!main_menu
			WHERE side = "left"
			ORDER BY sortOrder ASC
		',
        'getMainMenuOrderRight' => '
			SELECT * FROM !prefix!main_menu
			WHERE side = "right"
			ORDER BY sortOrder ASC
		',
        'getEnabledMainMenuOrderLeft' => '
			SELECT * FROM !prefix!main_menu
			WHERE side = "left"
			AND enabled = 1
			ORDER BY sortOrder ASC
		',
        'getEnabledMainMenuOrderRight' => '
			SELECT * FROM !prefix!main_menu
			WHERE side = "right"
			AND enabled = 1
			ORDER BY sortOrder ASC
		',
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
			DELETE
			FROM !prefix!sidebars
			WHERE id = :id
		',
        //No column "showOnParent" in table !prefix!pages
        'getHomePagePages' => '
			SELECT *
			FROM !prefix!pages
			WHERE parent=-4096
			AND showOnParent = TRUE
			ORDER BY sortOrder ASC
		',
        'getHomePageSidebarPages' => '
			SELECT *
			FROM !prefix!pages
			WHERE parent=-4097
			AND showOnParent = TRUE
			ORDER BY sortOrder ASC
		',
        'getSidebarPages' => '
			SELECT *
			FROM !prefix!pages
			WHERE parent=-4098
			AND showOnParent = TRUE
			ORDER BY sortOrder ASC
		',
        // Permissions
        'addPermissionsByUserId' => '
            INSERT INTO !prefix!user_permissions
            (userId,permissionName,allow)
            VALUES
            (:id,:permission,:allow)
        ',
        'removeAllUserPermissionsByUserID' => '
            DELETE FROM !prefix!user_permissions
            WHERE userId = :userID
        ',
        'addPermissionByGroupName' => '
            INSERT INTO !prefix!user_group_permissions
            (groupName,permissionName)
            VALUES
            (:groupName,:permissionName)
        ',
        'addUserToPermissionGroup' => '
            INSERT INTO !prefix!user_groups
            (userID,groupName,expires)
            VALUES
            (:userID,:groupName,FROM_UNIXTIME(UNIX_TIMESTAMP(CURRENT_TIMESTAMP)+:expires))
        ',
        'removeUserFromPermissionGroup' => '
            DELETE FROM !prefix!user_groups
            WHERE userID = :userID
            AND groupName = :groupName
        ',
        'updateExpirationInPermissionGroup' => '
			UPDATE !prefix!user_groups
			SET expires = FROM_UNIXTIME(UNIX_TIMESTAMP(CURRENT_TIMESTAMP)+:expires)
			WHERE userID = :userID
            AND groupName = :groupName
		',
        'addUserToPermissionGroupNoExpires' => '
            INSERT INTO !prefix!user_groups
            (userID,groupName,expires)
            VALUES
            (:userID,:groupName,0)
        ',
        'updateExpirationInPermissionGroupNoExpires' => '
			UPDATE !prefix!user_groups
			SET expires = 0
			WHERE userID = :userID
            AND groupName = :groupName
		',
        'getGroupsByUserID' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(expires,"+00:00")) AS expires
			FROM !prefix!user_groups
			WHERE userID = :userID
		',
        'getGroupsByGroupID' => '
			SELECT groupName
			FROM !prefix!user_groups
			WHERE groupName = :groupName
		',
        'getPermissionsByGroupName' => '
			SELECT permissionName
			FROM !prefix!user_group_permissions
			WHERE groupName = :groupName
		',
        'getUserPermissionsByUserID' => '
			SELECT *
			FROM !prefix!user_permissions
			WHERE userID = :userID
		',
        'isUserAdmin' => '
			SELECT userID
			FROM !prefix!user_groups
			WHERE userID = :userID
			AND groupName = "Administrators"
		',
        'purgeExpiredGroups' => '
			DELETE FROM !prefix!user_groups
			WHERE expires > 0 AND expires < CURRENT_TIMESTAMP
		',
        'getUserByPermissionNameGroupOnlyScope' => '
            SELECT !prefix!user_groups.userID
            FROM !prefix!user_group_permissions
            INNER JOIN !prefix!user_groups ON !prefix!user_groups.groupName = !prefix!user_group_permissions.groupName
            WHERE !prefix!user_group_permissions.permissionName =  :permissionName
        ',
        'getUserByPermissionNameUserOnlyScope' => '
            SELECT userID, allow
            FROM !prefix!user_permissions
            WHERE permissionName = :permissionName
		',
        'updateGroupName' => '
            UPDATE !prefix!user_groups
            SET groupName = :groupName
            WHERE groupName = :currentGroupName
        ',
        'purgePermissionByGroupName' => '
            DELETE FROM !prefix!user_group_permissions
            WHERE permissionName = :permissionName
            AND groupName = :groupName
        ',
        'getAllModuleIds' => '
			SELECT id FROM !prefix!modules
		',
        'getModuleByShortName' => '
			SELECT * FROM !prefix!modules
			WHERE shortName = :shortName
		',
        'getModuleById' => '
			SELECT * FROM !prefix!modules
			WHERE id = :id
		',
        'getAllModules' => '
			SELECT * FROM !prefix!modules
			ORDER BY name ASC
		',
        'getEnabledModules' => '
			SELECT name, shortName FROM !prefix!modules
			WHERE enabled = 1
			ORDER BY name ASC
		',
        'getAllSidebars' => '
			SELECT * FROM !prefix!sidebars
		',
        'getSidebarById' => '
			SELECT * FROM !prefix!sidebars
			WHERE id = :id
		',
        'getSidebarsByModule' => '
			SELECT a.id,a.module,a.sidebar,a.enabled,a.sortOrder,b.name FROM !prefix!module_sidebars a, !prefix!sidebars b WHERE a.module = :module AND a.sidebar = b.id ORDER BY a.sortOrder ASC
		',
        'getEnabledSidebarsByModule' => '
				SELECT a.enabled,a.sortOrder,b.*
					FROM !prefix!module_sidebars a, !prefix!sidebars b
					WHERE a.module = :module
					AND a.sidebar = b.id
					AND a.enabled = 1
					ORDER BY a.sortOrder ASC
		',
        'enableSidebar' => '
			UPDATE !prefix!module_sidebars
			SET
				enabled  =  1
			WHERE id = :id
		',
        'disableSidebar' => '
			UPDATE !prefix!module_sidebars
			SET
				enabled  =  0
			WHERE id = :id
		',
        'newModule' => '
			INSERT INTO !prefix!modules
				(name, shortName, enabled)
			VALUES
				(:name, :shortName, :enabled)
		',
        'editModule' => '
			UPDATE !prefix!modules
				SET name = :name,
					shortName = :shortName,
					enabled = :enabled
				WHERE
					id = :id
		',
        'deleteModule' => '
			DELETE FROM !prefix!modules WHERE id = :id
		',
        'countSidebarsByModule' => '
			SELECT COUNT(*) AS rowCount FROM !prefix!module_sidebars WHERE module = :moduleId
		',
        'createSidebarSetting' => '
			REPLACE INTO !prefix!module_sidebars
			SET
				module = :moduleId,
				sidebar = :sidebarId,
				enabled = :enabled,
				sortOrder = :sortOrder
		',
        'getSidebarSetting' => '
			SELECT * FROM !prefix!module_sidebars WHERE id = :id
		',
        'shiftSidebarOrderUpByID' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
        'shiftSidebarOrderUpRelative' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND module = :moduleId
			ORDER BY sortOrder DESC LIMIT 1
		',
        'shiftSidebarOrderDownByID' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
        'shiftSidebarOrderDownRelative' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND module = :moduleId
			ORDER BY sortOrder ASC LIMIT 1
		',
        'deleteSidebarSettingBySidebar' => '
			DELETE FROM !prefix!module_sidebars WHERE sidebar = :sidebar
		',
        'getEnabledPluginsByModule' => '
            SELECT !prefix!plugins.name
            FROM !prefix!plugins
            JOIN !prefix!plugins_modules
            ON !prefix!plugins.id = !prefix!plugins_modules.plugin
            AND !prefix!plugins_modules.module = :moduleID
        ',
        'getEnabledPlugins' => '
          SELECT *
          FROM !prefix!plugins
          WHERE enabled = 1
        ',
        'getPluginByName' => '
            SELECT *
            FROM !prefix!plugins
            WHERE name = :name
        ',
        'newPlugin' => '
            INSERT INTO !prefix!plugins
            (name,enabled)
            VALUES
            (:name,:enabled)
        ',
        'findReplacement' => '
            SELECT r.match,r.replace FROM !prefix!url_remap r WHERE :url RLIKE r.match ORDER BY r.sortOrder ASC
        '
    );
}
?>