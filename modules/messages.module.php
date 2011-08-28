<?php

function page_getUniqueSettings($data) {
	$data->output['pageShortName']='Personal Messages';
}

function page_buildContent($data,$db) {
	if(!isset($data->user) || $data->user['userLevel'] == 0){
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
				$statement = $db->query('getAllUsers', 'users');
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
				require_once($data->themeDir.'formGenerator.template.php');
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
					if ($data->output['sendForm']->validateFromPost()) {
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