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
function users_admin_config($data,$db) {
	$data->permissions['users']=array(
        'access'               => $data->phrases['core']['usersAccess'],
        'accessOthers'         => $data->phrases['core']['manageUsers'],
        'activate'             => $data->phrases['core']['activateUsers'],
        'add'                  => $data->phrases['core']['addUsers'],
        'ban'                  => $data->phrases['core']['banUsers'],
        'edit'                 => $data->phrases['core']['editUsers'],
        'delete'               => $data->phrases['core']['deleteUsers'],
        'groups'               => $data->phrases['core']['manageGroups']
    );
	if (checkPermission('access','users',$data)) {
		if (checkPermission('add','users',$data)) {
			$data->admin['menu'][]=array(
				'category'  => $data->phrases['core']['userManagement'],
				'command'   => 'users/add',
				'name'      => $data->phrases['core']['add'],
				'sortOrder' => 1
			);
		}
        if (checkPermission('accessOthers','users',$data)) {
            $data->admin['menu'][]=array(
                'category'  => $data->phrases['core']['userManagement'],
                'command'   => 'users/list',
                'name'      => $data->phrases['core']['browse'],
                'sortOrder' => 2
            );
			$data->admin['menu'][]=array(
				'category'  => $data->phrases['core']['userManagement'],
				'command'   => 'users/search',
				'name'      => $data->phrases['core']['search'],
				'sortOrder' => 3
			);
			/* Disabled until feature completely built
            $data->admin['menu'][]=array(
				'category'  => $data->phrases['users']['userManagement'],
				'command'   => 'users/activation',
				'name'      => $data->phrases['users']['activate'],
				'sortOrder' => 4
			);
			*/
			$data->admin['menu'][]=array(
				'category'  => $data->phrases['core']['userManagement'],
				'command'   => 'users/permissions',
				'name'      => $data->phrases['core']['permissions'],
				'sortOrder' => 5
			);
		}
	}
}
?>