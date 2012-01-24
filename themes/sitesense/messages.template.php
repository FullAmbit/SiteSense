<?php
function theme_messagesDefault($data) {
	echo '
		<table border=1>
			<tr>
				<th>User</th>
				<th>Message</th>
				<th>Direction</th>
				<th>Read</td>
			</tr>
	';
	foreach($data->output['messages'] as $message){
		$trimmedMessage = trim(strip_tags($message['last_message']));
		if(strlen($trimmedMessage) > 80){
			$trimmedMessage = substr($trimmedMessage, 0, 77) . '...';
		}
		echo '
			<tr>
				<td>
					<a href="users/', $message['otheruser_id'], '">', $message['otheruser_name'], '</a>
				</td>
				<td>
					<a href="', $data->linkRoot, 'messages/with/', $message['otheruser_id'], '">', $trimmedMessage, '</a>
				</td>
				<td>
					', $message['last_direction'], '
				</td>
				<td>
				    ', (($message['last_read'] == 1) ? 'read' : 'unread'), '
				</td>
			</tr>
		';
	}
	echo '
		</table>
		<h2>Start a conversation:</h2>
		<label>With:</label>
		<select name="with" onchange="window.location = \'', $data->linkRoot, 'messages/with/\' + value">
			<option value=""></option>
			';
	foreach($data->output['otherusers'] as $user){
		echo '
			<option value="', $user['id'], '">', $user['name'], '</option>
		';	
	}
	echo '
		</select>';
}
function theme_showConversation($data) {
	echo '
		<table border=1>
			<tr>
				<th>From</th>
				<th>To</th>
				<th>Sent</th>
				<th>Message</th>
				<th>Options</th>
			</tr>
	';
	foreach($data->output['messages'] as $message){
		echo '
			<tr>
				<td>
					', $message['from_name'], '
				</td>
				<td>
					', $message['to_name'], '
				</td>
				<td>
					', $message['sent'], '
				</td>
				<td>
				    ', $message['message'], '
				</td>
				<td>
				';
		if($message['to_id'] == $data->user['id']){
			echo '<a href="', $data->linkRoot, 'messages/with/', $message['from_id'], '/delete/', $message['pm_id'], '">Delete</a>';
		}
		echo '
				</td>
			</tr>
		';
	}
	echo '</table>';
	theme_buildForm($data->output['sendForm']);
}