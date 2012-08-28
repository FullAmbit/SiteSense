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
function admin_modulesBuild($data,$db){
    if(!checkPermission('disable','modules',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    if(!$data->action[3]) {
		$data->output['rejectError']=$data->phrases['modules']['insufficientParameter'];
		$data->output['rejectText']=$data->phrases['modules']['insufficientParameterModuleName'];
	} else {
		if(!$data->action[4]) {
			// Disable the module
			$statement=$db->prepare('disableModule','admin_modules');
			$statement->execute(array(
				':shortName' => $data->action[3]
			));
		} elseif($data->action[4]=='uninstall') {
			// Include the install file for this module
			$targetInclude='modules/'.$data->action[3].'/'.$data->action[3].'.install.php';
			if(!file_exists($targetInclude)) {
			$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
			$data->output['rejectText']=$data->phrases['modules']['errorInstallFileNotFound'];
			} else {
				common_include($targetInclude);
				// Run the module uninstall procedure
				$targetFunction=$data->action[3].'_uninstall';
				if(!function_exists($targetFunction)) {
					$data->output['rejectError']=$data->phrases['modules']['errorHeading'];
					$data->output['rejectText']=$data->phrases['modules']['errorUninstallFunctionNotFound'];
				} else {
					foreach($data->languageList as $languageItem) {
						$targetFunction($db,$languageItem['shortName']);
					}
				}
			}
		}
	}
}
function admin_modulesShow($data) {
	if(empty($data->output['rejectError'])) {
		$targetInclude='modules/'.$data->action[3].'/'.$data->action[3].'.install.php';
		if(file_exists($targetInclude)
		&& !$data->action[4]) {
			theme_disabledOfferUninstall($data);
		} else {
			theme_disabled($data);
		}
	} else {
		theme_rejectError($data);
	}
}
?>