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
common_include('libraries/admin.common.php');

function admin_buildContent($data,$db) {
	//Preload default values into $data->output:
	$defaults = array(
		'pagesError' => false,
		'abort' => false,
		'abortMessage' => 'abort',
		'blogsStart' => false
	);
	$data->output = array_merge($defaults, $data->output);
	if (checkPermission('access','core',$data)) {		
		if(empty($data->action[1])) {
			$data->action[1] = 'dashboard';
		}
		$moduleQuery=$db->prepare('getModuleByShortName','admin_modules');
		$moduleQuery->execute(array(':shortName' => $data->action[1]));
		$module=$moduleQuery->fetch(PDO::FETCH_ASSOC);
		if($module==FALSE){
			common_redirect($data->linkRoot.'admin');
		}
		// Get the plugins for this module
		$statement= $db->prepare('getEnabledPluginsByModule','common');
		$statement->execute(array(
				':moduleID' => $module['id']
			));
		$plugins=$statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($plugins as $plugin) {
			common_include('plugins/'.$plugin['name'].'/plugin.php');
			$objectName='plugin_'.$plugin['name'];
			$data->plugins[$plugin['name']]=new $objectName;
		}
				
		// Get Phrases
		$statement = $db->prepare('getPhrasesByModule', 'common');
		// Core Phraes
		$statement->execute(array(
				':module' => '',
				':isAdmin' => 1
			));
			while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$data->phrases['core'][$row['phrase']] = $row['text'];
		}
		
		// Module-Specific Phrases
		$statement->execute(array(
				':module' => $data->action[1],
				':isAdmin' => 1
			));
			while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$data->phrases[$data->action[1]][$row['phrase']] = $row['text'];
		}
				
		$data->currentModule=$module['name'];
        common_include('modules/'.$module['name'].'/admin/'.$module['name'].'.admin.php');
		$currentThemeInclude=$data->themeDir.'admin/'.$module['name'].'.admin.template.php';
		$defaultThemeInclude='themes/default/admin/'.$module['name'].'.admin.template.php';
		$moduleThemeInclude='modules/'.$module['name'].'/admin/'.$module['name'].'.admin.template.php';
		if(file_exists($moduleThemeInclude)) {
			common_include($moduleThemeInclude);
		} elseif(file_exists($currentThemeInclude)) {
			common_include($currentThemeInclude);
		} elseif(file_exists($defaultThemeInclude)) {
			common_include($defaultThemeInclude);
		}
		$files=glob('modules/*/admin/*.admin.config.php');
		foreach ($files as $fileName) {
			common_include($fileName);
			$strFind=array('.config.php','.admin');
			$targetName=substr(strrchr(str_replace($strFind,'',$fileName),'/'),1);
			if(!isset($data->output['moduleShortName'][$targetName])) continue;
			$targetFunction=$targetName.'_admin_config';
			if (function_exists($targetFunction)) {
				$targetFunction($data,$db);
			}
    	}
		usort($data->admin['menu'],'admin_menuCmp');
		$buildContent=$data->currentModule.'_admin_buildContent';
        if (function_exists($buildContent)) {
            $buildContent($data,$db);
		}
	}
}

function admin_content($data) {
    if (!checkPermission('access','core',$data)) {
      theme_accessDenied(true);
      theme_loginForm($data);
    } else {
        if (function_exists('admin_content')) {
            $content=$data->currentModule.'_admin_content';
            $content($data);
        } else {
          theme_fatalError('The requested admin.php module is not installed.');
        }
    }
}