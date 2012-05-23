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
function admin_usersBuild($data,$db)
{
	$userId = $data->action[3];
	
	//permission check for users ban
	if(!checkPermission('ban','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	
	// Get User, Make Sure He / She Is Already Banned
	$statement = $db->prepare('getById','users');
	$statement->execute(array(
		':id' => $userId
	));
	
	$data->output['userItem'] = $userItem = $statement->fetch();
	
	// Get The Ban Row
	$statement = $db->prepare('getBanByUserId','users');
	$statement->execute(array(
		':userId' => $userId
	));
	$data->output['banItem'] = $banItem = $statement->fetch();
	
	if($userItem == FALSE)
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Invalid Parameters</h2>The user you specified could not be found';
		return;
	}
	
	if($userItem['userLevel'] > USERLEVEL_BANNED || $banItem == FALSE)
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>User Is Not Banned</h2>';
		return;
	}
	
	// Post?
	if(isset($_POST['fromForm']) && $_POST['fromForm'] == $userItem['id'])
	{
		// If Cancel, Display The Return Form
		if(isset($_POST['cancel']))
		{
			$data->output['savedOkMessage'] = '
				<h2>Operation cancelled</h2>
				<p>
					<div class="buttonList">
						<a href="'.$data->linkRoot.'admin/users/list/">Return To User List</a>
					</div>
				</p>';
				return;
		}
		// Just to be safe...don't allow anything besides a yes.
		if($_POST['delete'] !== 'Yes')
		{
			return;
		}
		// Unban The User (Delete Banning Row Should Do The Trick, And Update User Level!
		$statement = $db->prepare('removeBanByUserId','users');
		$r1 = $statement->execute(array(
			':userId' => $userId
		));
		$update = $db->prepare('updateUserLevel','users');
		$r2 = $update->execute(array(
			':userId' => $userId,
			':userLevel' => $banItem['userLevel']
		));
		
		if($r1 && $r2)
		{
			$data->output['savedOkMessage'] = 
			'<h2>User Unbanned</h2>
			<p>
				<div class="buttonList">
					<a href="'.$data->linkRoot.'admin/users/list/">Return To User List</a>
				</div>
			</p>';
		} else {
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>Database Error</h2>There was an error in updating the database';
		}
	}
}

function admin_usersShow($data)
{
	if(isset($data->output['savedOkMessage']))
	{
		echo $data->output['savedOkMessage'];
	} else {
		// Show Form //
		theme_usersUnbanConfirm($data->output['userItem']['id'],$data->output['userItem']['name'],$data->linkRoot);
	}
}
?>