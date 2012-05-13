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
function page_getUniqueSettings($data) {
	$data->output['pageShortName']='gallery';
}
function page_buildContent($data,$db) {
	//permission check for friends access
	if(!checkPermission('access','friends',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}

	$friendsHome = $data->currentPage;
	if($data->request[0] != $data->action[0]){
		$friendsHome = $data->request[0];
	}
	$data->output['friendsHome'] = $data->linkRoot . $friendsHome . '/';
	$pageType = 'default';
	$data->output['pageType'] = &$pageType;
	switch($data->action[1]){
		case 'requests':
			$pageType = 'requests';
			if($data->action[2] == 'accept' || $data->action[2] == 'ignore'){
				$userStatement = $db->prepare('getUserByName', 'users');
				$userStatement->execute(array(':name' => $data->action[3]));
				$user = $userStatement->fetch();
				if($user !== false){
					if($data->action[2] == 'accept'){
						$friendStatement = $db->prepare('acceptRequest', 'friends');
					}else if($data->action[2] == 'ignore'){
						$friendStatement = $db->prepare('ignoreRequest', 'friends');
					}
					$friendStatement->execute(array('user1' => $user['id'], 'user2' => $data->user['id']));
					common_redirect_local($data, 'users/' . $user['name']);
				}
			}
			$requestList = $db->prepare('getRequestsByUser', 'friends');
			$requestList->execute(array('user' => $data->user['id']));
			$data->output['requests'] = $requestList->fetchAll();
			break;
		case 'makeRequest':
			if(!isset($data->user['id'])){
				$pageType = 'accessDenied';
				return;
			}else if($data->action[2] === false){
				$pageType = 'noUserSpecified';
				return;
			}
			$userStatement = $db->prepare('getUserByName', 'users');
			$userStatement->execute(array(':name' => $data->action[2]));
			$user = $userStatement->fetch();
			if($user === false){
				$pageType = 'userNotFound';
				return;
			}
			$request = $db->prepare('makeRequest', 'friends');
			$request->execute(array(':user1' => $data->user['id'], ':user2' => $user['id']));
			common_redirect_local($data, 'users/'.$user['name']);
			break;
		default:
			$friendList = $db->prepare('getFriendsByUser', 'friends');
			$friendList->execute(array(':user' => $data->user['id']));
			$data->output['friends'] = $friendList->fetchAll();
			$form = $data->output['friendsearch'] = new formHandler('friendSearch', $data);
			if (!empty($_POST['fromForm']) && $_POST['fromForm'] == $form->fromForm){
				if ($form->validateFromPost()) {
					$form->populateFromPostData();
					$data->output['search'] = $form->sendArray[':name'];
					$find = $db->prepare('findFriends', 'friends');
					$find->execute(array('name' => '%' . $data->output['search'] . '%'));
					$data->output['results'] = $find->fetchAll();
					$pageType = 'results';
				}
			}
			break;
	}
}
function page_content(&$data) {
	common_include($data->themeDir . 'formGenerator.template.php');
	switch($data->output['pageType']){
		case 'results':
			theme_searchResults($data);
			break;
		case 'requests':
			theme_viewRequests($data);
			break;
		default:
			theme_friendList($data);
			break;
	}
	//var_dump($data->output['links']);
}
?>