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
function messages_addQueries() {
	return array(
		//this one is a bit of a beast, but it saves using many queries
		'getLatestByUser' => '
			SELECT
				MAX(msg.id) last_id,
				msg.message last_message,
				IF(msg.`from` = :user, \'out\', \'in\') last_direction,
				IF(msg.`from` = :user, 1, msg.`read`) last_read,
				msg.sent last_sent,
				otheruser.name otheruser_name,
				otheruser.id otheruser_id
			FROM
				(
					SELECT seconduser, MAX(id) lastid FROM
					(
			    		(
			        		SELECT `to` seconduser, id
			            		FROM !prefix!user_pms
			        		WHERE `from` = :user AND deleted = 0
			    		) UNION (
			        		SELECT `from` seconduser, id
			            		FROM !prefix!user_pms
							WHERE `to` = :user AND deleted = 0
			    		)
			    	) lasttwo GROUP BY seconduser
				) lastmsg
			INNER JOIN
				!prefix!users otheruser
				ON
					otheruser.id = seconduser
			INNER JOIN
				!prefix!user_pms msg
				ON
					msg.id = lastid
			GROUP BY otheruser_id
			ORDER BY last_id DESC
		',
		'getUnreadCountByUser' => '
			SELECT COUNT(*) FROM !prefix!user_pms WHERE `to` = :user AND `read` = 0
		',
		'getMessagesBetweenUsers' => '
			SELECT pm.id pm_id, message, sent, userfrom.name from_name, userto.name to_name, userfrom.id from_id, userto.id to_id
				FROM !prefix!user_pms pm
			INNER JOIN
				!prefix!users userto
			ON
				userto.id = `to`
			INNER JOIN
				!prefix!users userfrom
			ON
				userfrom.id = `from`
			WHERE
			(
				(`from` = :firstuser AND `to` = :seconduser)
					OR
				(`from` = :seconduser AND `to` = :firstuser)
			) AND deleted = 0
			ORDER BY sent DESC
		',
		'deleteMessage' => '
			UPDATE !prefix!user_pms SET deleted = 1 WHERE id = :id AND `to` = :user
		',
		'sendMessage' => '
			INSERT INTO !prefix!user_pms (`from`,`to`,`message`) VALUES (:from, :to, :message) 
		',
		'setMessagesAsRead' => '
			UPDATE !prefix!user_pms SET `read` = 1 WHERE `from` = :seconduser AND `to` = :firstuser
		'
        );
}
?>