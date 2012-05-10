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
	$data->output['pageShortName']='friends';
}
function page_buildContent($data,$db) {
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
			}elseif($data->action[2] === false){
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
			if(!empty($_POST['fromForm']) && $_POST['fromForm'] == $form->fromForm){
                // need add check here to see what fields were submited and run quiries based on the input
				if($form->validateFromPost()) {
					$form->populateFromPostData();
                    if(!empty($form->sendArray[':userName']) && !empty($form->sendArray[':fullName']) && !empty($form->sendArray[':publicEmail'])) {
                        $find = $db->prepare('findFriendsByAllFields','friends');
                        $data->output['search']=$form->sendArray;
                        $find->execute(array('name' => '%'.$form->sendArray[':userName'].'%',
                                             'fullName' => '%'.$form->sendArray[':fullName'].'%',
                                             'publicEmail' => '%'.$form->sendArray[':publicEmail'].'%'));
                        $data->output['results']=$find->fetchAll();
                    } elseif(!empty($form->sendArray[':userName']) && !empty($form->sendArray[':fullName'])) {
                        // :userName and :fullName were filled out, send search by userName and fullName
                        $data->output['search']=$form->sendArray;
                        $find = $db->prepare('findFriendsByUserNameAndFullName','friends');
                        $find->execute(array('name' => '%'.$form->sendArray[':userName'].'%',
                            'fullName' => '%'.$form->sendArray[':fullName'].'%'));
                        $data->output['results']=$find->fetchAll();
                    } elseif(!empty($form->sendArray[':publicEmail']) && !empty($form->sendArray[':fullName'])) {
                        // :publicEmail and :fullName were filled out, send search by publicEmail and fullName
                        $data->output['search']=$form->sendArray;
                        $find = $db->prepare('findFriendsByPublicEmailAndFullName','friends');
                        $find->execute(array('publicEmail' => '%'.$form->sendArray[':publicEmail'].'%',
                            'fullName' => '%'.$form->sendArray[':fullName'].'%'));
                        $data->output['results']=$find->fetchAll();
                    } elseif(!empty($form->sendArray[':userName']) && !empty($form->sendArray[':publicEmail'])) {
                        // :userName and :publicEmail were filled out, send search by userName and publicEmail
                        $data->output['search']=$form->sendArray;
                        $find = $db->prepare('findFriendsByUserNameAndPublicEmail','friends');
                        $find->execute(array('name' => '%'.$form->sendArray[':userName'].'%',
                            'publicEmail' => '%'.$form->sendArray[':publicEmail'].'%'));
                        $data->output['results']=$find->fetchAll();
                    } else {
                        // one field submitted check which one and set correct data
                        if(!empty($form->sendArray[':userName'])) {
                            // :userName was filled out, send search by userName
                            $data->output['search']=$form->sendArray[':userName'];
                            $find = $db->prepare('findFriends','friends');
                            $find->execute(array('name' => '%'.$data->output['search'].'%'));
                            $data->output['results']=$find->fetchAll();
                        } elseif(!empty($form->sendArray[':fullName'])) {
                            // :fullName was filled out, send search by fullName
                            $data->output['search']=$form->sendArray[':fullName'];
                            $find = $db->prepare('findFriendsByFullName','friends');
                            $find->execute(array('fullName' => '%'.$data->output['search'].'%'));
                            $data->output['results']=$find->fetchAll();
                        } elseif(!empty($form->sendArray[':publicEmail'])) {
                            // :publicEmail was filled out, send search by publicEmail
                            $data->output['search']=$form->sendArray[':publicEmail'];
                            $find = $db->prepare('findFriendsByPublicEmail','friends');
                            $find->execute(array('publicEmail' => '%'.$data->output['search'].'%'));
                            $data->output['results']=$find->fetchAll();
                        }
                    }
					$pageType = 'results';
				} else {
                    $data->ouput['secondSideBar']='
                    <h2>Error in Data</h2>
                    <p>
                    There were one or more errors. Please correct the fields with the red X next to them and try again.
                    </p>';
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