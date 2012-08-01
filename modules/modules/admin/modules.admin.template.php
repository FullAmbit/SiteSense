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
function theme_modulesSidebarsTableHead($data) {
	/*echo '<pre>';
	var_dump($data);
	echo '</pre>';*/
	echo '
		<table class="sidebarList">
			<caption>Manage Sidebars on the "',ucfirst($data->output['module']['name']),'" Module</caption>
			<thead>
				<tr>
					<th class="name">Name</th>
					<th>Enabled</th>
					<th>Controls</th>
				</tr>
			</thead><tbody>';
}
function theme_modulesSidebarsTableRow($data,$sidebar,$action,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td class="name">', $sidebar['name'], '</td>
				<td>', ($sidebar['enabled'] ? 'Yes' : 'No'), '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/modules/sidebars/', $data->output['module']['id'], '/', $action, '/', $sidebar['id'], '">
						', ucfirst($action), '
					</a>
					<a href="',$data->linkRoot,'admin/modules/sidebars/',$data->output['module']['id'],'/moveUp/',$sidebar['id'],'" title="Move Up">&uArr;</a>
					<a href="',$data->linkRoot,'admin/modules/sidebars/',$data->output['module']['id'],'/moveDown/',$sidebar['id'],'" title="Move Down">&dArr;</a>
				</td>
			</tr>';
}
function theme_modulesSidebarsTableFoot() {
	echo '
			</tbody>
		</table>';
}
function theme_modulesListTableHead() {
	echo '
		<table class="modulesList">
			<caption>Manage Modules</caption>
			<tr>
				<th>Name</th>
				<th>URL</th>
				<th>Enabled</th>
				<th>Controls</th>
			</tr>
			';
}
function theme_modulesListNoModules() {
	echo '<tr><td colspan="4">No modules exist</tr></td>';
}
function theme_modulesListTableRow($data,$module,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td>', $module['name'], '</td>
				<td>', $module['shortName'], '</td>
				<td>', (($module['enabled'] == 1) ? 'yes' : 'no'), '</td> 
				<td class="buttonList">';
				
	if($module['enabled'])
		echo '<a href="', $data->linkRoot, 'admin/modules/disable/',$module['shortName'],'">Disable</a>';
	else
		echo '<a href="', $data->linkRoot, 'admin/modules/enable/',$module['shortName'],'">Enable</a>';
	echo '<a href="',$data->linkRoot,'admin/modules/languages/',$module['id'],'">Install Language</a>';
		switch($module['shortName']) {
			case 'forms':
				$sidebarsLink = 'admin/dynamic-forms/list/';
				break;
			case 'page':
				$sidebarsLink = 'admin/pages/list';
				break;
			default:
				$sidebarsLink = 'admin/modules/sidebars/'.$module['id'];
				break;
		}
		echo'	
					<a href="',$data->linkRoot,$sidebarsLink,'">Select Sidebars</a>
				</td>
			</tr>';
}
function theme_modulesListTableFoot() {
	echo '
		</table>
	';
}
function theme_modulesInstallSuccess($data=null) {
	echo '<h2>Success!</h2><p>Module successfully installed!</p>';
	echo 
	'<div class="buttonList">
		<a href="',$data->linkRoot,'admin/modules">
			Return to the List of Modules
		</a>
	</div>';
}
function theme_disabledOfferUninstall($data) {
	echo '<h2>Success!</h2><p>You have successfully disabled the module. 
	The data stored by this module are still in the database, but the 
	module is disabled from user access. If you would like to remove all 
	of the data stored by this module from the database then click 
	uninstall. But beware, this data will be gone forever.</p>
	<div class="buttonList">
		<a href="',$data->linkRoot,'admin/modules/disable/',$data->action[3],'/uninstall">
			Uninstall Module
		</a>
		<a href="',$data->linkRoot,'admin/modules">
			Return to the List of Modules
		</a>
	</div>';
}
function theme_disabled($data) {
	echo '<h2>Success!</h2><p>Module successfully disabled!</p>';
	echo 
	'<div class="buttonList">
		<a href="',$data->linkRoot,'admin/modules">
			Return to the List of Modules
		</a>
	</div>';
}

function theme_modulesLanguages($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption>Update Languages For <b>',ucwords($data->output['moduleItem']['name']),'</b></caption><br />
		Language:
		<select name="language">';
	foreach($data->output['languageList'] as $languageShortName => $languageName){
		echo
		'	<option value="',$languageShortName,'">',$languageName,'</option>';
	}
	echo '
		</select>
		Action:
		<select name="action">
			<option value="0">Clear Table And Start Fresh</option>
			<option value="1">Only Install Phrases That Don\'t Currently Exist</option>
			<option value="2">Update Existing Phrases And Install New Ones</option>
		</select>
		<input type="submit" name="install" value="Install" />
	</form>';
}

function theme_modulesLanguageSuccess($data){
	echo 'The language ',$data->output['languageItem']['name'],' has been updated for ',ucwords($data->output['moduleItem']['name']),'.';
}
?>