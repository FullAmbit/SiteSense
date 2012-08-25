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
			<caption>',$data->phrases['modules']['manageSidebarsHeading'],'&nbsp;"',ucfirst($data->output['module']['name']),'"</caption>
			<thead>
				<tr>
					<th class="name">',$data->phrases['core']['name'],'</th>
					<th>',$data->phrases['core']['enabled'],'</th>
					<th>',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}
function theme_modulesSidebarsTableRow($data,$sidebar,$action,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td class="name">', $sidebar['name'], '</td>
				<td>', ($sidebar['enabled'] ? $data->phrases['core']['yes'] : $data->phrases['core']['no']), '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/modules/sidebars/', $data->output['module']['id'], '/', $action, '/', $sidebar['id'], '">
						', $data->phrases['core'][$action], '
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
function theme_modulesListTableHead($data) {
	echo '
		<table class="modulesList">
			<caption>',$data->phrases['modules']['manageModulesHeading'],'</caption>
			<tr>
				<th>',$data->phrases['core']['name'],'</th>
				<th>',$data->phrases['core']['shortName'],'</th>
				<th>',$data->phrases['core']['version'],'</th>
				<th>',$data->phrases['core']['enabled'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
			';
}
function theme_modulesListNoModules() {
	echo '<tr><td colspan="4">',$data->phrases['modules']['noModulesExist'],'</tr></td>';
}
function theme_modulesListTableRow($data,$module,$count) {
	if ($module['version']=='0.0') {
		$module['version']='not specified';
	}
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td>', $module['name'], '</td>
				<td>', $module['shortName'], '</td>
				<td>', $module['version'], '</td>
				<td>', (($module['enabled'] == 1) ? 'yes' : 'no'), '</td> 
				<td class="buttonList">';
				
	if($module['enabled'])
		echo '<a href="', $data->linkRoot, 'admin/modules/disable/',$module['shortName'],'">',$data->phrases['core']['disable'],'</a>';
	else
		echo '<a href="', $data->linkRoot, 'admin/modules/enable/',$module['shortName'],'">',$data->phrases['core']['enable'],'</a>';
	echo '
	<a href="',$data->linkRoot,'admin/modules/installPhrases/',$module['id'],'">Install Phrases</a>
	<a href="',$data->linkRoot,'admin/modules/updateTranslations/',$module['id'],'">Update Translations</a>
	';
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
					<a href="',$data->linkRoot,$sidebarsLink,'">',$data->phrases['modules']['selectSidebars'],'</a>
				</td>
			</tr>';
}
function theme_modulesListTableFoot() {
	echo '
		</table>
	';
}
function theme_modulesInstallSuccess($data) {
	echo '<h2>',$data->phrases['modules']['moduleInstallSuccessHeading'],'</h2><p>',$data->phrases['modules']['moduleInstallSuccessMessage'],'</p>
	<div class="buttonList">
		<a href="',$data->linkRoot,'admin/modules">
			',$data->phrases['modules']['returnToModuleList'],'
		</a>
	</div>';
}
function theme_disabledOfferUninstall($data) {
	echo '<h2>',$data->phrases['modules']['moduleDisableSuccessHeading'],'</h2><p>',$data->phrases['modules']['moduleDisableOfferUninstall'],'</p>
	<div class="buttonList">
		<a href="',$data->linkRoot,'admin/modules/disable/',$data->action[3],'/uninstall">
			',$data->phrases['modules']['uninstallModule'],'
		</a>
		<a href="',$data->linkRoot,'admin/modules">
			',$data->phrases['modules']['returnToModuleList'],'
		</a>
	</div>';
}
function theme_disabled($data) {
	echo '<h2>',$data->phrases['modules']['moduleDisableSuccessHeading'],'</h2>';
	echo 
	'<div class="buttonList">
		<a href="',$data->linkRoot,'admin/modules">
			',$data->phrases['modules']['returnToModuleList'],'
		</a>
	</div>';
}

function theme_modulesLanguages($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption>',$data->phrases['modules']['updateLanguageHeading'],'&nbsp;<b>',ucwords($data->output['moduleItem']['name']),'</b></caption><br />
		',$data->phrases['modules']['language'],'
		<select name="updateLanguage">';
	foreach($data->languageList as $languageItem){
		echo
		'	<option value="',$languageItem['shortName'],'">',$languageItem['name'],'</option>';
	}
	echo '
		</select>
		',$data->phrases['modules']['phraseAction'],'
		<select name="action">
			<option value="0">',$data->phrases['modules']['updateActionClear'],'</option>
			<option value="1" selected="selected">',$data->phrases['modules']['updateActionNew'],'</option>
			<option value="2">',$data->phrases['modules']['updateActionAll'],'</option>
		</select>
		<input type="submit" name="install" value="',$data->phrases['modules']['updateLanguageSubmitButton'],'" />
	</form>';
}

function theme_modulesLanguageSuccess($data){
	echo $data->phrases['modules']['updateLanguageSuccessMessage'],'&nbsp;',ucwords($data->output['moduleItem']['name']);
}

function theme_modulesInstallPhrases($data){
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption>Install English Phrases For The Module ',$data->output['moduleItem']['name'],'</caption><br />
		',$data->phrases['modules']['phraseAction'],'
		<select name="action">
			<option value="0">',$data->phrases['modules']['updateActionClear'],'</option>
			<option value="1" selected="selected">',$data->phrases['modules']['updateActionNew'],'</option>
			<option value="2">',$data->phrases['modules']['updateActionAll'],'</option>
		</select><br />
		<input type="submit" name="install" value="Install" />
	</form>';
}

function theme_modulesUpdateTranslationSuccess($data){
	$list = array('userErrors','adminErrors');
	$error = FALSE;
	foreach($list as $index => $varName){
		if(isset($data->output[$varName]) && !empty($data->output[$varName])){
			$error = TRUE;
			break;
		}
	}
	if($error == FALSE){
		echo '<h2>The translation went smoothly and all phrases were updated.</h2>';
	}else{
		echo '<h2>Existing phrases were updated, however some phrases were found without an English counterpart. Please add the following phrases to English first.</h2>
		<br />';
		
		if(isset($data->output['userErrors']) && !empty($data->output['userErrors'])){
			echo '<h1>User Phrases</h1>';
			foreach($data->output['userErrors'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
		
		if(isset($data->output['adminErrors']) && !empty($data->output['adminErrors'])){
			echo '<h1>Admin Phrases</h1>';
			foreach($data->output['adminErrors'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
	}
}
?>