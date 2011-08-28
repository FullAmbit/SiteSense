<?php

function admin_urlremapsBuild($data,$db) {
	$staff=false;
	if (empty($data->action[3])) {
		$data->output['abort'] = true;
		$data->output['abortremap'] = '<h2>No ID Given</h2>';
	}else{
		$remap = $db->prepare('getUrlRemapById', 'admin_urlremap');
		$remap->execute(array(':id' => (int)$data->action[3]));
		$remap = $remap->fetch();
		$data->output['exists'] = $remap !== false;
		if($data->action[4] == 'confirm'){
			$remaps = $db->prepare('deleteUrlRemap','admin_urlremap');
			$remaps->execute(array(':id' => (int)$data->action[3]));
			$data->output['success'] = ($remaps->rowCount() == 1);
		}
	}
}

function admin_urlremapsShow($data) {
	if(isset($data->output['success'])){
		if($data->output['success']){
			echo '
				<h2>Removal Successful</h2>
				<p>The remap has been successfully deleted</p>
				<p><a href="', $data->linkRoot, 'admin/urlremap/list">Return to remap list</a></p>
			';
		}else{
			echo '
				<h2>Cannot remove remap</h2>
				<p>The remap cannot be removed. It ', ($data->output['exists'] ? 'does' : 'doesn\'t'), ' exist in the database.</p>
				<p><a href="', $data->linkRoot, 'admin/urlremap/list">Return to remap list</a></p>
			';
		}
	}else{
		echo '
			<h2>Confirm Removal</h2>
			<p>Are you sure that you want to remove this remap?</p>
			<ul>
				<li><a href="', $data->linkRoot, 'admin/urlremap/delete/', $data->action[3], '/confirm">Yes</a></li>
				<li><a href="', $data->linkRoot, 'admin/urlremap/list">No</a></li>
			</ul>
		';
	}
}

?>	