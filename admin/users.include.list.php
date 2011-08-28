<?php

function admin_usersBuild($data,$db) {
	$staff=false;
	if (empty($data->action[3])) {
		$data->output['userListStart']=0;
	} else if (is_numeric($data->action[3])) {
		$data->output['userListStart']=$data->action[3];
	} else if ($data->action[3]=='staff') {
		$staff=true;
		$data->output['forceMenu']='users/list/staff';
		if (empty($data->action[4])) {
			$data->output['userListStart']=0;
		} else if (is_numeric($data->action[4])) {
			$data->output['userListStart']=$data->action[4];
		}
	} else {
		$data->output['abort']=true;
		$data->output['abortMessage']='
			<h2>Unrecognized Command</h2>
			<p>Recheck the URL and try again.</p>
		';
	}
	if (empty($data->output['abort'])) {
		$data->output['userListLimit']=ADMIN_SHOWPERPAGE;
		$data->output['userListCount']=0;
		try {
			if ($staff) {
				$statement=$db->prepare('getListLimitedStaff','admin_users');
			} else {
				$statement=$db->prepare('getListLimited','admin_users');
			}
			$statement->bindParam(':start',$data->output['userListStart'],PDO::PARAM_INT);
			$statement->bindParam(':count',$data->output['userListLimit'],PDO::PARAM_INT);
			$statement->execute();
			$data->output['userList']=$statement->fetchAll();
		} catch(PDOException $e) {
			$data->output['abort']=true;
			$data->output['abortMessage']='
				<h2>There was a database connection error</h2>
				<pre>'.$e->getMessage().'</pre>
			';
		}
	}

}

function admin_usersShow($data) {
global $languageText;
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
					<th class="userLevel">Access Level</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
	foreach($data->output['userList'] as $key => $user) {
		$userLevelText=$languageText['userLevels'][$user['userLevel']];
		$userLevelClass='userLevel_'.common_camelBack($userLevelText);
		echo '
				<tr class="',($key%2==0 ? 'even' : 'odd'),'">
					<td class="id">',$user['id'],'</td>
					<td class="userName">
						<a href="'.$data->linkRoot.'admin/users/edit/',$user['id'],'">
							',$user['name'],'
						</a>
					</td>
					<td class="userLevel ',$userLevelClass,'">',$userLevelText,'</td>
					<td class="buttonList">',(
							$data->user['userLevel']==USERLEVEL_ADMIN ? '
						<a href="'.$data->linkRoot.'admin/users/delete/'.$user['id'].'">Delete</a>
						<a href="'.$data->linkRoot.'admin/users/ban/'.$user['id'].'">Ban</a>' :	''
						),'
					</td>
				</tr>';
	}
	echo '
			</tbody>
		</table>

		<div class="panel buttonList">
			<a href="'.$data->linkRoot.'admin/users/edit/new">Add New User</a>
		</div>';
}

?>