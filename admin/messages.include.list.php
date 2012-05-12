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
function admin_messagesBuild($data,$db) {
	//permission check for messages access
	if(!checkPermission('access','messages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}

	if (empty($data->action[3])) {
		$data->output['messageListStart'] = 0;
	} else {
		$data->output['messageListStart'] = (int)$data->action[3];
	}
	$data->output['messageListLimit']=ADMIN_SHOWPERPAGE;
	$messages = $db->prepare('getListLimited','admin_messages');
	$messages->bindParam(':start', $data->output['messageListStart'], PDO::PARAM_INT);
	$messages->bindParam(':count', $data->output['messageListLimit'], PDO::PARAM_INT);
	$messages->execute();
	$data->output['messageList'] = $messages->fetchAll();
}

function admin_messagesShow($data) {
	global $languageText;
	theme_messageListShow($data);
	foreach($data->output['messageList'] as $key => $message) {
		theme_messageListRow($message,$data,$key);
	}
	theme_messageListFoot();
}
?>	