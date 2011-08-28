<?php

function admin_messagesBuild($data,$db) {
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
	echo '
		<table class="messageList">
			<caption>
				Messages
				',$data->output['messageListStart']+1,' through
				',$data->output['messageListStart']+count($data->output['messageList']),'
			</caption>
			<thead>
				<tr>
					<th class="from">From</th>
					<th class="to">To</th>
					<th class="message">Message</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
	foreach($data->output['messageList'] as $key => $message) {
		echo '
				<tr class="', ($message['deleted'] == 1) ? ('deleted') : ($key%2==0 ? 'even' : 'odd'),'">
					<td class="from"><a href="', $data->linkRoot, 'admin/users/edit/', $message['from'], '">',$message['from_name'],'</a></td>
					<td class="to"><a href="', $data->linkRoot, 'admin/users/edit/', $message['from'], '">',$message['to_name'],'</a></td>
					<td class="message"><a href="', $data->linkRoot, 'admin/messages/view/', $message['id'], '">', substr(strip_tags($message['message']), 0, 50), '...</a></td>
					<td class="buttonList">
						',(
							($message['deleted'] == 1)
								?
									'deleted'
								:
									'
						<a href="'.$data->linkRoot.'admin/messages/delete/'.$message['id'].'">Delete</a>
									'
						),'
					</td>
				</tr>';
	}
	echo '
			</tbody>
		</table>
		';
}

?>	