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
common_include('libraries/forms.php');
function checkUserName($name,$db) {
	$statement=$db->prepare('checkUserName','admin_users');
	$statement->execute(array(
		':name' => $name
	));
	return $statement->fetchColumn();
}
function admin_usersBuild($data,$db) {
	//permission check for users edit
	if(!checkPermission('edit','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	global $languageText;
	
	$data->output['userForm'] = $form = new formHandler('users',$data,true);
	
	
	$statement=$db->prepare('getById','admin_users');
	$statement->execute(array(
		':id' => $data->action[3]
	));
	
	if (($item=$statement->fetch()) !== FALSE) {
		
		$data->output['userForm']->caption = 'Editing User '.$item['name'];

		foreach ($data->output['userForm']->fields as $key => $value) {
			if($value['tag'] == 'select')
			{
				$data->output['userForm']->fields[$key]['value'] = $item[$key];
			} else
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
						gmdate(
							'd F Y - G:i:s',
							strtotime($item[$key])
						)
					);
				break;
				default:
					$data->output['userForm']->fields[$key]['value']=$item[$key];
			}
		}
	} else {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = 'The user you specified could not be found';
	}
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['userForm']->fromForm))
	{
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['userForm']->populateFromPostData();
		$existing = false;
		// Check If UserName Already Exists (ONLY IF DIFFERENT) //
		if($form->sendArray[':name'] !== $item['name'])
		{
			$existing = checkUserName($form->sendArray[':name'],$db);
		}
		
		if($existing)
		{
			$data->output['secondSideBar']='
				  <h2>Error in Data</h2>
				  <p>
					  There were one or more errors. Please correct the fields with the red X next to them and try again.
				  </p><p>
					  <strong>That username is already taken!</strong>
				  </p>';
				  
			$data->output['userForm']->fields['name']['error'] = true;
				  
			return;
		}
		
		
		if (($data->output['userForm']->validateFromPost())) {
			//--Don't Need These, User Already Exists--//
			unset($data->output['userForm']->sendArray[':password2']);
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

			$result = $statement->execute($data->output['userForm']->sendArray);
			
			if($result == FALSE)
			{
				$data->output['savedOkMessage'] = 'There was an error in saving to the database';
				return;
			}
			
			$id = $db->lastInsertId();
			$profileAlbum = $db->prepare('addAlbum', 'gallery');
			$profileAlbum->execute(array(':user' => $id, ':name' => 'Profile Pictures', ':shortName' => 'profile-pictures', 'allowComments' => 0));
			
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>User <em>'.$data->output['userForm']->sendArray[':name'].'<em> Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/users/add">
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
	}
}
function admin_usersShow($data) {
	if (isset($data->output['pagesError']) && $data->output['pagesError'] == 'unknown function') {
		admin_unknown();
	} else if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['userForm']);
	}
}
?>
