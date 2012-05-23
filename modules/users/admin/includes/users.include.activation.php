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
function admin_usersBuild($data,$db) {
	//permission check for users activation
	if(!checkPermission('activate','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	if (empty($data->action[3])) {
		$data->output['userListStart']=0;
	} else if (is_numeric($data->action[3])) {
		$data->output['userListStart']=$data->action[3];
	}
	if(!empty($data->action[4]) && $data->action[4] == 'activate'){
		$userId = (int)$data->action[5];
		if($userId > 0){
			$statement = $db->prepare('activate', 'users');
			$statement->execute(array(':id' => $userId));
		}
	}
	if (empty($data->output['abort'])) {
		$data->output['userListLimit']=ADMIN_SHOWPERPAGE;
		$data->output['userListCount']=0;
		$statement=$db->prepare('getListActivations','users');
		$statement->bindParam(':start',$data->output['userListStart'],PDO::PARAM_INT);
		$statement->bindParam(':count',$data->output['userListLimit'],PDO::PARAM_INT);
		$statement->execute();
		$data->output['userList']=$statement->fetchAll();
	}
}
function admin_usersShow($data) {
	if(empty($data->output['userList'])){
		theme_usersActivationNone();
	}else{
		theme_usersActivationTableHead($data->output['userList'],$data->output['userListStart']);
		foreach($data->output['userList'] as $key => $user) {
			theme_usersActivationTableRow($user,$data->output['userListStart'],$data->linkRoot,$key);
		}
		theme_usersActivationTableFoot();
	}
}
?>
