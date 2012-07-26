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

function admin_usersBuild($data,$db) {
	//permission check for users permissions
	if(!checkPermission('groups','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	if(empty($data->action[3])){ // Display List of Groups
        $statement=$db->query('getAllGroups','admin_users');
        $data->output['groupList']=$statement->fetchAll();
    } elseif($data->action[3]=='group') {
        if($data->action[4]=='add') { //Add a new Group
            getPermissions($data,$db);
            $data->output['permissionGroup']=new formHandler('permissionGroup',$data,true);
            // Add Group Form Submitted
            if((!empty($_POST['fromForm']))&&($_POST['fromForm']==$data->output['permissionGroup']->fromForm)) {
                $data->output['permissionGroup']->populateFromPostData();
                // Check if groupName exists already
                $existing=false;
                $statement=$db->prepare('getGroupsByGroupID');
                $statement->execute(array(
                    ':groupName' => $data->output['permissionGroup']->sendArray[':groupName']
                ));
                if ($statement->fetchColumn()) {
                    // Returned result, groupName already exists
                    $existing=true;
                }

                if($existing) {
                    $data->output['secondSidebar']='
                      <h2>Error in Data</h2>
                      <p>
                          There were one or more errors. Please correct the fields with the red X next to them and try again.
                      </p><p>
                          <strong>That group name is already taken!</strong>
                      </p>';
                    $data->output['permissionGroup']->fields['groupName']['error']=true;
                    return;
                }
                foreach($data->output['permissionGroup']->sendArray as $fieldName => $value) {
                    if($fieldName!==':groupName' && $fieldName!==':expiration') {
                        // Check to see if it is checked or not
                        if($value) {
                            $permissions[]=substr($fieldName,1);
                        }
                    }
                }
                foreach($permissions as $permission) {
                    $statement=$db->prepare('addPermissionByGroupName');
                    $result = $statement->execute(array(
                        ':groupName'      => $data->output['permissionGroup']->sendArray[':groupName'],
                        ':permissionName' => $permission

                    ));
                    if($result==FALSE) {
                        $data->output['savedOkMessage'] = 'There was an error in saving to the database';
                        return;
                    }
                }
                $data->output['savedOkMessage']='
					<h2>Group <em>'.$data->output['permissionGroup']->sendArray[':groupName'].'<em> Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/users/permissions/group/add">
							Add New Group
						</a>
						<a href="'.$data->linkRoot.'admin/users/permissions/">
							Return to Group List
						</a>
					</div>';
                return;
            }
        } elseif($data->action[4]=='edit') { // Edit Group
            if($data->action[5]=='Administrators') {
                $data->output['abort'] = true;
                $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
                return;
            }
            getPermissions($data,$db);
            // Check To See If The Group Exists
            $statement=$db->query('getAllGroups','admin_users');
            $groupList=$statement->fetchAll(PDO::FETCH_ASSOC);
            $existing=false;
            foreach($groupList as $key => $value) {
                if($groupList[$key]['groupName']==$data->action[5]) {
                    $existing=true;
                    break;
                }
            }
            if(!$existing) {
                $data->output['abort'] = true;
                $data->output['abortMessage']='The group you specified could not be found';
                return;
            }
            
             // Get Group Permissions
            $statement=$db->prepare('getPermissionsByGroupName');
            $statement->execute(array(
                ':groupName' =>  $data->action[5]
            ));
            $permissionList=$statement->fetchAll(PDO::FETCH_ASSOC);
            foreach($permissionList as $permissionItem) {
            	list($prefix,$suffix) = parsePermissionName($permissionItem['permissionName']);
                $data->output['permissionList'][$prefix][$suffix]['value'] = $permissionItem['value'];
            }            
            $data->output['permissionGroup'] = new formHandler('permissionGroup',$data,true);
            
            // Edit Group Form Submitted
            if((!empty($_POST['fromForm']))&&($_POST['fromForm']==$data->output['permissionGroup']->fromForm)) {
                $data->output['permissionGroup']->populateFromPostData();
                // Check if groupName exists already
                if($data->output['permissionGroup']->sendArray[':groupName']!==$data->action[5]) {
                    $statement=$db->prepare('getGroupName');
                    $statement->execute(array(
                        ':groupName' => $data->action[5]
                    ));
                    if($statement->fetchColumn()) {
                        // Returned result, groupName already exists
                        $data->output['secondSidebar']='
                        <h2>Error in Data</h2>
                        <p>
                            There were one or more errors. Please correct the fields with the red X next to them and try again.
                        </p><p>
                            <strong>That group name is already taken!</strong>
                        </p>';
                        $data->output['permissionGroup']->fields['groupName']['error']=true;
                          return;
                    }

                    $statement=$db->prepare('updateGroupName');
                    $statement->execute(array(
                        ':groupName' => $data->output['permissionGroup']->sendArray[':groupName'],
                        ':currentGroupName' => $data->action[5]
                    ));
                }
                
                // Purge Existing Group Permissions
                $statement = $db->prepare('purgeAllPermissionsByGroupName');
                $statement->execute(array(
                    ':groupName' => $data->output['permissionGroup']->sendArray[':groupName'],
                ));
                // Start Adding Permissions
                $statement = $db->prepare('addPermissionByGroupName');
                foreach($data->permissions as $category => $permissions){
	                $statement->execute(array(
	                     ':groupName' => $data->output['permissionGroup']->sendArray[':groupName'],
	                     ':permissionName' => $category.'_permissions',
	                     ':value' => $data->output['permissionGroup']->sendArray[':'.$category.'_permissions']
	                ));
	                foreach($permissions as $permissionName => $permissionDescription) {
	                	$statement->execute(array(
	                		':groupName' => $data->output['permissionGroup']->sendArray[':groupName'],
	                     	':permissionName' => $category.'_'.$permissionName,
	                     	':value' => $data->output['permissionGroup']->sendArray[':'.$category.'_'.$permissionName]
	                     ));
	                }
                }

                $data->output['savedOkMessage']='
					<h2>Group <em>'.$data->output['permissionGroup']->sendArray[':groupName'].'<em> Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/users/permissions/group/add">
							Add New Group
						</a>
						<a href="'.$data->linkRoot.'admin/users/permissions/">
							Return to Group List
						</a>
					</div>';
                return;
            }
        } elseif($data->action[4]=='delete') { // Delete Group
            if($data->action[5]=='Administrators') {
                $data->output['abort'] = true;
                $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
                return;
            }
            $data->output['delete']='';
            if(empty($data->action[5])) {
                $data->output['rejectError']='insufficient parameters';
                $data->output['rejectText']='No group name was entered to be deleted';
            } else {
                if (@$_POST['fromForm']==$data->action[5]) {
                    if (!empty($_POST['delete'])) {
                        $success=false;
                        $statement=$db->prepare('removeGroupFromGroup_permissions','admin_users');
                        $statement->execute(array(
                            ':groupName' => $data->action[5]
                        ));
                        if($statement->rowCount()>0) {
                            $success = true;
                        }
                        $statement=$db->prepare('removeGroupFromUsersPermission_groups','admin_users');
                        $statement->execute(array(
                            ':groupName' => $data->action[5]
                        ));
                        if($statement->rowCount()>0) {
                            $success=true;
                        }
                        if($success) {
                            $data->output['delete']='deleted';
                        } else {
                            $data->output['rejectError']='Database Error';
                            $data->output['rejectText']='You attempted to delete a group, are you sure that group exists?';
                        }
                    } else {
                        /* from form plus not deleted must == cancelled. */
                        $data->output['delete']='cancelled';
                    }
                }
            }
        }
    }
}
function admin_usersShow($data) {
    if(empty($data->action[3])){ // Display List of Groups

        theme_GroupsListTableHead();
        foreach($data->output['groupList'] as $key => $group) {
            theme_GroupsListTableRow($group['groupName'],$data->linkRoot,$key);
        }
        theme_GroupsListTableFoot($data->linkRoot);
    } elseif($data->action[3]=='group') {
        if($data->action[4]=='add') { //Add a new Group
            if (isset($data->output['pagesError']) && $data->output['pagesError'] == 'unknown function') {
                admin_unknown();
            } else if (isset($data->output['savedOkMessage'])) {
                echo $data->output['savedOkMessage'];
            } else {
                theme_buildForm($data->output['permissionGroup']);
            }
        } elseif($data->action[4]=='edit') { //Edit Group
            if (isset($data->output['pagesError']) && $data->output['pagesError'] == 'unknown function') {
                admin_unknown();
            } else if (isset($data->output['savedOkMessage'])) {
                echo $data->output['savedOkMessage'];
            } else {
                theme_buildForm($data->output['permissionGroup']);
            }
        } elseif($data->action[4]=='delete') { //Delete Group
            switch ($data->output['delete']) {
                case 'deleted':
                    theme_groupDeleteDeleted($data->action[5],$data->linkRoot);
                    break;
                case 'cancelled':
                    theme_groupDeleteCancelled($data->linkRoot);
                    break;
                default:
                    theme_groupDeleteDefault($data->action[5],$data->linkRoot);
                    break;
            }
        }
    }
}
?>