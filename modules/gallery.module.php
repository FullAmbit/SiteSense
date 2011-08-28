<?php
common_include('libraries/forms.php');
function page_getUniqueSettings($data) {
	$data->output['pageShortName']='gallery';
}
 
function page_buildContent($data,$db) {	
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
	
	// The album/image belongs to a user, so find who it is!
	if($data->action[1] === false){
		//The user's own album
		if(isset($data->user['id'])){
			$data->output['user'] = $data->user;
		}else{
			$data->output['pageType'] = 'AccessDenied';
			return;			
		}
	}else{
		$userName = $data->action[1];
		$statement = $db->prepare('getUserByName', 'users');
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
		if($loadUserAlbums !== false){
			$statement = $db->prepare('getAlbumsByUser', 'gallery');
			$statement->execute(array('user' => $data->output['user']['id']));
			$data->output['albums'] = $statement->fetchAll();
		}
		if(!is_null($loadAlbum)){
			$statement = $db->prepare('getAlbumByUserAndShortName', 'gallery');
			$statement->execute(array('user' => $data->output['user']['id'], 'shortName' => $loadAlbum));
			$album = $statement->fetch();
			if($album === false){
				$pageType = 'AlbumNotFound';
				$data->output['album'] = array(
					'shortName' => $loadAlbum
				);
			}else{
				if($permission == LOGGEDIN && $album['user'] == $data->user['id']){
					$permission = OWNER;
				}
				
				if($minimumPermissions > $permission){
					$pageType = 'AccessDenied';
				}else{
					if($loadAlbumImages === true){
						$statement = $db->prepare('getImagesFromAlbum', 'gallery');
						$statement->execute(array('album' => $album['id']));
						$data->output['images'] = $statement->fetchAll();
					}
					if(!is_null($loadImage)){
						$statement = $db->prepare('getImageByAlbumAndName', 'gallery');
						$statement->execute(array('album' => $album['id'], 'shortName' => $loadImage));
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
			$form = $data->output['form'] = new formHandler('galleryAlbum', $data);
			$form->action = $data->output['galleryHome'] . 'album/add';
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					$form->sendArray[':user'] = $data->user['id'];
					//does this album already exist?
					$statement = $db->prepare('getAlbumByUserAndShortName', 'gallery');
					$statement->execute(array(':user' => $form->sendArray[':user'], ':shortName' => $form->sendArray[':shortName']));
					//if so, append a number to the end, e.g. album/view/something-2
					if($statement->fetch() !== false){
						$i = 1;
						do{
							$i++;
							$statement->execute(array(':user' => $form->sendArray[':user'], ':shortName' => $form->sendArray[':shortName'] . '-' . $i));
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
			$form = $data->output['form'] = new formHandler('galleryAlbum', $data);
			$form->action = $data->output['galleryHome'] . 'album/edit/' . $data->output['album']['shortName'];
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					//does this album already exist?
					$statement = $db->prepare('getAlbumByUserAndShortName', 'gallery');
					$statement->execute(array(':user' => $form->sendArray[':user'], ':shortName' => $form->sendArray[':shortName']));
					//if so, append a number to the end, e.g. album/view/something-2
					if(($album = $statement->fetch()) !== false && $album['id'] != $data->output['album']['id']){
						$i = 1;
						do{
							$i++;
							$statement->execute(array(':user' => $form->sendArray[':user'], ':shortName' => $form->sendArray[':shortName'] . '-' . $i));
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
			$form = $data->output['form'] = new formHandler('galleryImageAdd', $data);
			$form->action = $data->output['galleryHome'] . 'image/add/' . $data->output['album']['shortName'];
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					$form->sendArray[':album'] = $data->output['album']['id'];
					$form->sendArray[':image'] = $form->fields['image']['images']['full']['saveName'];
					$form->sendArray[':thumb'] = $form->fields['image']['images']['thumb']['saveName'];
					$form->sendArray[':icon'] = $form->fields['image']['images']['icon']['saveName'];
					$statement = $db->prepare('addImage', 'gallery');
					$statement->execute($form->sendArray);
					common_redirect($data->output['galleryHome'] . 'image/view/' . $data->output['album']['shortName'] . '/' . $form->sendArray[':shortName']);
				}else{
						
				}
			}
			break;
		case 'ImageEdit':
			$form = $data->output['form'] = new formHandler('galleryImageEdit', $data);
			$form->action = $data->output['galleryHome'] . 'image/edit/' . $data->output['album']['shortName'] . '/' . $data->output['image']['shortName'];
			if (isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if($form->validateFromPost()){
					$statement = $db->prepare('editImage', 'gallery');
					$form->sendArray[':id'] = $data->output['image']['id'];
					$statement->execute($form->sendArray);
					common_redirect($data->output['galleryHome'] . 'image/view/' . $data->output['album']['shortName'] . '/' . $form->sendArray[':shortName']);
				}
			}
			break;
		case 'ImageView':
			$form = $data->output['commentForm'] = new formHandler('galleryImageComment', $data);
			$form->action = $data->output['galleryHome'] . 'image/view/' . $data->output['album']['shortName'] . '/' . $data->output['image']['shortName'];
			if(isset($_POST['fromForm']) && ($_POST['fromForm']==$form->fromForm)){
				$form->populateFromPostData();
				if ($form->validateFromPost()) {
					$form->sendArray[':user'] = $data->user['id'];
					$form->sendArray[':image'] = $data->output['image']['id'];
					$statement = $db->prepare('addComment','gallery');
					$statement->execute($data->output['commentForm']->sendArray);
					common_redirect($data->output['galleryHome'] . 'image/view/' . $data->output['album']['shortName'] . '/' . $data->output['image']['shortName']);
				}
			}
			break;
		case 'AlbumDelete':
			if($data->action[5] == 'confirm'){
				$statement = $db->prepare('deleteAlbum','gallery');
				$statement->execute(array(':id' => $data->output['album']['id']));
				common_redirect_local($data, 'gallery');
			}
			break;
		case 'ImageDelete':
			if($data->action[6] == 'confirm'){
				$statement = $db->prepare('deleteImage','gallery');
				$statement->execute(array(':id' => $data->output['image']['id']));	
				common_redirect($data->output['galleryHome'] . 'album/view/' . $data->output['album']['shortName']);
			}
			break;
		case 'ImageMakeProfile':
			$statement = $db->prepare('getProfilePictureAlbum', 'gallery');
			$statement->execute(array('user' => $data->user['id']));
			if(false === ($album = $statement->fetch())){
				$statement = $db->prepare('addAlbum', 'gallery');
				$statement->execute(array(
					':user' => $data->user['id'],
					':name' => 'Profile Pictures',
					':allowComments' => 0,
					':shortName' => 'profile-pictures'
				));
				$album = array('id' => $db->lastInsertId);
			}
			$statement = $db->prepare('addImage', 'gallery');
			$statement->execute(array(
				':name' => $data->output['image']['name'],
				':shortName' => $data->output['image']['shortName'],
				':album' => $album['id'],
				':image' => $data->output['image']['image'],
				':thumb' => $data->output['image']['thumb'],
				':icon' => $data->output['image']['icon']
			));
			common_redirect($data->output['galleryHome'] . 'album/view/profile-pictures');
		break;
		
	}
}

function page_content($data) {
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