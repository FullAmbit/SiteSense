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
// Copyright (C) 2012 Nathan Wong
// See 'acknowledgment.txt' for more details
function populateTimeZones($data) {
	$currentTime=time();
	$times=array();
	$start=$currentTime-date('G', $currentTime)*3600;
	for ($i=0;$i<24*60;$i+=15) {
		$times[date('g:i A', $start+$i*60)]=array();
	}
	$timezones=DateTimeZone::listIdentifiers();
	foreach ($timezones as $timezone) {
		$dt=new DateTime('@'.$currentTime);
		$dt->setTimeZone(new DateTimeZone($timezone));
		$time=$dt->format('g:i A');
		$times[$time][]=$timezone;
	}
	$timeZones=array_filter($times);
	foreach ($timeZones as $time => $timeZoneList) {
		foreach ($timeZoneList as $timeZone) {
			$data->output['timeZones'][]=array(
				'text'  => $time.' - '.$timeZone,
				'value' => $timeZone
			);
		}
	}
}
// End Credit

function checkUserName($name, $db) {
	$statement=$db->prepare('checkUserName', 'admin_users');
	$statement->execute(array(
			':name' => $name
		));
	return $statement->fetchColumn();
}

function admin_usersBuild($data, $db) {
	//permission check for users add
	if (!checkPermission('add', 'users', $data)) {
		$data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	// Load all groups
	$db->query('purgeExpiredGroups');
	$statement=$db->query('getAllGroups', 'admin_users');
	$data->output['groupList']=$statement->fetchAll();
	sort($data->output['groupList']);

	// Add core control panel access permission
    $data->permissions['core']=array(
        'access'        => 'Control panel access'
    );
	// Poulate Time Zone List
	populateTimeZones($data);
	$data->output['userForm'] = $form = new formHandler('addEdit', $data, true);

	unset($form->fields['registeredDate']);
	unset($form->fields['registeredIP']);
	unset($form->fields['lastAccess']);

	$form->fields['password']['required'] = true;
	$form->fields['password2']['required'] = true;

	$form->caption = 'Add A User';
	// Handle Form Submission
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['userForm']->fromForm)) {
		// Populate From Post //
		$form->populateFromPostData();
		// Check If UserName Already Exists //
		$existing = checkUserName($form->sendArray[':name'], $db);
		if ($existing) {
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';

			$data->output['userForm']->fields['name']['error'] = true;

			return;
		}

		// Did it validate?!?
		if (($form->validateFromPost())) {
			// Make Sure We Have A Password..
			if (empty($data->output['userForm']->sendArray[':password'])) {
				$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p><p><strong>'.$data->phrases['core']['addUserErrorRequirePassword'].'</strong></p>';

				$data->output['userForm']->fields['password']['error']=true;

				return;
			}
			// Don't need this....
			unset($data->output['userForm']->sendArray[':password2']);
			unset($data->output['userForm']->sendArray[':id']);

			$data->output['userForm']->sendArray[':registeredIP']=$_SERVER['REMOTE_ADDR'];
			$data->output['userForm']->sendArray[':password']=hash('sha256', $data->output['userForm']->sendArray[':password']);
			foreach ($data->permissions as $category => $permissions) {
				if (!isset($permissions['permissions'])) {
					$permissions['permissions']='Manage Permissions';
				}
				foreach ($permissions as $permissionName => $permissionDescription) {
					if (isset($data->output['userForm']->sendArray[':'.$category.'_'.$permissionName])) {
						$submittedPermissions[':'.$category.'_'.$permissionName]=$data->output['userForm']->sendArray[':'.$category.'_'.$permissionName];
						unset($data->output['userForm']->sendArray[':'.$category.'_'.$permissionName]);
					}
				}
			}
			$submittedGroups=array();
			foreach ($data->output['groupList'] as $value) {
				if ($data->output['userForm']->sendArray[':'.$value['groupName']]=='checked') {
					// User is still a member
					// Check expiration
					$submittedGroups[$value['groupName']]['expires']=$data->output['userForm']->sendArray[':'.$value['groupName'].'_update'];

				}
				unset($data->output['userForm']->sendArray[':'.$value['groupName']]);
				unset($data->output['userForm']->sendArray[':'.$value['groupName'].'_expiration']);
				unset($data->output['userForm']->sendArray[':'.$value['groupName'].'_expiration_hidden']);
				unset($data->output['userForm']->sendArray[':'.$value['groupName'].'_update']);
				unset($data->output['userForm']->sendArray[':manageGroups_'.$value['groupName']]);

			}
			unset($data->output['userForm']->sendArray[':id_hidden']);
			unset($data->output['userForm']->sendArray[':registeredDate_hidden']);
			unset($data->output['userForm']->sendArray[':registeredIP_hidden']);
			unset($data->output['userForm']->sendArray[':lastAccess_hidden']);
			$statement=$db->prepare('insertUser', 'admin_users');
			$result=$statement->execute($data->output['userForm']->sendArray);
			$statement=$db->prepare('getUserIdByName');
			$statement->execute(array(
					':name' => $data->output['userForm']->sendArray[':name']
				));
			$userID=$statement->fetchAll();
			// Insert Permissions
			foreach ($data->permissions as $category => $permissions) {
				if (!isset($permissions['permissions'])) {
					$permissions['permissions']='Manage Permissions';
				}
				foreach ($permissions as $permissionName => $permissionDescription) {
					if (isset($submittedPermissions[':'.$category.'_'.$permissionName]) && $submittedPermissions[':'.$category.'_'.$permissionName] !== '0') {
						$value = $submittedPermissions[':'.$category.'_'.$permissionName];

						// Add it to the database
						$statement=$db->prepare('addPermissionsByUserId');
						$statement->execute(array(
							':id' => $userID[0]['id'],
							':permission' => $category.'_'.$permissionName,
							':value' => $value
						));
					}
				}
			}

			foreach ($submittedGroups as $groupName => $value) {
				// Add expiration dropdown box to the current time stamp
				$expires=0;
				$dropdown=$submittedGroups[$groupName]['expires'];
				switch ($dropdown) {
				case 'No change':
				case 'Never':
					$expires=0;
					break;
				case '15 minutes':
					$expires=900;
					break;
				case '1 hour':
					$expires=3600;
					break;
				case '2 hours':
					$expires=7200;
					break;
				case '1 day':
					$expires=86400;
					break;
				case '2 days':
					$expires=172800;
					break;
				case '1 week':
					$expires=604800;
					break;
				}
				if ($expires==0) {
					$statement=$db->prepare('addUserToPermissionGroupNoExpires');
					$statement->execute(array(
							':userID'          => $userID[0]['id'],
							':groupName'       => $groupName
						));
				} else {
					$statement=$db->prepare('addUserToPermissionGroup');
					$statement->execute(array(
							':userID'          => $userID[0]['id'],
							':groupName'       => $groupName,
							':expires'         => $expires
						));
				}
			}
			if ($result == FALSE) {
				$data->output['savedOkMessage'] = $data->phrases['users']['addUserErrorDatabase'];
				return;
			}

			$id = $db->lastInsertId();
			$profileAlbum = $db->prepare('addAlbum', 'gallery');
			$profileAlbum->execute(array(':userId' => $id, ':name' => 'Profile Pictures', ':shortName' => 'profile-pictures', 'allowComments' => 0));
			// All Is Good
			$data->output['savedOkMessage']='
					<h2>'.$data->phrases['users']['addUserSuccessMessage'].' - <em>'.$data->output['userForm']->sendArray[':name'].'</em></h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/users/add">
							'.$data->phrases['users']['addUser'].'
						</a>
						<a href="'.$data->linkRoot.'admin/users/list/">
							'.$data->phrases['users']['returnToUserList'].'
						</a>
					</div>';
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