<?php

function hostnames_admin_list_build($data,$db){
	$statement = $db->prepare('getAllHostnames','admin_hostnames');
	$statement->execute();
	$data->output['hostnameList'] = $statement->fetchAll(PDO::FETCH_ASSOC);
}

function hostnames_admin_list_content($data){
	theme_hostnamesList($data);
}
?>