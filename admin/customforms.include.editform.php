<?php

common_include('libraries/forms.php');

function admin_customformsBuild($data,$db) {
	if($data->action[3] === false){
		$existing = false;
	}else{
		$existing = (int)$data->action[3];
		$check = $db->prepare('getFormById', 'customform');
		$check->execute(array(':id' => $existing));
		if(($data->output['customform'] = $check->fetch()) === false){
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>ID does not exist in database</h2>';
			return;
		}
	}
	$form = $data->output['customformForm'] = new formHandler('customforms',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		if($existing){
			$form->caption = 'Editing Custom Form';
		}else{
			$form->caption = 'New Custom Form';
		}
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			if($existing){
				$statement = $db->prepare('editForm', 'customform');
				$form->sendArray[':id'] = $existing;
			}else{
				$statement = $db->prepare('newForm', 'customform');
			}
			$statement->execute($form->sendArray) or die('Saving customform failed');
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>customform Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/customforms/edit/new">
							Add New Custom Form
						</a>
						<a href="'.$data->linkRoot.'admin/customforms/list/">
							Return to Custom Form List
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

function admin_customformsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['customformForm']);
	}
}

?>
