<?php

function hostnames_settings(){
	return array(
		'name' => 'Hostnames',
		'shortName' => 'hostnames'
	);
}

function hostnames_install($db,$drop = FALSE){
	$structures = array(
		'hostnames' => array(
			'hostname' => 'VARCHAR(64) NOT NULL DEFAULT ``',
			'defaultTheme' => 'VARCHAR(64) NOT NULL DEFAULT ``',
			'defaultLanguage' => 'VARCHAR(64) NOT NULL DEFAULT ``',
			'homepage' => 'VARCHAR(64) NOT NULL DEFAULT ``'
		)
	);
	$db->createTable('hostnames',$structures['hostnames'],false);
}

function hostnames_uninstall($db){
	$db->dropTable('hostnames');
}

?>