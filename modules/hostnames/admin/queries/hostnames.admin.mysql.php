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
		',
		'updateHostname' => '
			UPDATE 
				!prefix!hostnames 
			SET 
				hostname = :hostname,
				defaultTheme = :defaultTheme,
				homepage = :homepage
			WHERE
				hostname = :originalHostName
			LIMIT 
				1
		',
		'deleteHostname' => '
			DELETE FROM 
				!prefix!hostnames 
			WHERE 
				hostname = :hostname 
			LIMIT 
				1
		'
	);
}