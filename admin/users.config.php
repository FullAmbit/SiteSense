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
function users_config($data,$db) {
	if (checkPermission('access','users',$data)) {
		if (checkPermission('add','users',$data)) {
			$data->admin['menu'][]=array(
				'category'  => 'User Management',
				'command'   => 'users/add',
				'name'      => 'Add New User',
				'sortOrder' => 1
			);
		}
		$data->admin['menu'][]=array(
			'category'  => 'User Management',
			'command'   => 'users/list',
			'name'      => 'Browse Users',
			'sortOrder' => 2
		);
		if (checkPermission('accessOthers','users',$data)) {
			$data->admin['menu'][]=array(
				'category'  => 'User Management',
				'command'   => 'users/search',
				'name'      => 'Search Users',
				'sortOrder' => 3
			);
			$data->admin['menu'][]=array(
				'category'  => 'User Management',
				'command'   => 'users/activation',
				'name'      => 'Activate Users',
				'sortOrder' => 4
			);
			$data->admin['menu'][]=array(
				'category'  => 'User Management',
				'command'   => 'users/list/staff',
				'name'      => 'Staff Members',
				'sortOrder' => 5
			);
			$data->admin['menu'][]=array(
				'category'  => 'User Management',
				'command'   => 'users/permissions',
				'name'      => 'Permissions',
				'sortOrder' => 6
			);
		}
	}
}
?>