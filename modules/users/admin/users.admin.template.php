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
function theme_usersUnbanConfirm($data) {
	echo '
		  <form action="',$data->linkRoot,'admin/users/unban/',$userId,'" method="post" class="verifyForm">
			  <fieldset>
			  	  <legend><span>',$data->phrases['users']['unbanConfirmMessage'],'  - ',$data->output['userItem']['name'],'</span></legend>
				  <input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
				  <input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
				  <input type="hidden" name="fromForm" value="',$data->output['userItem']['id'],'" />
			  </fieldset>
		  </form>';
}

function theme_usersSearchTableHead($data) {
	echo '
		<table class="userList">
			<caption>
				',$data->phrases['users']['searchResults'],'
			</caption>
			<thead>
				<tr>
					<th class="id">ID</th>
					<th class="userName">',$data->phrases['users']['username'],'</th>
					<th class="firstName">',$data->phrases['users']['firstName'],'</th>
					<th class="lastName">',$data->phrases['users']['lastName'],'</th>
					<th class="contactEmail">',$data->phrases['users']['contactEmail'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_usersSearchTableRow($userId,$userName,$firstName,$lastName,$contactEMail,$linkRoot,$key) {
	echo '
		<tr class="',($key%2==0 ? 'even' : 'odd'),'">
			<td class="id">',$userId,'</td>
			<td class="userName">
				<a href="'.$linkRoot.'admin/users/edit/',$userId,'">
					',$userName,'
				</a>
			</td>
			<td class="firstName">',$firstName,'</td>
			<td class="lastName">',$lastName,'</td>
			<td class="contactEmail">',$contactEMail,'</td>
			<td class="buttonList">
				<a href="'.$linkRoot.'admin/users/edit/'.$userId.'">Edit</a>
				<a href="'.$linkRoot.'admin/users/delete/'.$userId.'">Delete</a>
			</td>
		</tr>';
}

function theme_usersSearchTableFoot() {
	echo '
				</tbody>
			</table>
		';
}

function theme_usersListTableHead($data) {
	$userList = $data->output['userList'];
	$userListStart = $data->output['userListStart'];
	echo '
		<table class="userList">
			<caption>
				Users
				',$userListStart+1,' through
				',$userListStart+count($userList),'
			</caption>
			<thead>
				<tr>
					<th class="id">',$data->phrases['users']['id'],'</th>
					<th class="userName">',$data->phrases['users']['username'],'</th>
					<th class="firstName">',$data->phrases['users']['firstName'],'</th>
					<th class="lastName">',$data->phrases['users']['lastName'],'</th>
					<th class="contactEmail">',$data->phrases['users']['contactEmail'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_usersListTableRow($data,$userId,$userName,$firstName,$lastName,$contactEMail,$linkRoot,$key,$admin,$self) {
	echo '';
    echo '
		<tr class="',($key%2==0 ? 'even' : 'odd'),'">
			<td class="id">',$userId,'</td>
			<td class="userName">
				<a href="'.$linkRoot.'admin/users/edit/',$userId,'">
					',$userName,'
				</a>
			</td>
			<td class="firstName">',$firstName,'</td>
			<td class="lastName">',$lastName,'</td>
			<td class="contactEmail">',$contactEMail,'</td>
			<td class="buttonList">';
    if(!$admin) {
		echo '<a href="'.$linkRoot.'admin/users/edit/'.$userId.'">'.$data->phrases['core']['actionEdit'].'</a>
			  <a href="'.$linkRoot.'admin/users/delete/'.$userId.'">'.$data->phrases['core']['actionDelete'].'</a>';
    } else {
        if($self) {
            echo '<a href="'.$linkRoot.'admin/users/edit/'.$userId.'">'.$data->phrases['core']['actionEdit'].'</a>
                  <a href="'.$linkRoot.'admin/users/delete/'.$userId.'">'.$data->phrases['core']['actionDelete'].'</a>';
        }
    }
    echo '</td></tr>';
}

function theme_usersListTableFoot($data) {
	echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/users/add">',$data->phrases['users']['addUser'],'</a>
		</div>';
}

