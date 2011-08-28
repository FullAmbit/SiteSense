<?php

common_include('libraries/forms.php');

function admin_urlremapsBuild($data,$db) {
	if($data->action[3] === false){
		$existing = false;
	}else{
		$existing = (int)$data->action[3];
		$check = $db->prepare('getUrlRemapById', 'admin_urlremap');
		$check->execute(array(':id' => $existing));
		if(($data->output['urlremap'] = $check->fetch()) === false){
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>ID does not exist in database</h2>';
			return;
		}
	}
	$form = $data->output['remapForm'] = new formHandler('urlremap',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		if($existing){
			$form->caption = 'Editing URL Remap';
		}else{
			$form->caption = 'New URL Remap';
		}
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			if($existing){
				$statement = $db->prepare('editUrlRemap', 'admin_urlremap');
				$form->sendArray[':id'] = $existing;
			}else{
				$statement = $db->prepare('insertUrlRemap', 'admin_urlremap');
			}
			$statement->execute($form->sendArray) or die('Saving remap failed');
			
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>Remap Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/urlremap/edit/new">
							Add New URL Remap
						</a>
						<a href="'.$data->linkRoot.'admin/urlremap/list/">
							Return to URL Remap List
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}

function admin_urlremapsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['remapForm']);
	}
}

?>
