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
function dynamicForms_admin_buildContent($data,$db) {
	//permission check for forms access
	if(!checkPermission('access','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$data->action[2] = strtolower($data->action[2]);
	$target='modules/dynamicForms/admin/includes/dynamicForms.admin.include.'.$data->action[2].'.php';
    if (file_exists($target)) {
        common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_dynamicFormsBuild')) admin_dynamicFormsBuild($data,$db);
	$data->output['pageTitle']='Dynamic Forms';
}
function dynamicForms_admin_content($data) {
    if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_dynamicFormsShow($data);
		} else admin_unknown();
	}
}
?>