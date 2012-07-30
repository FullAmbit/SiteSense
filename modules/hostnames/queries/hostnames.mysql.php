<?php

function hostnames_addQueries(){
	return array(
		'getHostname' => '
			SELECT
				defaultTheme,defaultLanguage,homepage
			FROM 
				!prefix!hostnames 
			WHERE 
				hostname = :hostname 
			LIMIT 
				1
		'
	);
}
?>