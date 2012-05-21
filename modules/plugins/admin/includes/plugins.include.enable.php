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
function admin_pluginsBuild($data,$db){
    if(!checkPermission('plugins_enable','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    if(!$data->action[3]) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No module name was entered to be enabled';
	} else {
		// Include the install file for this plugin
		if(file_exists('plugins/'.$data->action[3].'/install.php'))
			common_include('plugins/'.$data->action[3].'/install.php');
		// Get the plugin's settings
		$targetFunction=$data->action[3].'_settings';
		if(function_exists($targetFunction)) {
			$settings=$targetFunction();
			// Run the plugin installation procedure
			$targetFunction=$data->action[3].'_install';
			if(function_exists($targetFunction))
				$targetFunction($data,$db);
			// Update this plugin in the database
			$statement=$db->prepare('enableAndUpdate','admin_plugins');
			$statement->execute(array(
				':name'				 => $data->action[3],
				':isCDN'			 => isset($settings['isCDN']) ? $settings['isCDN'] : '0',
				':isEditor'		 => isset($settings['isEditor']) ? $settings['isEditor'] : '0'
			));
		} else {
			// Enable this plugin in the database
			$statement=$db->prepare('enable','admin_plugins');
			$statement->execute(array(
				':name' => $data->action[3]
			));
		}
	}
}
function admin_pluginsShow($data) {
	if(empty($data->output['rejectError'])) 
		theme_pluginEnabledSuccess();
	else theme_rejectError($data);
}
?>