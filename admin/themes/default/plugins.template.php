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

function theme_pluginsListInstalledTableRow($pItemName,$pItemId,$linkRoot) {
	echo '
			<tr>
				<td>',$pItemName,'</td>
				<td class="buttonList">
					<a href="',$linkRoot,'admin/plugins/modify/',$pItemId,'">Modify</a>
				</td>
			</tr>';
}

function theme_pluginsListUninstalledTableRow($pluginDir,$linkRoot) {
	echo '
		<tr>
			<td>',$pluginDir,'</td>
			<td class="buttonList">
				<a href="',$linkRoot,'admin/plugins/install/',$pluginDir,'">Install</a>
			</td>
		</tr>';
}

?>