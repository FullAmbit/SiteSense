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
    if(!checkPermission('disable','plugins',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    if(!$data->action[3]) {
		$data->output['rejectError']=$data->phrases['plugins']['insuficientPrameters'];
		$data->output['rejectText']=$data->phrases['plugins']['noPluginNameEntered'];
	} else {
		if(!$data->action[4]) {
			// Disable the plugin
			$statement=$db->prepare('disable','admin_plugins');
			$statement->execute(array(
				':name' => $data->action[3]
			));
		} else if($data->action[4]=='uninstall') {
			common_include('plugins/'.$data->action[3].'/install.php');
			// Run the plugin uninstall procedure
			$targetFunction=$data->action[3].'_uninstall';
			if(!function_exists($targetFunction)) {
				$data->output['rejectError']=$data->phrases['plugins']['improperInstallFile'];
				$data->output['rejectText']=$data->phrases['plugins']['uninstallFunctionNotFound'];
			} else $targetFunction($data,$db);
		}
	}
}
function admin_pluginsShow($data) {
	if(empty($data->output['rejectError'])) {
		$targetInclude='plugins/'.$data->action[3].'/install.php';
		if($data->action[4]) theme_uninstalled();
		else if(file_exists($targetInclude)) {
			common_include($targetInclude);
			if(function_exists($data->action[3].'_uninstall'))
				theme_disabledOfferUninstall($data);
			else theme_disabled();
		}
	} else theme_rejectError($data);
}
?>