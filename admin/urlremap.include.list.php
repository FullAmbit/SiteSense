<?php

function admin_urlremapsBuild($data,$db) {
	$data->output['messageListLimit']=ADMIN_SHOWPERPAGE;
	$messages = $db->query('getAllUrlRemaps','admin_urlremap');
	$data->output['remapList'] = $messages->fetchAll();
}

function admin_urlremapsShow($data) {
	echo '
		<table class="remapList">
			<thead>
				<tr>
					<th class="match">Pattern</th>
					<th class="replacement">Replacement</th>
					<th class="redirect">Redirect?</th>
					<th class="controls">Controls</th>
				</tr>
			</thead>
			<tbody>
	';
	$key = 0;
	foreach($data->output['remapList'] as $key => $remap) {
		echo '
				<tr class="', ($key%2==0 ? 'even' : 'odd'),'">
					<td class="match">', $remap['match'], '</td>
					<td class="replacement">', $remap['replace'], '</td>
					<td class="redirect">', ($remap['redirect'] == 1 ? 'Yes' : 'No'), '</td>
					<td class="buttonList">
						<a href="'.$data->linkRoot.'admin/urlremap/edit/'.$remap['id'].'">Modify</a>
						<a href="'.$data->linkRoot.'admin/urlremap/delete/'.$remap['id'].'">Delete</a>
					</td>
				</tr>';
	}
	$key++;
	echo '
				<tr class="', ($key%2==0 ? 'even' : 'odd'), '">
					<td></td>
					<td></td>
					<td></td>
					<td class="buttonList">
						<a href="'.$data->linkRoot.'admin/urlremap/edit/">New URL Remap</a>
					</td>
				</tr>
			</tbody>
		</table>
		';
}

?>	