function theme_GroupsListTableHead($data) {
    echo '
		<table class="userList">
			<caption>
				',$data->phrases['users']['listGroupsHeading'],'
			</caption>
			<thead>
				<tr>
					<th class="userName">',$data->phrases['users']['groups'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_GroupsListTableRow($data,$groupName,$linkRoot,$key) {
    echo '
		<tr class="',($key%2==0 ? 'even' : 'odd'),'">
			<td class="userName">',
                (($groupName=='Administrators')? $groupName:'
				<a href="'.$linkRoot.'admin/users/permissions/group/edit/'.$groupName.'">
					'.$groupName.'
				</a>'),
			'</td>
			<td class="buttonList">',
			    (($groupName=='Administrators')? '':'
			    <a href="'.$linkRoot.'admin/users/permissions/group/delete/'.$groupName.'">'.$data->phrases['core']['actionDelete'].'</a>'),
			'</td>
		</tr>';
}

function theme_GroupsListTableFoot($data) {
    echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/users/permissions/group/add/">',$data->phrases['users']['addGroup'],'</a>
		</div>';
}

function theme_usersDeleteDeleted($data) {
	echo '
		<h2>',$data->phrases['users']['deleteUserSuccessHeading'],'</h2>
		<p>
			',$data->phrases['users']['deleteUserSuccessMessage'],' ',$data->output['deleteCount'],'
		</p>
		<div class="buttonList">
			<a href="',$data->linkRoot,'admin/users/list">',$data->phrases['users']['returnToUserList'],'</a>
		</div>
		';
}

function theme_usersDeleteCancelled($data) {
	echo '
		<h2>',$data->phrases['users']['deleteUserCancelledHeading'],'</h2>
		<p>
			',$data->phrases['core']['messageRedirect'],'
			<a href="',$data->linkRoot,'admin/users/list">',$data->phrases['core']['linkSkipWait'],'</a>
		</p>';
}

function theme_usersDeleteDefault($data) {
	echo '
		<form action="',$data->linkRoot,'admin/users/delete/',$data->action[3],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>',$data->phrases['users']['deleteUserConfirmHeading'],' - ',$data->action[3],'</span></legend>
				<p class="warning">',$data->phrases['users']['deleteUserConfirmMessage'],'</p>
				<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
				<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
				<input type="hidden" name="fromForm" value="',$data->action[3],'" />
			</fieldset>
		</form>';
}
function theme_groupDeleteDeleted($data) {
    echo '
    	<h2>',$data->phrases['users']['deleteGroupSuccessHeading'],'</h2>
		<p>
			',$data->phrases['users']['deleteGroupSuccessMessage'],' - ',$data->action[5],'
		</p>
		<div class="buttonList">
			<a href="',$data->linkRoot,'admin/users/permissions">',$data->phrases['users']['returnToGroupList'],'</a>
		</div>
		';
}

function theme_groupDeleteCancelled($linkRoot) {
    echo '
		<h2>',$data->phrases['users']['deleteGroupCancelledHeading'],'</h2>
		<p>
			',$data->phrases['core']['messageRedirect'],'
			<a href="',$data->linkRoot,'admin/users/list">',$data->phrases['core']['linkSkipWait'],'</a>
		</p>';
}

function theme_groupDeleteDefault($data) {
    echo '
		<form action="',$data->linkRoot,'admin/users/permissions/group/delete/',$data->action[5],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>',$data->phrases['users']['deleteGroupConfirmHeading'],' - ',$data->action[5],'</span></legend>
				<p class="warning">*** WARNING *** This action cannot be undone</p>
				<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
				<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
				<input type="hidden" name="fromForm" value="',$data->action[5],'" />
			</fieldset>
		</form>';
}
function theme_usersActivationNone() {
	echo '<p>',$data->phrases['users']['noActivationsFound'],'</p>';
}

function theme_usersActivationTableHead($data) {
	echo '
			<table class="userList">
				<caption>
					',$data->phrases['users']['users'],'
					',$data->output['userListStart']+1,' through
					',$data->output['$userListStart']+count($userList),'
				</caption>
				<thead>
					<tr>
						<th class="id">',$data->phrases['users']['id'],'</th>
						<th class="userName">',$data->phrases['users']['username'],'</th>
						<th class="Hash">',$data->phrases['users']['hash'],'</th>
						<th class="Expires">',$data->phrases['users']['expires'],'</th>
						<th class="Activate">',$data->phrases['core']['controls'],'</th>
					</tr>
				</thead>
				<tbody>
		';
}

function theme_usersActivationTableRow($data,$user,$userListStart,$linkRoot,$key) {
	echo '
			<tr class="',($key%2==0 ? 'even' : 'odd'),'">
				<td class="id">',$user['id'],'</td>
				<td class="userName">
					<a href="',$linkRoot,'admin/users/edit/',$user['id'],'">
						',$user['name'],'
					</a>
				</td>
				<td class="Hash">',$user['hash'],'</td>
				<td class="Expires">',$user['expires'],'</td>
				<td class="Activate">
					<a href="',$linkRoot,'admin/users/activation/',$userListStart,'/activate/',$user['id'],'">',$data->phrases['users']['activate'],'</a>
				</td>
			</tr>';
}

function theme_usersActivationTableFoot() {
	echo '
				</tbody>
			</table>
			';
}

?>