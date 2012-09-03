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
			INSERT INTO !prefix!settings_!lang!
			(name,category,value)
			VALUES (:name, :category, :value)
		',
		'addUser' => '
			INSERT INTO !prefix!users
			(name,password,registeredIP)
			VALUES
			(:name,:passphrase,:registeredIP)
		',
		'makeNewsBlog' => '
			INSERT INTO !prefix!blogs_!lang!
			(name,shortName,title,owner,numberPerPage,description)
			VALUES
			(\'news\', \'news\', \'News\', 0, 3, \'Home Page News\')
		',
		'makeWelcomePost' => '
			INSERT INTO !prefix!blog_posts_!lang!
			(blogId,title,name,shortName,user,postTime,rawSummary,parsedSummary,rawContent,parsedContent,live)
			VALUES (
				1,
				\'Welcome to SiteSense\',
				\'Welcome\',
				\'welcome\',
				1,
				CURRENT_TIMESTAMP,
				\'<p>SiteSense installation successful</p>\',
				\'&lt;p&gt;SiteSense installation successful&lt;/p&gt;\',
				\'<p>You have sucessfully completed your SiteSense Installation</p>\',
				\'&lt;p&gt;You have sucessfully completed your SiteSense CMS Installation&lt;/p&gt;\',
				1
			)
		',
		'makeRegistrationAgreement' => '
			INSERT INTO !prefix!pages_!lang!
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
			INSERT INTO !prefix!pages_!lang!
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
		'newModule' => '
			INSERT INTO !prefix!modules (name,shortName,enabled,version) VALUES (:name,:shortName,:enabled,:version)
		',
	);
}
function installer_tableStructures() {
	return array(
		'settings' => array(
			'id'        => SQR_IDKey,
			'name'      => SQR_shortName,
			'category'  => 'VARCHAR(31)',
			'value'     => 'MEDIUMTEXT'
		)
	);
}
?>