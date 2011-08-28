<?php

function admin_messagesBuild($data,$db) {
	$staff=false;
	if (empty($data->action[3])) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
	}else{
		$messages = $db->prepare('getMessage','admin_messages');
		$messages->execute(array(':id' => (int)$data->action[3]));
		$data->output['message']=$messages->fetch();
		if($data->output['message'] === false){
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>Message not found</h2>';
		}
	}
}

function admin_messagesShow($data) {
	$message = $data->output['message'];
	echo '
	<table class="adminMessageTable">
		<tr>
			<th>From</th>
			<td>', $message['from_name'], '</td>
		</tr>
		<tr class="odd">
			<th>To</th>
			<td>', $message['to_name'], '</td>
		</tr>
			<th>Sent</th>
			<td>', $message['sent'], '</td>
		</tr>
		<tr class="odd">
			<th>Message</th>
			<td>', htmlspecialchars($message['message']), '</td>
		</tr>
		<tr>
			<th>Controls</th>
			<td class="buttonList">
				<a href="'.$data->linkRoot.'admin/messages/delete/'.$message['id'].'">Delete</a>
			</td>
		</tr>
	</table>
	';
}

?>	