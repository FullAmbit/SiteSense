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
common_include('libraries/forms.php');
function admin_pluginsBuild($data,$db) {
    if(!checkPermission('plugins_edit','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    if(!$data->action[3]) {
		$data->output['pluginError'] = 'You did not specify a plugin.';
		return;
	}
	// Check If It Exists
	$statement=$db->prepare('getPluginByName','admin_plugins');
	$statement->execute(array(':name' => $data->action[3]));
	$data->output['pluginItem']=$statement->fetch();
	$statement=$db->prepare('getModulesEnabledForPlugin','admin_plugins');
	$statement->execute(array(':plugin' => $data->output['pluginItem']['id']));
	$appliedModules=$statement->fetchAll(PDO::FETCH_COLUMN, 0);
	if(!$data->output['pluginItem']){
		$data->output['pluginError'] = 'The plugin you specified could not be found.';
		return;
	}
	// Is This A Default Plugin That Is Loaded By The CMS? (e.g. an editor or a CDN?)
	if($data->output['pluginItem']['isCDN'] == '1' || $data->output['pluginItem']['isEditor'] == '1') {
		$data->output['pluginError'] = 'This plugin is auto-loaded by SiteSense for core functionality.';
		return;
	}
	// Load The Form
	$data->output['pluginForm']=new formHandler('edit',$data,true);
	// Load The List Of Modules
	$statement = $db->prepare('getEnabledModules','admin_modules');
	$statement->execute();
	$moduleList = $statement->fetchAll();
	// Insert Modules Into FormHandler
	foreach($moduleList as $moduleItem) {
		$data->output['pluginForm']->fields[$moduleItem['id']] = array(
			'label' => $moduleItem['name'],
			'tag' => 'input',
			'params' => array(
				'type' => 'checkbox',
			),
			'required' => false,
			'error' => '',
			'class' => '',
			'compareFailed' => ''
		);
		if(in_array($moduleItem['id'],$appliedModules)) {
			$data->output['pluginForm']->fields[$moduleItem['id']]['params']['checked'] = 'checked';
		}
	}
	// Post Command??
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm'] == $data->output['pluginForm']->fromForm)) {
		$data->output['pluginForm']->populateFromPostData();
		if($data->output['pluginForm']->validateFromPost($data)) {
			foreach($data->output['pluginForm']->sendArray as $pluginItemId => $enabled){
				$module=substr($pluginItemId,1);
				if($enabled 
				&& !in_array($module,$appliedModules))
					$enabledModules[]=$module;
				else if (!$enabled
				&& in_array($module,$appliedModules))
					$disabledModules[]=$module;
			}
			// Query
			if(isset($enabledModules)) {
				$statement=$db->prepare('enablePluginForModule','admin_plugins');
				foreach($enabledModules as $enabledModule) {
					$statement->execute(array(
						':plugin'		 => $data->output['pluginItem']['id'],
						':module'		 => $enabledModule
					));
					$data->output['pluginForm']->fields[$enabledModule]['params']['checked']='checked';
				}
			}
			if(isset($disabledModules)) {
				$statement=$db->prepare('disablePluginForModule','admin_plugins');
				foreach($disabledModules as $disabledModule) {
					$statement->execute(array(
						':plugin'		 => $data->output['pluginItem']['id'],
						':module'		 => $disabledModule
					));
					unset($data->output['pluginForm']->fields[$disabledModule]['params']['checked']);
				}
			}
		}
	}
}
function admin_pluginsShow($data) {
	if(isset($data->output['pluginError'])) {
		theme_pluginsModifyError($data->output['pluginError']);
		return;
	}
	if(isset($data->output['pluginForm'])) {
		theme_buildForm($data->output['pluginForm']);
	}
}
?>