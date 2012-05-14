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
function user_settings($data) {
	return array(
		'name' => 'user',
		'shortName' => 'user'
	);
}
function user_install($data,$drop=false) {
	$structures = array(
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
		)
	);
	if($drop)
		$data->dropTable('users');
	$data->createTable('users',$structures['users'],false);
    // Set up default permission groups
    $defaultPermissionGroups=array(
        'Moderator' => array(
            'access',
            'accessOthers',
            'activate',
            'add',
            'ban',
            'edit',
            'delete',
            'permissions'
        ),
        'Writer' => array(
            'access',
			'edit'
        ),
        'Blogger' => array(
            'access',
			'edit'
        ),
        'User' => array(
            'access',
            'edit'
        )
    );
    foreach($defaultPermissionGroups as $groupName => $permissions) {
        foreach($permissions as $permissionName) {
            $statement=$data->prepare('addPermissionByGroupName','common');
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
	if($data->countRows('users')==0) {
		try {
			$newPassword=common_randomPassword();
			echo '
				<h3>Attempting to add admin user</h3>';
			$statement=$data->prepare('addUser','installer');
			$statement->execute(array(
				':name' => 'admin',
				':passphrase' => hash('sha256',$newPassword),
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
?>