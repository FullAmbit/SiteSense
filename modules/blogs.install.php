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
function blogs_settings($data)
{
	return array(
		'name' => 'blogs',
		'shortName' => 'blogs'
	);
}

function blogs_install($data,$drop=false)
{	
	$settings = blogs_settings($data,$data);
	$structures = array(
		'blogs' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'managingEditor' => 'varchar(256) NOT NULL',
			'webMaster' => 'varchar(256) NOT NULL',
			'name' => 'varchar(256) DEFAULT NULL',
			'shortName' => 'varchar(256) DEFAULT NULL',
			'title' => 'varchar(256) DEFAULT NULL',
			'owner' => 'int(11) DEFAULT NULL',
			'minPermission' => 'int(11) DEFAULT NULL',
			'numberPerPage' => 'int(11) DEFAULT NULL',
			'description' => 'mediumtext',
			'allowComments' => 'tinyint(1) NOT NULL DEFAULT \'1\'',
			'commentsRequireLogin' => 'tinyint(1) NOT NULL',
			'topLevel' => 'tinyint(1) NOT NULL',
			'rssOverride' => 'varchar(256) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `name` (`name`,`owner`,`minPermission`)'
		),
		'blog_categories' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'blogId' => 'int(11) NOT NULL',
			'name' => 'varchar(256) NOT NULL',
			'shortName' => 'varchar(256) NOT NULL',
			'PRIMARY KEY (`id`)'
		),
		'blog_comments' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'post' => 'int(11) NOT NULL',
			'time' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'author' => 'varchar(64) NOT NULL',
			'rawContent' => 'text NOT NULL',
			'parsedContent' => 'text NOT NULL',
			'email' => 'varchar(256) NOT NULL',
			'loggedIP' => 'varchar(64) NOT NULL',
			'approved' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'PRIMARY KEY (`id`)'
		),
		'blog_posts' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'blogId' => 'int(11) DEFAULT NULL',
			'categoryId' => 'int(11) NOT NULL DEFAULT \'0\'',
			'title' => 'varchar(256) DEFAULT NULL',
			'name' => 'varchar(256) DEFAULT NULL',
			'shortName' => 'varchar(256) DEFAULT NULL',
			'user' => 'int(11) DEFAULT NULL',
			'postTime' => 'int(11) DEFAULT NULL',
			'modifiedTime' => 'int(11) DEFAULT NULL',
			'live' => 'tinyint(1) DEFAULT NULL',
			'rawSummary' => 'text NOT NULL',
			'parsedSummary' => 'text NOT NULL',
			'rawContent' => 'text NOT NULL',
			'parsedContent' => 'mediumtext',
			'description' => 'varchar(500) NOT NULL',
			'allowComments' => 'tinyint(1) NOT NULL',
			'repliesWaiting' => 'int(11) NOT NULL',
			'tags' => 'text NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `blogId` (`blogId`)'
		)
	);
		
	if($drop) {
		$data->dropTable('blogs');
		$data->dropTable('blog_posts');
		$data->dropTable('blog_comments');
		$data->dropTable('blog_categories');
	}
	
	$data->createTable('blogs',$structures['blogs'],true);
	$data->createTable('blog_posts',$structures['blog_posts'],true);
	$data->createTable('blog_comments',$structures['blog_comments'],true);
	$data->createTable('blog_categories',$structures['blog_categories'],true);

	$count=$data->countRows('blogs');
	if ($count==0) {
		try {
			echo '
				<h3>Attempting:</h3>';
			$data->exec('makeNewsBlog','installer');
			echo '
				<div>
					Home Page News Blog Generated!
				</div><br />
			';
		} catch(PDOException $e) {
			$installErrors++;
			echo '
				<h2>Failed to create Home Page News Blog!</h2>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
	} else echo '<p class="exists">"blogs database" already contains records</p>';
	
	$count=$data->countRows('blog_posts');
	if ($count==0) {
		try {
			echo '
				<h3>Attempting to add Welcome Post</h3>';
			$statement=$data->prepare('makeWelcomePost','installer');
			$statement->execute(array(':time' => time()));
			echo '
				<div>
					Home Page Welcome Post Generated!<br />
				</div><br />';
		} catch(PDOException $e) {
			$data->installErrors++;
			echo '
				<p class="error">Failed to create Home Page Welcome Post!</p>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
	} else echo '<p class="exists">"blogs database" already contains records</p>';
	
	return NULL;
}

function blogs_postInstall($data)
{
}

?>