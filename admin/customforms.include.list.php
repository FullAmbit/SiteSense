<?php
function admin_customformsBuild($data,$db){
	$statement = $db->query('getAllForms', 'customform');
	$data->output['forms'] = $statement->fetchAll();
}
function admin_customformsShow($data){
	echo '
		<table>
			<tr>
				<th>Table Name</th>
				<th>URL</th>
				<th>Require Login?</th>
				<th>Controls</th>
			</tr>
			';

	foreach($data->output['forms'] as $form){
		echo '
			<tr>
				<td>', $form['name'], '</td>
				<td>', $form['shortName'], '</td>
				<td>', ($form['requireLogin'] == 1 ? 'Yes' : 'No'), '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/customforms/editform/', $form['id'], '">Edit Form Settings</a>
					<a href="', $data->linkRoot, 'admin/customforms/listfields/', $form['id'], '">Manage Fields</a>
					<a href="', $data->linkRoot, 'admin/customforms/viewdata/', $form['id'], '">View Data</a>
				</td>
			</tr>
		';
	}
			
	echo '
			<tr>
				<td><a href="', $data->linkRoot, 'admin/customforms/editform">New Form</a></td>
			</tr>
		</table>
	';
}