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
		'getUserById' => '
			SELECT * FROM !prefix!users
			WHERE id = :userId
		',
		'getById' => '
			SELECT * FROM !prefix!users WHERE id = :id
		',
		'getUserByName' => '
			SELECT * FROM !prefix!users
			WHERE name = :name
		',
		'getAllUsers' => '
			SELECT * FROM !prefix!users
		',
		'getUserNameByID' => '
			SELECT name FROM !prefix!users WHERE id = :userID
		',
		'checkIpBan' => '
			SELECT * FROM !prefix!banned WHERE ipAddress = :ip
		',
		'removeBan' => '
			DELETE FROM !prefix!banned WHERE id = :id LIMIT 1
		',
		'updateUserLevel' => '
			UPDATE !prefix!users SET userLevel = :userLevel WHERE id = :userId
		'
	);
}
?>