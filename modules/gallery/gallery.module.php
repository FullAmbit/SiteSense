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

function gallery_buildContent($data,$db) {
	//permission check for gallery access
	if(!checkPermission('access','gallery',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	// In this module, there are many possible combinations
	// of things to be loaded from the database, so this time
	// the approach is first find out what needs to be loaded
	// Then, afterwards, load everything that needs to be loaded.
	//
	//$loadAlbum and $loadImage are null, not false, because they
	//will be assigned a value from $data->action. By default these
	//values are false, so if a user didn't enter in an album name
	//then the program will assume an album isn't supposed to be loaded.
	$loadUserAlbums = false;
	$loadAlbum = null;
	$loadAlbumImages = false;
	$loadImage = null;
	$loadImageComments = false;
	$minimumPermissions = 0;
	define('LOGGEDIN', 1);
	define('OWNER', 2);
	$pageType = 'NotFound';

	// If No User Specified, Then We're Browsing our Own
	if($data->action[1] === FALSE){
		$data->output['user'] = $data->user;
	}else{
		$userName = $data->action[1];
		$statement = $db->prepare('getByName', 'users');
		$statement->execute(array(':name' => $userName));
		$data->output['user'] = $statement->fetch();
		if($data->output['user'] === false){
			$data->output['pageType'] = 'UserNotFound';
			$data->output['userName'] = $userName;
			return;
		}
	}
	
	if(function_exists('theme_getGalleryHome')){
		$data->output['galleryHome'] = theme_getGalleryHome($data);
	}else{
		$data->output['galleryHome'] = $data->linkRoot . implode('/', $data->request) . '/' . $data->output['user']['name'] . '/';
	}
	// What needs to be loaded?
	switch($data->action[2]){
		case 'album':
			switch($data->action[3]){
				case 'view':
					$loadAlbum = $data->action[4];
					$loadAlbumImages = true;
					$pageType = 'AlbumView';
				break;
				case 'edit':
					$loadAlbum = $data->action[4];
					$minimumPermissions = OWNER;
					$pageType = 'AlbumEdit';
				break;
				case 'add':
					$pageType = 'AlbumAdd';
					$minimumPermissions = LOGGEDIN;
				break;
				case 'delete':
					$loadAlbum = $data->action[4];
					$pageType = 'AlbumDelete';
					$minimumPermissions = OWNER;
				break;
				default:
					$loadUserAlbums = true;
					$pageType = 'default';
				break;
			}
			break;
		case 'image':
			switch($data->action[3]){
				case 'view':
					if($data->action[4] !== false){
						if($data->action[5] === false){
							//User wants to view an image from an album, but no image specified
							//So redirect to viewing the album
							common_redirect_local($data, $data->currentPage . '/' . $data->output['user']['name'] . '/album/view/' . $data->action[4]);
						}else{
							//Both an album and an image are specified.
							$loadAlbum = $data->action[4];
							$loadImage = $data->action[5];
							$loadImageComments = true;
							$pageType = 'ImageView';
						}
					}
					break;
				case 'edit':
					if($data->action[4] !== false && $data->action[5] !== false){
						//Both an album and an image are specified.
						$loadAlbum = $data->action[4];
						$loadImage = $data->action[5];
						$pageType = 'ImageEdit';
						$minimumPermissions = OWNER;
					}
					break;
				case 'delete':
					if($data->action[4] !== false && $data->action[5] !== false){
						$loadAlbum = $data->action[4];
						$loadImage = $data->action[5];
						$pageType = 'ImageDelete';
						$minimumPermissions = OWNER;
						break;
					}
				case 'add':
					//no need to load album or image.
					if($data->action[4] !== false){
						$loadAlbum = $data->action[4];
						$pageType = 'ImageAdd';
						$loadUserAlbums = true;
						$minimumPermissions = OWNER;
					}
					break;
				case 'profile':
					if($data->action[4] !== false && $data->action[5] !== false){
						$loadAlbum = $data->action[4];
						$loadImage = $data->action[5];
						$pageType = 'ImageMakeProfile';
						$minimumPermissions = OWNER; 
					}
			}
			break;
		default:
		case false:
			$pageType = 'Default';
			$loadUserAlbums = true;
			break;
	}
	// Now load what needs to be loaded
	if(isset($data->user['id'])){
		$permission = LOGGEDIN;
	}else{
		$permission = 0;
	}
	$prePermissions = ($minimumPermissions > 0) ? 1 : 0;
	if($prePermissions > $permission){
		$pageType = 'AccessDenied';
	}else{
		if($loadUserAlbums){
			$statement = $db->prepare('getAlbumsByUser', 'gallery');
			$statement->execute(array(':userId' => $data->output['user']['id']));
			$data->output['albums'] = $statement->fetchAll();
		}
		if(!is_null($loadAlbum)){
			$statement = $db->prepare('getAlbumByUserAndShortName', 'gallery');
			$statement->execute(array(':userId' => $data->output['user']['id'], ':shortName' => $loadAlbum));
			$album = $statement->fetch();
			if($album === false){
				$pageType = 'AlbumNotFound';
				$data->output['album'] = array(
					'shortName' => $loadAlbum
				);
			}else{
				if($permission == LOGGEDIN && $album['userId'] == $data->user['id']){
					$permission = OWNER;
				}
				if($minimumPermissions > $permission){
					$pageType = 'AccessDenied';
				}else{
					if($loadAlbumImages === true){
						$statement = $db->prepare('getImagesFromAlbum', 'gallery');
						$statement->execute(array(':albumId' => $album['id']));
						$data->output['images'] = $statement->fetchAll();
					}
					if(!is_null($loadImage)){
						$statement = $db->prepare('getImageByAlbumAndName', 'gallery');
						$statement->execute(array(':albumId' => $album['id'], ':shortName' => $loadImage));
						$image = $statement->fetch();
						if($image === false){
							$pageType = 'ImageNotFound';
							$data->output['image'] = array(
								'shortName' => $loadImage
							);
						}else{
							if($loadImageComments){
								$statement = $db->prepare('getImageComments', 'gallery');
								$statement->execute(array('image' => $image['id']));
								$data->output['comments'] = $statement->fetchAll();
							}
							$data->output['image'] = $image;
						}
					}
				}
				$data->output['album'] = $album;
			}
		}
	}
	$data->output['pageType'] = $pageType;
	page_manageForms($data, $db);
	return;
}
function page_manageForms($data, $db){
	switch($data->output['pageType']){
		case 'AlbumAdd':
			$form = $data->output['form'] = new formHandler('album', $data);
			$form->action = $data->output['galleryHome'] . 'album/add';
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					$form->sendArray[':userId'] = $data->user['id'];
					//does this album already exist?
					$statement = $db->prepare('getAlbumByUserAndShortName', 'gallery');
					$statement->execute(array(':userId' => $form->sendArray[':userId'], ':shortName' => $form->sendArray[':shortName']));
					//if so, append a number to the end, e.g. album/view/something-2
					if($statement->fetch() !== false){
						$i = 1;
						do{
							$i++;
							$statement->execute(array(':userId' => $form->sendArray[':userId'], ':shortName' => $form->sendArray[':shortName'] . '-' . $i));
						}while($statement->fetch() !== false);
						$form->sendArray[':shortName']  .= '-' . $i;
					}
					$statement = $db->prepare('addAlbum', 'gallery');
					$statement->execute($form->sendArray);
					common_redirect($data->output['galleryHome'] . 'album/view/' . $form->sendArray[':shortName']);
				}
			}
			break;
		case 'AlbumEdit':
			$form = $data->output['form'] = new formHandler('album', $data);
			$form->action = $data->output['galleryHome'] . 'album/edit/' . $data->output['album']['shortName'];
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					// Load user Id into sendArray
					$form->sendArray[':userId'] = $data->user['id'];
					//does this album already exist?
					$statement = $db->prepare('getAlbumByUserAndShortName', 'gallery');
					$statement->execute(array(':userId' => $form->sendArray[':userId'], ':shortName' => $form->sendArray[':shortName']));
					//if so, append a number to the end, e.g. album/view/something-2
					if(($album = $statement->fetch()) !== false && $album['id'] != $data->output['album']['id']){
						$i = 1;
						do{
							$i++;
							$statement->execute(array(':userId' => $form->sendArray[':userId'], ':shortName' => $form->sendArray[':shortName'] . '-' . $i));
						}while($statement->fetch() !== false);
						$form->sendArray[':shortName']  .= '-' . $i;
					}
					$statement = $db->prepare('editAlbum', 'gallery');
					$form->sendArray[':id'] = $data->output['album']['id'];
					$statement->execute($form->sendArray);
					common_redirect($data->output['galleryHome'] . 'album/view/' . $form->sendArray[':shortName']);
				}
			}
			break;
		case 'ImageAdd':
			$form = $data->output['form'] = new formHandler('addImage', $data);
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost($data)){
					$form->sendArray[':albumId'] = $data->output['album']['id'];
					$form->sendArray[':image'] = $form->fields['image']['images']['full']['saveName'];
					//$form->sendArray[':thumb'] = $form->fields['image']['images']['thumb']['saveName'];
					//$form->sendArray[':icon'] = $form->fields['image']['images']['icon']['saveName'];
					
					$statement = $db->prepare('addImage', 'gallery');
					$statement->execute($form->sendArray);
					$data->output['responseMessage'] = 'Your image has been uploaded. ' . common_generateLink($data,$data->output['galleryHome'].'image/view/'.$data->output['album']['shortName'].'/'.$form->sendArray[':shortName'],'Click here','image_view','/urgallery/'.$data->output['user']['name'].'/image/view/'.$data->output['album']['shortName'].'/'.$form->sendArray[':shortName'],NULL,true) . ' to view it.';
				}else{
				}
			}
			break;
		case 'ImageEdit':
			$form = $data->output['form'] = new formHandler('editImage', $data);
			$form->action = $data->output['galleryHome'] . 'image/edit/' . $data->output['album']['shortName'] . '/' . $data->output['image']['shortName'];
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					$statement = $db->prepare('editImage', 'gallery');
					$form->sendArray[':id'] = $data->output['image']['id'];
					$statement->execute($form->sendArray);
					
					$data->output['responseMessage'] = 'Your changes has been saved. ' . common_generateLink($data,$data->output['galleryHome'].'image/view/'.$data->output['album']['shortName'].'/'.$form->sendArray[':shortName'],'Click here','image_view','/urgallery/'.$data->output['user']['name'].'/image/view/'.$data->output['album']['shortName'].'/'.$form->sendArray[':shortName'],NULL,true) . ' to return to your image.';
				}
			}
			break;
		case 'ImageView':
			$form = $data->output['commentForm'] = new formHandler('imageComment', $data);
			$form->action = $data->output['galleryHome'] . 'image/view/' . $data->output['album']['shortName'] . '/' . $data->output['image']['shortName'];
			if(isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if ($form->validateFromPost()) {
					$form->sendArray[':user'] = $data->user['id'];
					$form->sendArray[':image'] = $data->output['image']['id'];
					$statement = $db->prepare('addComment','gallery');
					$statement->execute($data->output['commentForm']->sendArray);
				}
			}
			break;
		case 'AlbumDelete':
			if($data->action[5] == 'confirm'){
				$statement = $db->prepare('deleteAlbum','gallery');
				$statement->execute(array(':id' => $data->output['album']['id']));
				//common_redirect_local($data, 'urgallery/');
			}
			break;
		case 'ImageDelete':
			if($data->action[6] == 'confirm'){
				$statement = $db->prepare('deleteImage','gallery');
				$statement->execute(array(':id' => $data->output['image']['id']));	
				
				$data->output['responseMessage'] = 'Your image has been deleted. Click ' . common_generateLink($data,$data->output['galleryHome'].'album/view/'.$data->output['album']['shortName'],'here','album_view','/urgallery/'.$data->output['user']['name'].'/album/view/'.$data->output['album']['shortName'],NULL,true) . ' to return to your album.'; 
			}
			break;
		case 'ImageMakeProfile':
			$statement = $db->prepare('getProfilePictureAlbum', 'gallery');
			$statement->execute(array(':userId' => $data->user['id']));
			if(false === ($album = $statement->fetch())){
				$statement = $db->prepare('addAlbum', 'gallery');
				$statement->execute(array(
					':userId' => $data->user['id'],
					':name' => 'Profile Pictures',
					':allowComments' => 0,
					':shortName' => 'profile-pictures'
				));
				$album = array('id' => $db->lastInsertId);
			}
			$statement = $db->prepare('addImage', 'gallery');
			$result = $statement->execute(array(
				':name' => $data->output['image']['name'],
				':shortName' => $data->output['image']['shortName'],
				':albumId' => $album['id'],
				':image' => $data->output['image']['image'],
			));
			
			if($result){
				$data->output['responseMessage'] = 'The image has been added to your profile pictures. Click ' . common_generateLink($data,$data->output['galleryHome'].'album/view/profile-pictures','here','album_view','/urgallery/'.$data->output['user']['name'].'/album/view/profile-pictures',NULL,true) . ' to view your profile pictures.'; 	
			}else{
				$data->output['responseMessage'] = $data->output['responseMessage'] = 'There was an error in processing your request. Click ' . common_generateLink($data,$data->output['galleryHome'].'album/view/'.$data->output['album']['shortName'],'here','album_view','/urgallery/'.$data->output['user']['name'].'/album/view/'.$data->output['album']['shortName'],NULL,true) . ' to return to your album.'; 
			}
			
		break;
	}
}
function gallery_content($data) {
	$functionName = 'theme_gallery' . $data->output['pageType'];
	if(function_exists($functionName)){
		$functionName($data);
	}else{
		theme_contentBoxHeader("Not yet implemented");
		echo "<p>Please implement the function ", $functionName, ' ASAP</p>';
		theme_contentBoxFooter();
	}
}
?>