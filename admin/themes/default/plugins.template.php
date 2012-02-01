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
function theme_pluginsInstallError($err) {
	echo $err;
}

function theme_pluginsInstallOutput($msgs) {
	echo '<h2>Installer Output</h2>',$msgs;
}

function theme_pluginsInstallSuccess($linkRoot) {
	echo '<hr /><br /><h1>Install Success!</h1><br /><a href="',$linkRoot,'admin/plugins/list/">Return To Plugins</a>';
}

function theme_pluginsModifyError($err) {
	echo $err;
}

function theme_pluginsModifySuccess($linkRoot) {
	echo 'The changed were saved. Please <a href="',$linkRoot,'admin/plugins/list">click here</a> to return to the plugins.';
}

function theme_pluginsListTableHead($title) {
	echo '
	<table class="pagesList">
		<caption>',$title,'</caption>
		<thead>
			<tr>
				<th class="title">Name</td>
				<th class="buttonList">Controls</td>
			</tr>
		</thead>
		<tbody>
	';
}

function theme_pluginsListTableFoot() {
	echo '
		</tbody>
	</table>';
}

function theme_pluginsListNoneInstalled($msg) {
	echo '
			<tr>
				<td colspan="2">',$msg,'</td>
			</tr>';
}

function theme_pluginsListInstalledTableRow($plugin,$data) {
	echo '
			<tr>
				<td>',$plugin['name'],'</td>
				<td class="buttonList">';
	if($plugin['enabled'])
		echo '<a href="',$data->linkRoot,'admin/plugins/disable/',$plugin['name'],'">Disable</a>';
	else
		echo '<a href="',$data->linkRoot,'admin/plugins/enable/',$plugin['name'],'">Enable</a>';
					echo '
					<a href="',$data->linkRoot,'admin/plugins/modify/',$plugin['name'],'">Modify</a>
				</td>
			</tr>';
}
function theme_pluginEnabledSuccess() {
	echo '<h2>Success!</h2><p>Plugin successfully enabled!</p>';
}
function theme_disabledOfferUninstall($data) {
	echo '<h2>Success!</h2><p>You have successfully disabled the plugin. 
	The data stored by this plugin are still in the database, but the 
	plugin is disabled from user access. If you would like to remove all 
	of the data stored by this plugin from the database then click 
	uninstall. But beware, this data will be gone forever.</p>
	<div class="buttonList">
		<a href="',$data->linkRoot,'admin/plugins/disable/',$data->action[3],'/uninstall">
			Uninstall Module
		</a>
		<a href="',$data->linkRoot,'admin/plugins">
			Return to the List of Plugins
		</a>
	</div>';
}
function theme_uninstalled() {
	echo '<h2>Success!</h2><p>Plugin successfully uninstalled!</p>';
}
function theme_disabled() {
	echo '<h2>Success!</h2><p>Plugin successfully disabled!</p>';
}
?>