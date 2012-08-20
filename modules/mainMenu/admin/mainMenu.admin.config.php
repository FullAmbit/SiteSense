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
function mainMenu_admin_config($data) {
	$data->permissions['mainMenu']=array(
        'access'               => $data->phrases['core']['permission_mainMenu_access'],
        'add'                  => $data->phrases['core']['permission_mainMenu_add'],
        'delete'               => $data->phrases['core']['permission_mainMenu_delete'],
        'disable'              => $data->phrases['core']['permission_mainMenu_disable'],
        'edit'                 => $data->phrases['core']['permission_mainMenu_edit'],
        'enable'               => $data->phrases['core']['permission_mainMenu_enable'],
        'list'                 => $data->phrases['core']['permission_mainMenu_list']
    );
    
	if(checkPermission('access','mainMenu',$data)) {
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'main-menu/list',
			'name'      => $data->phrases['core']['mainMenu'],
			'sortOrder' => 2
		);
	}
}
?>