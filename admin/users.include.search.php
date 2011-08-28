<?php

common_include('libraries/forms.php');

function checkUserName($name,$db) {
	$statement=$db->prepare('checkUserName','admin_users');
	$statement->execute(array(
		':name' => $name
	));
	return $statement->fetchColumn();
}

function admin_usersBuild($data,$db) {
	$data->output['userForm']=new formHandler('userSearch',$data,true);
	
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['userForm']->fromForm)
	) {
		$data->output['userForm']->populateFromPostData();
		if($data->output['userForm']->sendArray[':userLevel'] != "-100"){
			$statement = $db->prepare('searchUsers_IncludingLevel', 'admin_users');
		}else{
			$statement = $db->prepare('searchUsers_NotIncludingLevel', 'admin_users');
			unset($data->output['userForm']->sendArray[':userLevel']);
		}
		$statement->execute($data->output['userForm']->sendArray);
		$data->output['userList'] = $statement->fetchAll();
	}else{
		$data->output['userList'] = false;
	}
}

function admin_usersShow($data) {
	global $languageText;
	theme_buildForm($data->output['userForm']);
	if($data->output['userList'] !== false){
		echo '
			<table class="userList">
				<caption>
					User Search Results
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
		';
	}
}

?>
