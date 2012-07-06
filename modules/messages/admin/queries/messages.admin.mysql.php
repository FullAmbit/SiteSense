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
function admin_messages_addQueries() {
	return array(
        'getListLimited' => '
			SELECT p.*,UNIX_TIMESTAMP(CONCAT(p.sent,"+00:00")) AS sent,u1.name from_name, u2.name to_name FROM !prefix!user_pms p
				INNER JOIN !prefix!users u1
					ON u1.id = p.`from`
				INNER JOIN !prefix!users u2
					ON u2.id = p.`to`
			ORDER BY sent DESC
			LIMIT :start, :count
		',
        'getMessage' => '
			SELECT p.*,UNIX_TIMESTAMP(CONCAT(p.sent,"+00:00")) AS sent, u1.name from_name, u2.name to_name FROM !prefix!user_pms p
				INNER JOIN !prefix!users u1
					ON u1.id = p.`from`
				INNER JOIN !prefix!users u2
					ON u2.id = p.`to`
			WHERE p.id = :id
		',
        'deleteMessageById' => '
			UPDATE !prefix!user_pms
			SET deleted = 1
			WHERE id = :id
		'
	);
}
?>