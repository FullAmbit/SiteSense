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
	//permission check for users access
	if(!checkPermission('access','users',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	$data->output['userForm']=new formHandler('search',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['userForm']->fromForm)
	) {
		$data->output['userForm']->populateFromPostData();
		$statement = $db->prepare('searchUsers','admin_users');
		$statement->execute($data->output['userForm']->sendArray);
		$data->output['userList'] = $statement->fetchAll();
	}else{
		$data->output['userList'] = false;
	}
}
function admin_usersShow($data) {
	global $languageText;
	theme_buildForm($data->output['userForm']);
	if($data->output['userList'] !== false){
		theme_usersSearchTableHead();
		foreach($data->output['userList'] as $key => $user) {
			theme_usersSearchTableRow($user['id'],$user['name'],$user['firstName'],$user['lastName'],$user['contactEMail'],'',$data->linkRoot,$key);
		}
		theme_usersSearchTableFoot();
	}
}
?>
