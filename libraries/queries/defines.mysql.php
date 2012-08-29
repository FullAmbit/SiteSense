<?php
	define('SQR_ID',           'INT UNSIGNED NOT NULL');
	define('SQR_IDKey',        SQR_ID.' AUTO_INCREMENT PRIMARY KEY');
	define('SQR_username',     'VARCHAR(63) NOT NULL');
	define('SQR_password',     'VARCHAR(127) NOT NULL');
	define('SQR_firstName',    'VARCHAR(31) NOT NULL');
	define('SQR_lastName',     'VARCHAR(31) NOT NULL');
	define('SQR_IP',           'VARCHAR(39) NOT NULL'); // Big enough for IPv6
	define('SQR_name',         'VARCHAR(127) NOT NULL');
	define('SQR_shortName',    'VARCHAR(127) NOT NULL');
	define('SQR_title',        'TINYTEXT NOT NULL');
	define('SQR_URL',          'VARCHAR(255) NOT NULL');
	define('SQR_moduleName',   'VARCHAR(63) NOT NULL');
	define('SQR_email',        'TINYTEXT NOT NULL');
	define('SQR_boolean',      'TINYINT(1) NOT NULL');
	define('SQR_side',         'VARCHAR(5) NOT NULL');
	define('SQR_sortOrder',    'INT UNSIGNED NOT NULL');
	define('SQR_time',         'DATETIME NOT NULL');
	// Only one default TIMESTAMP column per table supported by MySQL
	define('SQR_lastModified', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
	// Only use SQR_added if the table doesn't require a SQR_lastModified
	define('SQR_added',        'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
?>