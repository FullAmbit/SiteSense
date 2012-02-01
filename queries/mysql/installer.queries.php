<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author			Full Ambit Media, LLC <pr@fullambit.com>
* @copyright	Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license		http://opensource.org/licenses/osl-3.0.php	Open Software License (OSL 3.0)
*/
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
			(name,shortName,title,owner,minPermission,numberPerPage,description)
			VALUES
			(\'news\', \'news\', \'News\', 0, '.USERLEVEL_WRITER.', 3, \'Home Page News\')
		',
		'makeWelcomePost' => '
			INSERT INTO !prefix!blog_posts
			(blogId,title,name,shortName,user,modifiedTime,rawSummary,parsedSummary,rawContent,parsedContent,live)
			VALUES (
				1,
				\'Welcome to SiteSense\',
				\'Welcome\',
				\'welcome\',
				1,
				:time,
				\'<p>SiteSense installation successful</p>\',
				\'&lt;p&gt;SiteSense installation successful&lt;/p&gt;\',
				\'<p>You have sucessfully completed your SiteSense Installation</p>\',
				\'&lt;p&gt;You have sucessfully completed your SiteSense CMS Installation&lt;/p&gt;\',
				1
			)
		',
		'makeRegistrationAgreement' => '
			INSERT INTO !prefix!pages
			(shortName,name,title,parent,rawContent,parsedContent,live)
			VALUES (
				\'registration-agreement\',
				\'Registration Agreement\',
				\'Registration Agreement\',
				0,
				\'<p>Your registration agreement text should go in this file. I suggest leaving it "live" so people can access it directly, though it will continue to work on the registration page without that.</p>\',
				\'&lt;p&gt;Your registration agreement text should go in this file. I suggest leaving it &amp;quot;live&amp;quot; so people can access it directly, though it will continue to work on the registration page without that.&lt;/p&gt;\',
				1
			)
		',
		'makeRegistrationEMail' => '
			INSERT INTO !prefix!pages
			(shortName,name,title,parent,rawContent,parsedContent,live)
			VALUES (
				\'registration-email\',
				\'Registration Email\',
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
				\'
				&lt;h1&gt;
	Thank you for registering with $siteName.&lt;/h1&gt;
&lt;p&gt;
	To activate your account, please use the following link. If you are unable to launch it from your e-mail reader, please cut and paste to the address bar of your browser.&lt;/p&gt;
&lt;p&gt;
	$registerLink&lt;/p&gt;
				\',
				1
			)
		',
		'makeModules' => "
			INSERT INTO !prefix!modules
			(name, shortName, enabled)
			VALUES
			('default', 'default', 1)
		",
		"newModule" => "
			INSERT INTO !prefix!modules (name,shortName,enabled) VALUES (:name,:shortName,:enabled)
		",
		'addPlugin' => '
			INSERT INTO !prefix!plugins (name,isCDN,isEditor) VALUES (:pluginName,:isCDN,:isEditor)
		',
	);
}
function installer_tableStructures() {
	return array(
		'activations' => array(
			'userId'					 => SQR_IDKey,
			'hash'						 => 'VARCHAR(255)',
			'expires'					 => SQR_time
		),
		'banned' => array(
			'id'							 => SQR_IDKey,
			'userId'					 => SQR_ID,
			'userLevel'				 => SQR_userLevel,
			'email'						 => SQR_email,
			'ipAddress'				 => SQR_IP,
			'timestamp'				 => SQR_added,
			'expiration'			 => SQR_time,
			'UNIQUE KEY `userId` (`userId`)'
		),
		'main_menu' => array(
			'id'							 => SQR_IDKey,
			'text'						 => SQR_name,
			'title'						 => SQR_title,
			'url'							 => SQR_URL,
			'side'						 => SQR_side.' DEFAULT \'left\'',
			'sortOrder'				 => SQR_sortOrder.' DEFAULT \'1\'',
			'enabled'					 => SQR_boolean,
			'parent'					 => SQR_ID.' DEFAULT \'0\'',
			'KEY `sortOrder` (`sortOrder`,`side`)'
		),
		'modules' => array(
			'id'							 => SQR_IDKey,
			'name'						 => SQR_name,
			'shortName'				 => SQR_shortName,
			'enabled'					 => SQR_boolean
		),
		'module_sidebars' => array(
			'id'							 => SQR_IDKey,
			'module'					 => SQR_ID,
			'sidebar'					 => SQR_ID,
			'enabled'					 => SQR_boolean,
			'sortOrder'				 => SQR_sortOrder.' DEFAULT \'1\'',
			'UNIQUE KEY `module` (`module`,`sidebar`)'
		),
		'plugins' => array(
			'id'							 => SQR_IDKey,
			'name'						 => SQR_shortName,
			'enabled'					 => SQR_boolean.' DEFAULT \'1\'',
			'isCDN'						 => SQR_boolean.' DEFAULT \'0\'',
			'isEditor'				 => SQR_boolean.' DEFAULT \'0\''
		),
		'plugins_modules' => array(
			'plugin'							 => SQR_ID,
			'module'							 => SQR_ID
		),
		'sessions' => array(
			'sessionId'				 => 'VARCHAR(255) NOT NULL PRIMARY KEY',
			'userId'					 => SQR_ID,
			'expires'					 => SQR_time,
			'ipAddress'				 => SQR_IP,
			'userAgent'				 => 'VARCHAR(255)',
			'KEY `userId` (`userId`,`expires`)'
		),
		'settings' => array(
			'id'							 => SQR_IDKey,
			'name'						 => SQR_shortName,
			'category'				 => 'VARCHAR(31)',
			'value'						 => 'MEDIUMTEXT'
		),
		'sidebars' => array(
			'id'							 => SQR_IDKey,
			'name'						 => SQR_name,
			'shortName'				 => SQR_shortName,
			'enabled'					 => SQR_boolean,
			'fromFile'				 => SQR_boolean,
			'title'						 => 'VARCHAR(255)',
			'titleURL'				 => SQR_URL,
			'rawContent'			 => 'TEXT',
			'parsedContent'		 => 'TEXT',
			'side'						 => SQR_side.' DEFAULT \'left\'',
			'sortOrder'				 => SQR_sortOrder,
			'KEY `sortOrder` (`sortOrder`,`side`)'
		),
		'url_remap' => array(
			'id'							 => SQR_IDKey,
			'match'						 => 'VARCHAR(127) NOT NULL',
			'replace'					 => 'VARCHAR(127) NOT NULL'
		),
	);
}
?>