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
global $languageText;

	$data->output['userForm']=new formHandler('users',$data,true);
	
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['userForm']->fromForm)
	) {
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['userForm']->caption='Editing Page '.$data->action[3];
		$data->output['userForm']->populateFromPostData();
		if ($existing=checkUserName($data->output['userForm']->sendArray[':name'],$db)) {
			$existing=($existing!=$data->action[3]);
		}
		if (($data->output['userForm']->validateFromPost()) && (!$existing)) {
			unset($data->output['userForm']->sendArray[':password2']);
			if (is_numeric($data->action[3])) {
				unset($data->output['userForm']->sendArray[':registeredDate']);
				unset($data->output['userForm']->sendArray[':registeredIP']);
				unset($data->output['userForm']->sendArray[':lastAccess']);
				/* existing user, from form, must be save existing */
				if ($_POST['viewUser_password']=='') {
					$statement=$db->prepare('updateUserByIdNoPw','admin_users');
					unset($data->output['userForm']->sendArray[':password']);
					$data->output['userForm']->sendArray[':id']=$data->action[3];
				} else {
					$data->output['userForm']->sendArray[':password']=hash('sha256',$_POST['viewUser_password']);
					$statement=$db->prepare('updateUserById','admin_users');
					$data->output['userForm']->sendArray[':id']=$data->action[3];
				}
				$statement->execute($data->output['userForm']->sendArray) or die('Saving user failed');
				$id = $db->lastInsertId();
				$profileAlbum = $db->prepare('addAlbum', 'gallery');
				$profileAlbum->execute(array(':user' => $id, ':name' => 'Profile Pictures', ':shortName' => 'profile-pictures', 'allowComments' => 0));
				
			} else {
				/* not numeric, must be a new user */
				if (empty($data->output['userForm']->sendArray[':password'])) {
					$data->output['secondSideBar']='
						<h2>Error in Data</h2>
						<p>
							There were one or more errors. Please correct the fields with the red X next to them and try again.
						</p><p>
							<strong>New User Accounts must include Password!</strong>
						</p>';
						$data->output['userForm']->fields['password']['error']=true;
				} else {
					unset($data->output['userForm']->sendArray[':id']);
					$data->output['userForm']->sendArray[':registeredDate']=time();
					$data->output['userForm']->sendArray[':registeredIP']=$_SERVER['REMOTE_ADDR'];
					$data->output['userForm']->sendArray[':lastAccess']=0;
					$data->output['userForm']->sendArray[':password']=hash('sha256',$data->output['userForm']->sendArray[':password']);
					$statement=$db->prepare('insertUser','admin_users');
					$statement->execute($data->output['userForm']->sendArray) or die('Saving user failed');
				}
			}
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>User <em>'.$data->output['userForm']->sendArray[':name'].'<em> Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/users/edit/new">
							Add New User
						</a>
						<a href="'.$data->linkRoot.'admin/users/list/">
							Return to User List
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
			if ($existing) {
				$data->output['secondSideBar'].='
				<p>
					<strong>Username Already Exists!</strong>
				</p>';
				$data->output['userForm']->fields['name']['error']=true;
			}
			if ($_POST['viewUser_password']!=$_POST['viewUser_password2']) {
				$data->output['secondSideBar'].='
				<p>
					<strong>Password fields do not match!</strong>
				</p>';
				$data->output['userForm']->fields['password']['error']=true;
				$data->output['userForm']->fields['password2']['error']=true;
			}
		}
	} else if (is_numeric($data->action[3])) {
		$data->output['userForm']->caption='Editing User '.$data->action[3];
		$statement=$db->prepare('getById','admin_users');
		$statement->execute(array(
			':id' => $data->action[3]
		));
		if ($item=$statement->fetch()) {
			foreach ($data->output['userForm']->fields as $key => $value) {
				if (!empty($value['params']['type'])) {
					switch ($value['params']['type']) {
						case 'checkbox':
							$data->output['userForm']->fields[$key]['checked']=(
								$item[$key] ? 'checked' : ''
							);
						break;
						case 'password':
							/* NEVER SEND PASSWORD TO A FORM!!! */
						break;
						default:
							$data->output['userForm']->fields[$key]['value']=$item[$key];
					}
				} else switch ($key) {
					case 'lastAccess':
					case 'registeredDate':
						$data->output['userForm']->fields[$key]['value']=(
							($item[$key]==0) ?
							'never' :
							date(
								'd F Y - G:i:s O',
								$item[$key]
							)
						);
					break;
					default:
						$data->output['userForm']->fields[$key]['value']=$item[$key];
				}
			}
		}
	} else if ($data->action[3]!='new') {
		/* if it's not new, not numbered, and didn't come from form... */
		$data->output['editError']='unknown function';
	}
	if ($data->action[3]=='new') {
		$data->output['forceMenu']='users/edit/new';
	}
}

function admin_usersShow($data) {
	if ($data->user['userLevel']==USERLEVEL_ADMIN) {
		if (isset($data->output['pagesError']) && $data->output['pagesError'] == 'unknown function') {
			admin_unknown();
		} else if (isset($data->output['savedOkMessage'])) {
			echo $data->output['savedOkMessage'];
		} else {
			theme_buildForm($data->output['userForm']);
		}
	} else {
		theme_buildTable($data->output['userForm']);
	}
}

?>
