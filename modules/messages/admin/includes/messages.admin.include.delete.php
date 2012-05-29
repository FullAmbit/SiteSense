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
	//permission check for messages delete
	if(!checkPermission('delete','messages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}

	$staff=false;
	if (empty($data->action[3])) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
	}else{
		$message = $db->prepare('getMessage','admin_messages');
		$message->execute(array(':id' => (int)$data->action[3]));
		$message = $message->fetch();
		$data->output['exists'] = ($message !== false && $message['deleted'] == 0);
		if($data->action[4] == 'confirm'){
			$messages = $db->prepare('deleteMessageById','admin_messages');
			$messages->execute(array(':id' => (int)$data->action[3]));
			$data->output['success'] = ($messages->rowCount() == 1);
		}
	}
}
function admin_messagesShow($data) {
	if(isset($data->output['success'])){
		if($data->output['success']){
			theme_messagesDeleteSuccess($data);
		}else{
			theme_messagesDeleteFailure($data);
		}
	}else{
		theme_messagesDeleteConfirm($data);
	}
}
?>	
