<?php

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
			WHERE expires < :currentTime
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
			SET lastAccess = :lastAccess
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
			SELECT id
			FROM !prefix!blogs
			WHERE name='news'
		",
		'getAllNews' => "
			SELECT *
			FROM !prefix!blogs
			WHERE name='news'
		",
		'getMainMenuOrder' => '
			SELECT * FROM !prefix!mainMenu
			ORDER BY sortOrder ASC
		',
		'getMainMenuOrderLeft' => "
			SELECT * FROM !prefix!mainMenu
			WHERE side = 'left'
			ORDER BY sortOrder ASC
		",
		'getMainMenuOrderRight' => "
			SELECT * FROM !prefix!mainMenu
			WHERE side = 'right'
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
		"
	);
}

?>