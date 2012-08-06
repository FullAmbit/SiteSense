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
			'hostname' => 'VARCHAR(64) NOT NULL DEFAULT ""',
			'defaultTheme' => 'VARCHAR(64) NOT NULL DEFAULT ""',
			'defaultLanguage' => 'VARCHAR(64) NOT NULL DEFAULT ""',
			'homepage' => 'VARCHAR(64) NOT NULL DEFAULT ""',
			'PRIMARY KEY (`hostname`)'
		)
	);
	$db->createTable('hostnames',$structures['hostnames'],false);
	return NULL;
}

function hostnames_uninstall($db){
	$db->dropTable('hostnames');
}

?>