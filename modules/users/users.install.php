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
function users_settings() {
	return array(
		'name'      => 'users',
		'shortName' => 'users'
	);
}
function users_install($db,$drop=false) {
	$structures = array(
        'activations' => array(
            'userId'              => SQR_IDKey,
            'hash'                => 'VARCHAR(255)',
            'expires'             => SQR_time
        ),
        'banned' => array(
            'id'                  => SQR_IDKey,
            'userId'              => SQR_ID,
            'email'               => SQR_email,
            'ipAddress'           => SQR_IP,
            'timestamp'           => SQR_added,
            'expiration'          => SQR_time,
            'UNIQUE KEY `userId` (`userId`)'
        ),
        'sessions' => array(
            'sessionId'           => 'VARCHAR(255) NOT NULL PRIMARY KEY',
            'userId'              => SQR_ID,
            'expires'             => SQR_time,
            'ipAddress'           => SQR_IP,
            'userAgent'           => 'VARCHAR(255)',
            'KEY `userId` (`userId`,`expires`)'
        ),
        'users' => array(
			'id'                  => SQR_IDKey,
			'name'                => SQR_username,
			'password'            => SQR_password,
			'fullName'            => SQR_fullName,
			'registeredDate'      => SQR_added,
			'registeredIP'        => SQR_IP,
			'lastAccess'          => SQR_time,
			'contactEMail'        => SQR_email,
			'publicEMail'         => SQR_email,
			'emailVerified'       => SQR_boolean.' DEFAULT \'0\''
		),
        'user_groups' => array(
            'userID'              => SQR_ID,
            'groupName'           => SQR_name,
            'expires'             => SQR_time
        ),
        'user_group_permissions' => array(
            'groupName'           => SQR_name,
            'permissionName'      => SQR_name
        ),
        'user_permissions' => array(
            'userId'              => SQR_ID,
            'permissionName'      => SQR_name,
            'allow'               => SQR_boolean
        )
	);
	if($drop)
		users_uninstall($db);

	$db->createTable('activations',$structures['activations'],false);
    $db->createTable('banned',$structures['banned'],false);
    $db->createTable('sessions',$structures['sessions'],false);
    $db->createTable('users',$structures['users'],false);
    $db->createTable('user_groups',$structures['user_groups'],false);
    $db->createTable('user_group_permissions',$structures['user_group_permissions'],false);
    $db->createTable('user_permissions',$structures['user_permissions'],false);

    // Set up default permission groups
    $defaultPermissionGroups=array(
        'Moderator' => array(
            'users_access',
            'users_accessOthers',
            'users_activate',
            'users_add',
            'users_ban',
            'users_edit',
            'users_delete',
            'users_permissions'
        ),
        'Writer' => array(
            'users_access',
			'users_edit'
        ),
        'Blogger' => array(
            'users_access',
			'users_edit'
        ),
        'User' => array(
            'users_access',
            'users_edit'
        )
    );
    foreach($defaultPermissionGroups as $groupName => $permissions) {
        foreach($permissions as $permissionName) {
            $statement=$db->prepare('addPermissionByGroupName');
            $statement->execute(
                array(
                    ':groupName' => $groupName,
                    ':permissionName' => $permissionName
                )
            );
        }
    }
    // ---
	// Generate an admin account if this is a fresh installation
	if($db->countRows('users')==0) {
		try {
			$newPassword=common_randomPassword();
			echo '
				<h3>Attempting to add admin user</h3>';
			$statement=$db->prepare('addUser','installer');
			$statement->execute(array(
				':name' => 'admin',
				':passphrase' => hash('sha256',$newPassword),
				':registeredIP' => $_SERVER['REMOTE_ADDR']
			));
			echo '
				<p>Administrator account automatically generated!</p>';
		} catch(PDOException $e) {
			$db->installErrors++;
			echo '
				<h3 class="error">Failed to create administrator account!</h3>
				<pre>',$e->getMessage(),'</pre><br />';
		}
	} else echo '<p class="exists">"users database" already contains records</p>';
	return $newPassword;
}
function users_uninstall($db) {
    $db->dropTable('activations');
    $db->dropTable('banned');
    $db->dropTable('sessions');
    $db->dropTable('users');
    $db->dropTable('user_groups');
    $db->dropTable('user_group_permissions');
    $db->dropTable('user_permissions');
}
?>