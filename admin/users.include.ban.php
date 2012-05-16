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
function admin_usersBuild($data,$db) {
	//permission check for users ban
	if(!checkPermission('ban','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	$userId = $data->action[3];
	// Check That User Exists And Can Be Banned
	$statement = $db->prepare('getById','admin_users');
	$statement->execute(array(
		':id' => $userId
	));
	if(($data->output['userItem'] = $userItem = $statement->fetch()) == FALSE) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Invalid User ID</h2>The user you specified could not be found';
		return;
	}
	// Cannot Ban Anything Admin Or Greater
	if($userItem['userLevel'] >= USERLEVEL_ADMIN) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>User Cannot Be Banned</h2>The user you specified is an administrator and cannot be banned';
		return;
	}
	// Cannot Ban A User Already Banned
	if($userItem['userLevel'] < USERLEVEL_USER) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>User Already Banned</h2>';
		return;
	}
	// Build Ban Form //
	$data->output['banForm'] = $form = new formHandler('banForm',$data,true);
	
	//--Handle Post Form--//
	if(!empty($_POST['fromForm']) && ($_POST['fromForm'] == $form->fromForm)) {
		$form->populateFromPostData();
		$emailResult = NULL;
		$ipResult = NULL;
		//--Are We Banning The Email Address?--//
		$sqlVars[':email'] = ($form->sendArray[':banEmail']) ? $userItem['contactEMail'] : '';
			
		$emailResult = ($form->sendArray[':banEmail']) ? '<br />The email address <b>'.$userItem['contactEMail'].'</b> has been banned' : NULL;
			
		//--Are We Banning The IP Address?--//
		$sqlVars[':ip'] = ($form->sendArray[':banIp']) ? $userItem['registeredIP'] : '';
		
		$ipResult = ($form->sendArray[':banIp']) ? '<br />The IP address <b>'.$userItem['registeredIP'].'</b> has been banned' : NULL;

		//--Shift User To Ban Group--//
		$statement = $db->prepare('updateUserLevel','admin_users');
		$statement->execute(array(
			':userId' => $userItem['id'],
			':userLevel' => USERLEVEL_BANNED
		));
		
		//--Count Ban Expiration--//
		$sqlVars[':expiration'] = time() + ($form->sendArray[':banTime'] * $form->sendArray[':banUnit']);
		
		//--Insert Into Banned Table--//
		$sqlVars[':userId'] = $userItem['id'];
		$sqlVars[':userLevel'] = $userItem['userLevel'];
		$statement = $db->prepare('addBan','admin_users');
		$statement->execute($sqlVars) or die("WTF BRO");
		
		$data->output['savedOkMessage'] = '
			<h2>User Banned</h2>
			The user '.$userItem['name'].' has been banned.
			'.$emailResult.$ipResult;
	}
}


function admin_usersShow($data)
{
	if(isset($data->output['savedOkMessage']))
	{
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['banForm']);
	}
}
?>