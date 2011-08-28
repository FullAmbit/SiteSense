<?php

common_include('libraries/forms.php');

function admin_modulesBuild($data,$db) {
	if($data->action[3] === false){
		$existing = false;
	}else{
		$existing = (int)$data->action[3];
		$check = $db->prepare('getModuleById', 'modules');
		$check->execute(array(':id' => $existing));
		if(($data->output['module'] = $check->fetch()) === false){
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>ID does not exist in database</h2>';
			return;
		}
	}
	$form = $data->output['moduleForm'] = new formHandler('module',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		if($existing){
			$form->caption = 'Editing Module';
		}else{
			$form->caption = 'New Module';
		}
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			if($existing){
				$statement = $db->prepare('editModule', 'modules');
				$form->sendArray[':id'] = $existing;
			}else{
				$statement = $db->prepare('newModule', 'modules');
			}
			$statement->execute($form->sendArray) or die('Saving module failed');
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>module Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/modules/edit/">
							Add New Module
						</a>
						<a href="'.$data->linkRoot.'admin/modules/list/">
							Return to Module List
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

function admin_modulesShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['moduleForm']);
	}
}

?>