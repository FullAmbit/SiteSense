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
function admin_usersBuild($data,$db) {
	//permission check for users access
    if($data->user['id']!==$data->action[3] && !checkPermission('accessOthers','users',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }

	$data->output['delete']='';
	if (empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']=$data->phrases['core']['invalidID'];
	} else {
		if (checkPermission('delete','users',$data)) {
			if (@$_POST['fromForm']==$data->action[3]) {
				if (!empty($_POST['delete'])) {
					$qHandle=$db->prepare('deleteUserById','admin_users');
					$qHandle->execute(array(
						':id' => $data->action[3]
					));
                    // Delete from user_groups
                    $statement=$db->prepare('deleteUserFromUserGroups','admin_users');
                    $statement->execute(array(
                        ':userID' => $data->action[3]
                    ));
                    // Delete user specific permissions
                    $statement=$db->prepare('deleteUserFromUserPermissions','admin_users');
                    $statement->execute(array(
                        ':userID' => $data->action[3]
                    ));

					$data->output['deleteCount']=$qHandle->rowCount();
					if ($data->output['deleteCount']>0) {
						$data->output['delete']='deleted';
					} else {
						$data->output['rejectError']=$data->phrases['core']['databaseErrorHeading'];
						$data->output['rejectText']=$data->phrases['core']['databaseErrorMessage'];
					}
				} else {
					/* from form plus not deleted must == cancelled. */
					$data->output['delete']='cancelled';
				}
			}
		} else {
       	 	$data->output['abort'] = true;
			$data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		}
	}
}
function admin_usersShow($data) {
	if (empty($data->output['rejectError'])) {
		switch ($data->output['delete']) {
			case 'deleted':
				theme_usersDeleteDeleted($data);
			break;
			case 'cancelled':
				theme_usersDeleteCancelled($data);
			break;
			default:
				theme_usersDeleteDefault($data);
			break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>