<?php
function admin_customformsBuild($data,$db){
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFormById', 'customform');
	$statement->execute(array(':id' => $data->action[3]));
	$form = $statement->fetch();
	if($form === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Form Doesn\'t Exist</h2>';
		return;
	}
	$data->output['form'] = $form;
	$statement = $db->prepare('getFieldsByForm', 'customform');
	$statement->execute(array(':form' => $form['id']));
	$data->output['fields'] = $statement->fetchAll();
}
function admin_customformsShow($data){
	echo '
		<table>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Controls</th>
			</tr>
			';

	foreach($data->output['fields'] as $field){
		echo '
			<tr>
				<td>', $field['name'], '</td>
				<td>', $field['type'], '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/customforms/editfield/', $field['id'], '">Edit</a>
					<a href="', $data->linkRoot, 'admin/customforms/listfields/', $field['id'], '">Delete</a>
				</td>
			</tr>
		';
	}
	echo '
			<tr>
				<td><a href="', $data->linkRoot, 'admin/customforms/newfield/', $data->output['form']['id'], '">New Field</a></td>
			</tr>
		</table>
	';
}