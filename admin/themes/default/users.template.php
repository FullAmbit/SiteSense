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
function theme_usersUnbanConfirm($userId,$userName,$linkRoot) {
	echo '
		  <form action="',$linkRoot,'admin/users/unban/',$userId,'" method="post" class="verifyForm">
			  <fieldset>
				  <legend><span>Are you sure you want to unban <i>',$userName,'</i></span></legend>
				  <input type="submit" name="delete" value="Yes" />
				  <input type="submit" name="cancel" value="No" />
				  <input type="hidden" name="fromForm" value="',$userId,'" />
			  </fieldset>
		  </form>';
}

function theme_usersSearchTableHead() {
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
}

function theme_usersSearchTableRow($userId,$userName,$userLevel,$userLevelClass,$userLevelText,$linkRoot,$key) {
	echo '
			<tr class="',($key%2==0 ? 'even' : 'odd'),'">
				<td class="id">',$userId,'</td>
				<td class="userName">
					<a href="'.$linkRoot.'admin/users/edit/',$userId,'">
						',$userName,'
					</a>
				</td>
				<td class="userLevel ',$userLevelClass,'">',$userLevelText,'</td>
				<td class="buttonList">',(
			$userLevel==USERLEVEL_ADMIN ? '
					<a href="'.$linkRoot.'admin/users/delete/'.$userId.'">Delete</a>
					<a href="'.$linkRoot.'admin/users/ban/'.$userId.'">Ban</a>' :	''
			),'
				</td>
			</tr>';
}

function theme_usersSearchTableFoot() {
	echo '
				</tbody>
			</table>
		';
}

function theme_usersListTableHead($userList,$userListStart) {
	echo '
		<table class="userList">
			<caption>
				Users
				',$userListStart+1,' through
				',$userListStart+count($userList),'
			</caption>
			<thead>
				<tr>
					<th class="id">ID</th>
					<th class="userName">Username</th>
					<th class="userLevel">Access Level</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
}

function theme_usersListTableRow($userId,$userName,$userLevel,$userLevelClass,$userLevelText,$banControl,$linkRoot,$key) {
	echo '
		<tr class="',($key%2==0 ? 'even' : 'odd'),'">
			<td class="id">',$userId,'</td>
			<td class="userName">
				<a href="'.$linkRoot.'admin/users/edit/',$userId,'">
					',$userName,'
				</a>
			</td>
			<td class="userLevel ',$userLevelClass,'">',$userLevelText,'</td>
			<td class="buttonList">',($userLevel==USERLEVEL_ADMIN ? '
				<a href="'.$linkRoot.'admin/users/delete/'.$userId.'">Delete</a>
				'.$banControl :	''
				),'
			</td>
		</tr>';
}

function theme_usersListTableFoot($linkRoot) {
	echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="',$linkRoot,'admin/users/add">Add New User</a>
		</div>';
}

function theme_GroupsListTableHead() {
    echo '
		<table class="userList">
			<caption>
				Groups
			</caption>
			<thead>
				<tr>
					<th class="userName">Groups</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
}

function theme_GroupsListTableRow($groupName,$linkRoot,$key) {
    echo '
		<tr class="',($key%2==0 ? 'even' : 'odd'),'">
			<td class="userName">
				<a href="',$linkRoot,'admin/user/permissions/group/edit/',$groupName,'">
					',$groupName,'
				</a>
			</td>
			<td class="buttonList">
			    <a href="',$linkRoot,'admin/user/permissions/group/delete/',$groupName,'">Delete</a>
			</td>
		</tr>';
}

function theme_GroupsListTableFoot($linkRoot) {
    echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="'.$linkRoot.'admin/user/permissions/group/add/">Add New Group</a>
		</div>';
}

function theme_usersDeleteDeleted($action3,$deleteCount,$linkRoot) {
	echo '
		<h2>User #',$action3,' Deleted</h2>
		<p>
			This action deleted a total of ',$deleteCount,' users!
		</p>
		<div class="buttonList">
			<a href="',$linkRoot,'admin/users/list">Return to List</a>
		</div>
		';
}

function theme_usersDeleteCancelled($linkRoot) {
	echo '
		<h2>Deletion Cancelled</h2>
		<p>
			You should be auto redirected to the page list in three seconds.
			<a href="',$linkRoot,'admin/users/list">Click Here if you don not wish to wait.</a>
		</p>';
}

function theme_usersDeleteDefault($action3,$linkRoot) {
	echo '
		<form action="',$linkRoot,'admin/users/delete/',$action3,'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>Are you sure you want to delete user #',$action3,'?</span></legend>
				<p class="warning">*** WARNING *** This action cannot be undone</p>
				<input type="submit" name="delete" value="Yes, Delete it" />
				<input type="submit" name="cancel" value="Cancel" />
				<input type="hidden" name="fromForm" value="',$action3,'" />
			</fieldset>
		</form>';
}

function theme_usersActivationNone() {
	echo '<p>There are no users awaiting activation</p>';
}

function theme_usersActivationTableHead($userList,$userListStart) {
	echo '
			<table class="userList">
				<caption>
					Users
					',$userListStart+1,' through
					',$userListStart+count($userList),'
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
}

function theme_usersActivationTableRow($user,$userListStart,$linkRoot,$key) {
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
					<a href="',$linkRoot,'admin/users/activation/',$userListStart,'/activate/',$user['id'],'">Activate</a>
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