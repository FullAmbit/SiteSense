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
define('ADMIN_SHOWPERPAGE',16);
require_once('libraries/admin.common.php');
function page_buildContent($data,$db) {
	$db->loadModuleQueries('admin',true);
	//Preload default values into $data->output:
	$defaults = array(
		'pagesError' => false,
		'abort' => false,
		'abortMessage' => 'abort',
		'blogsStart' => false
	);
	$data->output = array_merge($defaults, $data->output);
	if (checkPermission('access','core',$data)) {
		if (empty($data->action[1])) {
			common_include('modules/dashboard/admin/dashboard.admin.php');
			common_include('themes/default/admin/dashboard.template.php');
		} else {
			if(strstr($data->action[1],"-"))
				$data->action[1] = hyphenToCamel($data->action[1],$ucfirst=false);
			$target='modules/'.$data->action[1].'/admin/'.$data->action[1].'.admin.php';
			if (file_exists($target)) {
				common_include($target);
				$db->loadModuleQueries('admin_'.$data->action[1]);
				$theme = 'themes/default/admin/'.$data->action[1].'.template.php';
				if( file_exists($theme) )
					common_include($theme);
			} else {
				common_include('themes/default/admin/404.static.php');
			}
		}
	
    	$files=glob('modules/*/admin/*.config.php');
		foreach ($files as $fileName) {
			common_include($fileName);
			$targetName=substr(strrchr(str_replace('.config.php','',$fileName),'/'),1);
			$targetName=hyphenToCamel($targetName);
			$targetFunction=$targetName.'_config';
	
			if (function_exists($targetFunction)) {
				$targetFunction($data,$db);
			}
		}
	    usort($data->admin['menu'],'admin_menuCmp');
		if (function_exists('admin_buildContent')) {
			admin_buildContent($data,$db);
		}
	}
}

function page_content($data) {
	if (!checkPermission('access','core',$data)) {
		theme_accessDenied(true);
		theme_loginForm($data);
	} else {
		if (function_exists('admin_content')) {
			admin_content($data);
		} else {
			theme_fatalError('The requested admin.php module is not installed.');
        }
    }
}