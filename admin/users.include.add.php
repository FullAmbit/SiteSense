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
function getPermissions($data,$db) {
    $targetFunction='loadPermissions';
    // Get core permissions
    if (function_exists($targetFunction)) {
        $targetFunction($data);
    }
}
function checkUserName($name,$db) {
	$statement=$db->prepare('checkUserName','admin_users');
	$statement->execute(array(
		':name' => $name
	));
	return $statement->fetchColumn();
}

function admin_usersBuild($data,$db) {
	//permission check for users add
	if(!checkPermission('add','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
    // Load all groups
    $statement=$db->query('getAllGroups','admin_users');
    $data->output['groupList']=$statement->fetchAll();
    // Load all groups by userID
    $statement=$db->prepare('getGroupsByUserID');
    $statement->execute(array(
        ':userID' =>  $data->action[3]
    ));
    $data->output['userGroupList']=$statement->fetchAll();
    // Load core permissions
    getPermissions($data,$db);

    $data->output['userForm'] = $form = new formHandler('users',$data,true);

	unset($form->fields['registeredDate']);
	unset($form->fields['registeredIP']);
	unset($form->fields['lastAccess']);
	
	$form->fields['password']['required'] = true;
	$form->fields['password2']['required'] = true;
	
	$form->caption = 'Add A User';
	// Handle Form Submission
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['userForm']->fromForm))
	{
		// Populate From Post //
		$form->populateFromPostData();
		// Check If UserName Already Exists //
		$existing = checkUserName($form->sendArray[':name'],$db);
		if($existing) {
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

		// Did it validate?!?
		if (($form->validateFromPost()))
		{
			// Make Sure We Have A Password..
			if (empty($data->output['userForm']->sendArray[':password']))
			{
			  $data->output['secondSideBar']='
				  <h2>Error in Data</h2>
				  <p>
					  There were one or more errors. Please correct the fields with the red X next to them and try again.
				  </p><p>
					  <strong>New User Accounts must include Password!</strong>
				  </p>';
				  $data->output['userForm']->fields['password']['error']=true;
				  
				  return;
			}
			// Don't need this....
			unset($data->output['userForm']->sendArray[':password2']);
			unset($data->output['userForm']->sendArray[':id']);
			
			$data->output['userForm']->sendArray[':registeredIP']=$_SERVER['REMOTE_ADDR'];
			$data->output['userForm']->sendArray[':password']=hash('sha256',$data->output['userForm']->sendArray[':password']);
            foreach($data->permissions as $category => $permissions) {
                foreach($permissions as $permissionName => $permissionDescription) {
                    if(isset($data->output['userForm']->sendArray[':'.$category.'_'.$permissionName])) {
                        $submittedPermissions[':'.$category.'_'.$permissionName]=$data->output['userForm']->sendArray[':'.$category.'_'.$permissionName];
                        unset($data->output['userForm']->sendArray[':'.$category.'_'.$permissionName]);
                    }
                }
            }
            $submittedGroups=array();
            foreach($data->output['groupList'] as $key => $value) {
                $expires='Never';
                if($data->output['userForm']->sendArray[':'.$value['groupName']]=='checked') {
                    // User is still a member
                    // Check expiration
                    $submittedGroups[$value['groupName']]['expires']=$expires;

                }
                unset($data->output['userForm']->sendArray[':'.$value['groupName']]);
                unset($data->output['userForm']->sendArray[':'.$value['groupName'].'_expiration']);
                unset($data->output['userForm']->sendArray[':'.$value['groupName'].'_update']);
            }
			$statement=$db->prepare('insertUser','admin_users');
			$result=$statement->execute($data->output['userForm']->sendArray);
            $statement=$db->prepare('getUserIdByName');
            $statement->execute(array(
                ':name' => $data->output['userForm']->sendArray[':name']
            ));
            $userID=$statement->fetchAll();
            // Insert Permissions
            foreach($data->permissions as $category => $permissions) {
                foreach($permissions as $permissionName => $permissionDescription) {
                    if(isset($submittedPermissions[':'.$category.'_'.$permissionName])) {
                        if($submittedPermissions[':'.$category.'_'.$permissionName]!=='Inherited') {
                            $allow=0;
                            if($submittedPermissions[':'.$category.'_'.$permissionName]=='Allow') {
                                $allow=1;
                            }
                            // Add it to the database
                            $statement=$db->prepare('addPermissionsByUserId');
                            $statement->execute(array(
                                ':id' => $userID[0]['id'],
                                ':permission' => $category.'_'.$permissionName,
                                ':allow' => $allow
                            ));
                        }
                    }
                }
            }

            foreach($submittedGroups as $groupName => $value) {
                $statement=$db->prepare('addPermissionGroup');
                $statement->execute(array(
                    ':userID'    => $userID[0]['id'],
                    ':groupName' => $groupName,
                    ':expires'   => 0
                ));
            }
			if($result == FALSE) {
				$data->output['savedOkMessage'] = 'There was an error in saving to the database';
				return;
			}

			$id = $db->lastInsertId();
			$profileAlbum = $db->prepare('addAlbum', 'gallery');
			$profileAlbum->execute(array(':user' => $id, ':name' => 'Profile Pictures', ':shortName' => 'profile-pictures', 'allowComments' => 0));

			// All Is Good
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
	}
}

function admin_usersShow($data)
{
	if (isset($data->output['pagesError']) && $data->output['pagesError'] == 'unknown function') {
		admin_unknown();
	} else if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['userForm']);
	}
}
?>