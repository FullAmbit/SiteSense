<?php

function admin_usersBuild($data,$db) {
	if (empty($data->action[3])) {
		$data->output['userListStart']=0;
	} else if (is_numeric($data->action[3])) {
		$data->output['userListStart']=$data->action[3];
	}
	if(!empty($data->action[4]) && $data->action[4] == 'activate'){
		$userId = (int)$data->action[5];
		if($userId > 0){
			$statement = $db->prepare('activate', 'admin_users');
			$statement->execute(array(':id' => $userId));
		}
	}
	if (empty($data->output['abort'])) {
		$data->output['userListLimit']=ADMIN_SHOWPERPAGE;
		$data->output['userListCount']=0;
		$statement=$db->prepare('getListActivations','admin_users');
		$statement->bindParam(':start',$data->output['userListStart'],PDO::PARAM_INT);
		$statement->bindParam(':count',$data->output['userListLimit'],PDO::PARAM_INT);
		$statement->execute();
		$data->output['userList']=$statement->fetchAll();
	}

}

function admin_usersShow($data) {
	if(empty($data->output['userList'])){
		echo '<p>There are no users awaiting activation</p>';
	}else{
		echo '
			<table class="userList">
				<caption>
					Users
					',$data->output['userListStart']+1,' through
					',$data->output['userListStart']+count($data->output['userList']),'
				</caption>
				<thead>
					<tr>
						<th class="id">ID</th>
						<th class="userName">Username</th>
						<th class="Hash">Hash</th>
						<th class="Expires">Expires</th>
						<th class="Activate">Activate?</th>
					</tr>
				</thead>
				<tbody>
		';
		foreach($data->output['userList'] as $key => $user) {
			echo '
					<tr class="',($key%2==0 ? 'even' : 'odd'),'">
						<td class="id">',$user['id'],'</td>
						<td class="userName">
							<a href="',$data->linkRoot,'admin/users/edit/',$user['id'],'">
								',$user['name'],'
							</a>
						</td>
						<td class="Hash">',$user['hash'],'</td>
						<td class="Expires">',$user['expires'],'</td>
						<td class="Activate">
							<a href="',$data->linkRoot,'admin/users/activation/',$data->output['userListStart'],'/activate/',$user['id'],'">Activate</a>
						</td>
					</tr>';
		}
		echo '
				</tbody>
			</table>
			';
	}
}

?>