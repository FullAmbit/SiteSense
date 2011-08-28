<?php
function admin_modulesBuild($data,$db){
	$statement = $db->query('getAllModules', 'modules');
	$data->output['modules'] = $statement->fetchAll();
}
function admin_modulesShow($data){
	echo '
		<table>
			<tr>
				<th>Name</th>
				<th>URL</th>
				<th>Enabled</th>
				<th>Controls</th>
			</tr>
			';

	foreach($data->output['modules'] as $module){
		echo '
			<tr>
				<td>', $module['name'], '</td>
				<td>', $module['shortName'], '</td>
				<td>', (($module['enabled'] == 1) ? 'yes' : 'no'), '</td> 
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/modules/edit/', $module['id'], '">Edit module Settings</a>
					<a href="', $data->linkRoot, 'admin/modules/sidebars/', $module['id'], '">Select Sidebars</a>
				</td>
			</tr>
		';
	}
			
	echo '
			<tr>
				<td><a href="', $data->linkRoot, 'admin/modules/edit">New Module</a></td>
				<td><a href="', $data->linkRoot, 'admin/modules/update">Update From Filesystem</a></td>
			</tr>
		</table>
	';
}