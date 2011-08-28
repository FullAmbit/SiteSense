<?php

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
			            		FROM !prefix!userpms
			        		WHERE `from` = :user AND deleted = 0
			    		) UNION (
			        		SELECT `from` seconduser, id
			            		FROM !prefix!userpms
							WHERE `to` = :user AND deleted = 0
			    		)
			    	) lasttwo GROUP BY seconduser
				) lastmsg
			INNER JOIN
				!prefix!users otheruser
				ON
					otheruser.id = seconduser
			INNER JOIN
				!prefix!userpms msg
				ON
					msg.id = lastid
			GROUP BY otheruser_id
			ORDER BY last_id DESC
		',
		'getUnreadCountByUser' => '
			SELECT COUNT(*) FROM !prefix!userpms WHERE `to` = :user AND `read` = 0
		',
		'getMessagesBetweenUsers' => '
			SELECT pm.id pm_id, message, sent, userfrom.name from_name, userto.name to_name, userfrom.id from_id, userto.id to_id
				FROM !prefix!userpms pm
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
			UPDATE !prefix!userpms SET deleted = 1 WHERE id = :id AND `to` = :user
		',
		'sendMessage' => '
			INSERT INTO !prefix!userpms (`from`, `to`, message) VALUES (:from, :to, :message) 
		',
		'setMessagesAsRead' => '
			UPDATE !prefix!userpms SET `read` = 1 WHERE `from` = :seconduser AND `to` = :firstuser
		'
	);
}

?>