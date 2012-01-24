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
function user_settings($data)
{
	return array(
		'name' => 'user',
		'shortName' => 'user'
	);
}

function user_install($data,$drop=false)
{	
	$settings = user_settings($data);
	$structures = array(
		'users' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(64) DEFAULT NULL',
			'password' => 'varchar(64) DEFAULT NULL',
			'fullName' => 'varchar(128) DEFAULT NULL',
			'userLevel' => 'int(11) DEFAULT NULL',
			'registeredDate' => 'int(11) DEFAULT NULL',
			'registeredIP' => 'varchar(64) DEFAULT NULL',
			'lastAccess' => 'int(11) DEFAULT NULL',
			'contactEMail' => 'varchar(255) DEFAULT NULL',
			'publicEMail' => 'varchar(255) DEFAULT NULL',
			'emailVerified' => 'tinyint(1) DEFAULT \'0\'',
			'PRIMARY KEY (`id`)'
		)
	);
	
	if($drop)
		$data->dropTable('users');
	
	$data->createTable('users',$structures['users'],true);
	
	$count=$data->countRows('users');
	if ($count==0) {
		try {
			$newPassword=common_randomPassword();
			echo '
				<h3>Attempting to add admin user</h3>';
			$statement=$data->prepare('addUser','installer');
			$statement->execute(array(
				':name' => 'admin',
				':passphrase' => hash('sha256',$newPassword),
				':userLevel' => 255,
				':registeredDate' => time(),
				':registeredIP' => $_SERVER['REMOTE_ADDR']
			));
			echo '
				<p>Administrator account automatically generated!</p>';
		} catch(PDOException $e) {
			$data->installErrors++;
			echo '
				<h3 class="error">Failed to create administrator account!</h3>
				<pre>',$e->getMessage(),'</pre><br />';
		}
	} else echo '<p class="exists">"users database" already contains records</p>';
	
	return $newPassword;
}

function user_postInstall($data)
{
}

?>