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
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
      (blogId,title,name,shortName,user,postTime,modifiedTime,rawSummary,parsedSummary,rawContent,parsedContent,live)
      VALUES (
        1,
        \'Welcome to SiteSense\',
        \'Welcome\',
        \'welcome\',
        1,
        :time,
        0,
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
function installer_structures() {
  return array(
  'activations' => array(
    'userId' => 'int(11) NOT NULL',
      'hash' => 'varchar(256) DEFAULT NULL',
      'expires' => 'int(11) DEFAULT NULL',
    'PRIMARY KEY (`userId`)'
  ),
  'banned' => array(
    'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'userId' => 'int(11) NOT NULL',
    'userLevel' => 'int(11) NOT NULL',
    'email' => 'varchar(256) DEFAULT NULL',
    'ipAddress' => 'varchar(64) DEFAULT NULL',
    'time' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    'expiration' => 'int(11) NOT NULL',
    'PRIMARY KEY (`id`)',
    'UNIQUE KEY `userId` (`userId`)'
  ),
    'main_menu' => array(
      'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'text' => 'varchar(256) DEFAULT NULL',
      'title' => 'varchar(256) DEFAULT NULL',
      'url' => 'varchar(256) DEFAULT NULL',
      'side' => 'varchar(8) DEFAULT \'left\'',
      'sortOrder' => 'int(11) DEFAULT \'1\'',
      'enabled' => 'tinyint(1) NOT NULL',
      'parent' => 'int(11) NOT NULL DEFAULT \'0\'',
      'PRIMARY KEY (`id`)',
      'KEY `sortOrder` (`sortOrder`,`side`)'
    ),
    'modules' => array(
      'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
      'name' => 'varchar(64) NOT NULL',
      'shortName' => 'varchar(32) NOT NULL',
      'enabled' => 'tinyint(1) NOT NULL',
      'PRIMARY KEY (`id`)'
    ),
    'module_sidebars' => array(
      'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'module' => 'int(11) NOT NULL',
      'sidebar' => 'int(11) NOT NULL',
      'enabled' => 'tinyint(1) NOT NULL',
      'sortOrder' => 'int(11) NOT NULL DEFAULT \'1\'',
      'PRIMARY KEY (`id`)',
      'UNIQUE KEY `module` (`module`,`sidebar`)'
    ),
    'plugins' => array(
      'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'name' => 'varchar(256) NOT NULL',
      'modules' => 'text',
      'enabled' => 'tinyint(1) NOT NULL DEFAULT \'1\'',
      'isCDN' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
      'isEditor' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
    'PRIMARY KEY (`id`)'
    ),
    'sessions' => array(
      'sessionId' => 'varchar(256) NOT NULL',
      'userId' => 'int(11) DEFAULT NULL',
      'expires' => 'int(11) DEFAULT NULL',
      'ipAddress' => 'varchar(16) DEFAULT NULL',
      'userAgent' => 'varchar(256) DEFAULT NULL',
      'PRIMARY KEY (`sessionId`)',
      'KEY `userId` (`userId`,`expires`)'
    ),
    'settings' => array(
      'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'name' => 'varchar(64) DEFAULT NULL',
      'category' => 'varchar(64) DEFAULT NULL',
      'value' => 'mediumtext',
      'PRIMARY KEY (`id`)'
    ),
    'sidebars' => array(
      'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'name' => 'varchar(256) DEFAULT NULL',
      'shortName' => 'varchar(256) DEFAULT NULL',
      'enabled' => 'tinyint(1) DEFAULT NULL',
      'fromFile' => 'tinyint(1) DEFAULT NULL',
      'title' => 'varchar(256) DEFAULT NULL',
      'titleURL' => 'varchar(256) DEFAULT NULL',
      'rawContent' => 'text',
        'parsedContent' => 'text NOT NULL',
      'side' => 'varchar(8) DEFAULT \'left\'',
      'sortOrder' => 'int(11) DEFAULT NULL',
      'PRIMARY KEY (`id`)',
      'KEY `sortOrder` (`sortOrder`,`side`)'
    ),
    'url_remap' => array(
      'id' => 'int(11) NOT NULL AUTO_INCREMENT',
      'match' => 'varchar(128) NOT NULL',
      'replace' => 'varchar(128) NOT NULL',
      'PRIMARY KEY (`id`)'
    ),
  );
}
?>