<?php
function admin_modulesBuild($data,$db){
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getModuleById', 'modules');
	$statement->execute(array(':id' => $data->action[3]));
	$module = $statement->fetch();
	if($module === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Module With ID ' . $data->action[3] . ' Doesn\'t Exist</h2>';
		return;
	}
	$data->output['module'] = $module;
	// Does a change need to be made?
	switch($data->action[4]){
		case 'enable':
			$sidebar = (int)$data->action[5];
			$statement = $db->prepare('enableSideBar', 'modules');
			$statement->execute(array(':module' => $module['id'], ':sidebar' => $sidebar));
			break;
		case 'disable':
			$sidebar = (int)$data->action[5];
			$statement = $db->prepare('disableSideBar', 'modules');
			$statement->execute(array(':module' => $module['id'], ':sidebar' => $sidebar));
			break;
	}
	//
	$statement = $db->prepare('getSideBarsByModule', 'modules');
	$statement->execute(array(':module' => $module['id']));
	$data->output['sidebars'] = $statement->fetchAll();
}
function admin_modulesShow($data){
	echo '
		<table>
			<tr>
				<th>Name</th>
				<th>Enabled</th>
			</tr>
			';

	foreach($data->output['sidebars'] as $sidebar){
		$action = ($sidebar['enabled'] == 1) ? 'disable' : 'enable';
		echo '
			<tr>
				<td>', $sidebar['name'], '</td>
				<td>', ($sidebar['enabled'] ? 'Yes' : 'No'), '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/modules/sidebars/', $data->output['module']['id'], '/', $action, '/', $sidebar['id'], '">
						', ucfirst($action), '
					</a>
				</td>
				
			</tr>
		';
	}
	echo '
		</table>
	';
}