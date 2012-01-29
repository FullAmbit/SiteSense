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
	if(!$data->action[3]) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No module name was entered to be enabled';
	} else {
		// Include the install file for this module
		$targetInclude='modules/'.$data->action[3].'.install.php';
		if(!file_exists($targetInclude)) {
			$data->output['rejectError']='Module installation file does not exist';
			$data->output['rejectText']='The module installation could not be found.';
		} else {
			common_include($targetInclude);
			// Get the module's settings
			$targetFunction=$data->action[3].'_settings';
			if(!function_exists($targetFunction)) {
				$data->output['rejectError']='Improper installation file';
				$data->output['rejectText']='The module settings function could not be found within the module installation file.';
			} else {
				$moduleSettings=$targetFunction($db);
				// Insert module into the database
				$statement=$db->prepare('updateModule','admin_modules');
				$statement->execute(
					array(
						':name' => $moduleSettings['name'],
						':shortName' => $moduleSettings['shortName'],
						':enabled' => 1
					)
				);
				// Run the module installation procedure
				$targetFunction=$data->action[3].'_install';
				if(!function_exists($targetFunction)) {
					$data->output['rejectError']='Improper installation file';
					$data->output['rejectText']='The module install function could not be found within the module installation file.';
				} else $targetFunction($db);
			}
		}
	}
}
function admin_modulesShow($data) {
	if (empty($data->output['rejectError'])) {
		theme_modulesInstallSuccess();
	} else {
		theme_rejectError($data);
	}
}
?>