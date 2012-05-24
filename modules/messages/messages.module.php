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
function page_buildContent($data,$db) {
	if(!isset($data->user['id'])){
		common_redirect_local($data, 'login');
	}else{
		switch($data->action[1]){
			case false:
				$statement = $db->prepare('getLatestByUser', 'messages');
				$statement->execute(array(':user' => $data->user['id']));
				$data->output['messages'] = $statement->fetchAll();
				$data->output['icons'] = array();
				$otherUserIcons = $db->prepare('getProfilePictures', 'gallery');
				foreach($data->output['messages'] as &$message){
					$otherUserIcons->execute(array(':user' => $message['otheruser_id']));
					$icon = $otherUserIcons->fetch();
					if($icon === false){
						$message['icon'] = false;
					}else{
						$message['icon'] = $icon['icon'];
					}
				}
				//
				$statement = $db->prepare('getFriendsByUser', 'friends');
				$statement->execute(array(':user' => $data->user['id']));
				$data->output['otherusers'] = $statement->fetchAll();
				foreach($data->output['otherusers'] as $key => $otheruser){
					if($otheruser['id'] == $data->user['id']){
						unset($data->output['otherusers'][$key]);
					}
				}
			break;
			case 'with':
				// Send Message Form
				require_once('libraries/forms.php');
				$statement = $db->prepare('getUserByName', 'users');
				$statement->execute(array(':name' => $data->action[2]));
				$otherUser = $statement->fetch();
				if($otherUser === false){
					common_redirect_local($data, 'messages');
				}
				$data->output['sendForm'] = new formHandler('sendMessage', $data);
				// Has the form been sent?
				if (isset($_POST['fromForm']) && ($_POST['fromForm']==$data->output['sendForm']->fromForm)){
					$data->output['sendForm']->populateFromPostData();
					if ($data->output['sendForm']->validateFromPost()){
						$statement = $db->prepare('sendMessage', 'messages');
						$statement->execute(array(':from' => $data->user['id'], ':to' => $otherUser['id'], ':message' => $data->output['sendForm']->sendArray[':message']));
						$data->output['sendForm']->fields['message']['value'] = '';
					}else{
						if(strlen(strip_tags($data->output['sendForm']->sendArray[':message'])) == 0){
							$data->output['sendForm']->fields['message']['error'] = true;
							$data->output['sendForm']->fields['message']['errorList'][] = 'No message given';
						}
					}
				}
				// Have any messages been deleted?
				if($data->action[3] == 'delete'){
					$statement = $db->prepare('deleteMessage', 'messages');
					$statement->execute(array(':id' => $data->action[4], ':user' => $data->user['id']));
				}
				// Current Messages
				$statement = $db->prepare('getMessagesBetweenUsers', 'messages');
				$statement->execute(array(':firstuser' => $data->user['id'], ':seconduser' => $otherUser['id']));
				$data->output['messages'] = $statement->fetchAll();
				$statement = $db->prepare('setMessagesAsRead', 'messages');
				$statement->execute(array(':firstuser' => $data->user['id'], ':seconduser' => $otherUser['id']));
		}
		
	}
}
function page_content($data) {
	theme_contentBoxHeader('Private Messages');
	$data->loadModuleTemplate('messages');
	switch($data->action[1]){
		case false:
			theme_messagesDefault($data);
		break;
		case 'with':
			theme_showConversation($data);
		break;
	}
	theme_contentBoxFooter();
}
?>