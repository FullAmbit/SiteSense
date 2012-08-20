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

//configures admin's left menu bar configurations - category, order, name, etc.
function pages_admin_config($data,$db) {
	
	$data->permissions['pages']=array(
        'access'               => $data->phrases['core']['permission_pages_access'],
        'add'                  => $data->phrases['core']['permission_pages_add'],
        'edit'                 => $data->phrases['core']['permission_pages_edit'],
        'editSpecific'         => $data->phrases['core']['permission_pages_editSpecific'], //not being used for now while this comment exists. Requires checkPermissions module update
        //this is the statement that should replace the current ones. This accounts for editSpecific
        //if(!checkPermission('access','pages',$data) && (!checkPermission('editSpecific','pages',$data) == ***PAGE ID***))
        //more complicated logic for editSpecific override, may require function modification for checkPermission
        'delete'               => $data->phrases['core']['permission_pages_delete'],
        'publish'              => $data->phrases['core']['permission_pages_publish']
    );
	
	if (checkPermission('access','pages',$data)) {
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'pages/list',
			'name'      => $data->phrases['core']['pages'],
			'sortOrder' => 3
		);
	}
}
?>