<?php

common_include('libraries/forms.php');

function hostnames_admin_delete_build($data,$db){
	// Check If HostName Exists
	$statement = $db->prepare('getHostname','admin_hostnames');
	$statement->execute(array(
		':hostname' => $data->action[3]
	));
	if(($data->output['hostnameItem'] = $statement->fetch(PDO::FETCH_ASSOC)) == FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	// We Getting Any Post?
	if(!empty($_POST)){
		if(isset($_POST['yes']) && $_POST['yes'] === 'Yes'){
			// Okay...Delete This
			$statement = $db->prepare('deleteHostname','admin_hostnames');
			$r = $statement->execute(array(
				':hostname' => $data->output['hostnameItem']['hostname']
			));
			if($r){
				// Delete URL Remaps
				$statement = $db->prepare('deleteByHostname','admin_urls');
				$r = $statement->execute(array(
					':hostname' => $data->output['hostnameItem']['hostname']
				));
				if($r){
					$data->output['themeOverride'] = 'DeleteSuccess';
				}else{
					$data->output['responseMessage'] = 'The hostname was removed, however there was an error in removing the associated URL remaps.';
				}
			}else{
				$data->output['responseMessage'] = 'There was an error in removing the hostname from the database.';
			}
		}else{
			// Nope. Redirect.
			common_redirect($data->linkRoot.'admin/hostnames/list');
		}
	}
}

function hostnames_admin_delete_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_hostnames'.$data->output['themeOverride'];
		$func($data);
		return;
	}
	theme_hostnamesDelete($data);
}
?>