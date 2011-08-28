<?php

common_include('libraries/forms.php');

function admin_customformsBuild($data,$db) {
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFormById', 'customform');
	$statement->execute(array(':id' => $data->action[3]));
	$dbform = $statement->fetch();
	if($dbform === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Form Doesn\'t Exist</h2>';
		return;
	}
	$data->output['form'] = $dbform;
	$form = $data->output['customformForm'] = new formHandler('customformfields',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		$form->caption = 'New Custom Form';
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			$form->sendArray[':form'] = $dbform['id'];
			$statement = $db->prepare('newField', 'customform');
			$statement->execute($form->sendArray) or die($statement->errorInfo());
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>customform Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/customforms/newfield/' . $data->output['form']['id'] . '">
							Add New Field
						</a>
						<a href="'.$data->linkRoot.'admin/customforms/listfields/' . $data->output['form']['id'] . '">
							Return to Field List
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
