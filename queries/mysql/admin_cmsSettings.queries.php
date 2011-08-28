<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_cmsSettings_addQueries() {
	return array(
		'updateSettings' => "
			UPDATE !prefix!settings
			SET value= :value
			WHERE name= :name
			AND category= 'cms'
		"
	);
}

?>