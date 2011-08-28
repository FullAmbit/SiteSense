<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function installer_addQueries() {
	return array(
		'dropTable' => '
			DROP TABLE !prefix!!table!
		',
		'addSetting' => '
			INSERT INTO !prefix!settings
			(name,category,value) VALUES (:name, :category, :value)
		',
		'addUser' => '
			INSERT INTO !prefix!users
			(name,password,userLevel,registeredDate,registeredIP)
			VALUES
			(:name,:passphrase,:userLevel,:registeredDate,:registeredIP)
		',
		'makeNewsBlog' => '
			INSERT INTO !prefix!blogs
			(name,shortName,owner,minPermission,numberPerPage,description)
			VALUES
			(\'news\', \'news\', 0, '.USERLEVEL_WRITER.', 3, \'Home Page News\')
		',
		'makeWelcomePost' => '
			INSERT INTO !prefix!blogPosts
			(blogId,title,shortName,user,postTime,modifiedTime,summary,content,live)
			VALUES (
				1,
				\'Welcome to Paladin CMS\',
				\'welcome\',
				0,
				:time,
				0,
				\'<p>SiteSense CMS installation successful</p>\',
				\'<p>You have sucessfully completed your SiteSense CMS Installation</p>\',
				1
			)
		',
		'makeRegistrationAgreement' => '
			INSERT INTO !prefix!pages
			(shortName,title,parent,content,live)
			VALUES (
				\'Registration_Agreement\',
				\'Registration Agreement\',
				0,
				\'<p>Your registration agreement text should go in this file. I suggest leaving it "live" so people can access it directly, though it will continue to work on the registration page without that.</p>\',
				1
			)
		',
		'makeRegistrationEMail' => '
			INSERT INTO !prefix!pages
			(shortName,title,parent,content,live)
			VALUES (
				\'Registration_EMail\',
				\'Registration E-Mail\',
				0,
				\'
<h1>
	Thank you for registering with $siteName.
</h1>
<p>
	To activate your account, please use the following link. If you are unable to launch it from your e-mail reader, please cut and paste to the address bar of your browser.
</p><p>
	$registerLink
</p>\',
				1
			)
		',
		'makeModules' => "
			INSERT INTO !prefix!modules
			(name, shortName, enabled)
			VALUES
			('default', 'default', 1)
		"
	);
}

function installer_structures() {
	return array(
		'settings' => array(
			'id'       => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name'     => 'VARCHAR(64)',
			'category' => 'VARCHAR(64)',
			'value'    => 'MEDIUMTEXT'
		),
		'users' => array(
			'id'             => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name'           => 'VARCHAR(64)',
			'password'       => 'VARCHAR(64)',
			'fullName'       => 'VARCHAR(128)',
			'userLevel'      => 'INT',
			'registeredDate' => 'INT',
			'registeredIP'   => 'VARCHAR(64)',
			'lastAccess'     => 'INT',
			'contactEMail'   => 'VARCHAR(255)',
			'publicEMail'    => 'VARCHAR(255)'
		),
		'userpms' => array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'from' => 'int(11) unsigned NOT NULL',
			'to' => 'int(11) unsigned NOT NULL',
			'message' => 'text NOT NULL',
			'read' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'deleted' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'sent' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP'
		),
		'pages' => array(
			'id'           => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'shortName'    => 'VARCHAR(128)',
			'title'        => 'VARCHAR(256)',
			'content'      => 'MEDIUMTEXT',
			'parent'       => 'INT',
			'showOnParent' => 'BOOLEAN',
			'sortOrder'    => 'INT',
			'showOnMenu'   => 'BOOLEAN',
			'menuTitle'    => 'VARCHAR(64)',
			'live'         => 'BOOLEAN',
			'INDEX(shortName,parent,sortOrder,showOnMenu,showOnParent)'
		),
		'sessions' => array(
			'sessionId' => 'VARCHAR(256) PRIMARY KEY',
			'userId'    => 'INT',
			'expires'   => 'INT',
			'ipAddress' => 'VARCHAR(16)',
			'userAgent' => 'VARCHAR(256)',
			'INDEX(userId,expires)'
		),
		'sidebars' => array(
			'id'        => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name'      => 'VARCHAR(256)',
			'enabled'   => 'BOOLEAN',
			'fromFile'  => 'BOOLEAN',
			'title'     => 'VARCHAR(256)',
			'titleURL'  => 'VARCHAR(256)',
			'content'   => 'MEDIUMTEXT',
			'side'      => 'VARCHAR(8)',
			'sortOrder' => 'INT',
			'INDEX(sortOrder,side)'
		),
		'mainMenu' => array(
			'id'        => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'text'      => 'VARCHAR(256)',
			'title'     => 'VARCHAR(256)',
			'url'       => 'VARCHAR(256)',
			'module'    => 'VARCHAR(64)',
			'side'      => 'VARCHAR(8)',
			'sortOrder' => 'INT',
			'INDEX(sortOrder,side)'
		),
		'blogs' => array(
			'id'            => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name'          => 'VARCHAR(256)',
			'shortName'     => 'VARCHAR(256)',
			'owner'         => 'INT',
			'minPermission' => 'INT',
			'numberPerPage' => 'INT',
			'description'   => 'MEDIUMTEXT',
			'commentsRequireLogin' => 'BOOLEAN DEFAULT 0',
			'INDEX(name,owner,minPermission)'
		),
		'blogPosts' => array(
			'id'           => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'blogId'       => 'INT',
			'title'        => 'VARCHAR(256)',
			'shortName'    => 'VARCHAR(256)',
			'user'         => 'INT',
			'postTime'     => 'INT',
			'modifiedTime' => 'INT',
			'live'         => 'BOOLEAN',
			'summary'      => 'MEDIUMTEXT',
			'content'      => 'MEDIUMTEXT',
			'allowReplies' => 'BOOLEAN DEFAULT 1',
			'repliesWaiting' => 'INT DEFAULT 0',
			'INDEX(blogId)'
		),
		'blogcomments' => array(
			'id' => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'post' => 'INT NOT NULL',
			'time' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'commenter'	=> 'VARCHAR(64) NOT NULL',
			'content' => 'MEDIUMTEXT'
		),
		'activations' => array(
			'userId'         => 'INT PRIMARY KEY',
			'hash'           => 'VARCHAR(256)',
			'expires'        => 'INT'
		),
		'galleryalbums' => array(
			'id'            =>  'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name'          =>  'varchar(64) NOT NULL',
			'shortName'     =>  'varchar(32) NOT NULL',
			'user'          =>  'int(11) NOT NULL',
			'allowComments' =>  'tinyint(1) NOT NULL'
		),
		'gallerycomments' => array(
			'id'            =>   'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'image'         =>   'int(11) NOT NULL',
			'user'          =>   'int(11) NOT NULL',
			'content'       =>   'text NOT NULL',
			'time'          =>   'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		),
		'galleryimages' => array(
			'id'           =>    'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'album'        =>    'int(11) NOT NULL',
			'name'         =>    'varchar(128) NOT NULL',
			'shortName'    =>    'varchar(32) NOT NULL',
			'image'        =>    'varchar(64) NOT NULL',
			'thumb'        =>    'varchar(64) NOT NULL',
			'time'         =>    'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		),
		'urlremap' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
	  		'match' => 'varchar(128) NOT NULL',
	  		'replace' => 'varchar(128) NOT NULL',
	  		'redirect' => 'tinyint(1) NOT NULL'
		),
		'customformfields' => array(
	  		'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
	  		'form' => 'int(11) NOT NULL',
	  		'name' => 'varchar(32) NOT NULL',
	  		'type' => 'varchar(16) NOT NULL',
		),
		'customformrows' => array(
	  		'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
	  		'form' => 'int(11) NOT NULL',
		),
		'customforms' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'shortName' => 'varchar(32) NOT NULL',
			'name' => 'varchar(64) NOT NULL',
			'successMessage' => 'text NOT NULL',
			'requireLogin' => 'tinyint(1) NOT NULL',
		),
		'customformvalues' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
	  		'row' => 'int(11) NOT NULL',
	  		'field' => 'int(11) NOT NULL',
	  		'value' => 'text NOT NULL'
		),
		'modules' => array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
  			'name' => 'varchar(64) NOT NULL',
  			'shortName' => 'varchar(32) NOT NULL',
  			'enabled' => 'tinyint(1) NOT NULL',
		),
		'modulesidebars' => array(
	  		'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
	  		'module' => 'int(11) NOT NULL',
	  		'sidebar' => 'int(11) NOT NULL',
	  		'enabled' => 'tinyint(1) NOT NULL',
			'UNIQUE KEY `module` (`module`,`sidebar`)'
		)
	);
}

?>