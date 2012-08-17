<?php
function hostnames_settings(){
	return array(
		'name' => 'hostnames',
		'shortName' => 'hostnames'
	);
}
function hostnames_install($db,$drop = FALSE){
	$structures = array(
		'hostnames' => array(
			'hostname'        => 'VARCHAR(63) NOT NULL PRIMARY KEY',
			'defaultTheme'    => SQR_moduleName,
			'defaultLanguage' => 'VARCHAR(5) NOT NULL',
			'homepage'        => SQR_moduleName
		)
	);
	$db->createTable('hostnames',$structures['hostnames'],false);
	return NULL;
}
function hostnames_uninstall($db){
	$db->dropTable('hostnames');
}
?>