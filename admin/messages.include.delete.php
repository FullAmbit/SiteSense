<?php

function admin_messagesBuild($data,$db) {
	$staff=false;
	if (empty($data->action[3])) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
	}else{
		$message = $db->prepare('getMessage', 'admin_messages');
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
			echo '
				<h2>Removal Successful</h2>
				<p>The Message has been successfully deleted</p>
				<p><a href="', $data->linkRoot, 'admin/messages/list">Return to message list</a></p>
			';
		}else{
			echo '
				<h2>Cannot remove message</h2>
				<p>The message cannot be removed. It ', ($data->output['exists'] ? 'does' : 'doesn\'t'), ' exist in the database.</p>
				<p><a href="', $data->linkRoot, 'admin/messages/list">Return to message list</a></p>
			';
		}
	}else{
		echo '
			<h2>Confirm Removal</h2>
			<p>Are you sure that you want to remove this message?</p>
			<ul>
				<li><a href="', $data->linkRoot, 'admin/messages/delete/', $data->action[3], '/confirm">Yes</a></li>
				<li><a href="', $data->linkRoot, 'admin/messages/list">No</a></li>
			</ul>
		';
	}
}

?>	