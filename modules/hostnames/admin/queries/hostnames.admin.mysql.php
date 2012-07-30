<?php

function admin_hostnames_addQueries(){
	return array(
		'getAllHostnames' => '
			SELECT
				*
			FROM 
				!prefix!hostnames 
			ORDER BY
				hostname ASC
		',
		'getHostname' => '
			SELECT
				*
			FROM
				!prefix!hostnames 
			WHERE
				hostname = :hostname
			LIMIT
				1
		',
		'addHostname' => '
			INSERT INTO
				!prefix!hostnames 
				(hostname,defaultTheme,homepage)
			VALUES
				(:hostname,:defaultTheme,:homepage)
		'
	);
}