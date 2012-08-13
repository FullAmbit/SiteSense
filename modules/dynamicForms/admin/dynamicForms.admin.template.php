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
function theme_dynamicFormsDeleteReject($data,$aRoot) {
	echo '
		<h2>',$data->phrases['dynamic-forms']['deleteFormRejectHeading'],'</h2>
		',$data->phrases['dynamic-forms']['deleteFormRejectMesssage'],'
		<div class="buttonList">
			<a href="'.$aRoot.'" title="Return To Forms">',$data->phrases['dynamic-forms']['returnToForms'],'</a>
		</div>';
}

function theme_dynamicFormsDeleteCancelled($data,$aRoot) {
	echo '
		<h2>',$data->phrases['dynamic-forms']['deleteFormCancelledHeading'],'</h2>
		<p>
			',$data->phrases['core']['messageRedirect'],'
			<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a>
		</p>';
}

function theme_dynamicFormsDeleteDeleted($data,$aRoot) {
	echo $data->phrases['dynamic-forms']['deleteFormSuccessMessage'],'
		
		<div class="buttonList">
			<a href="'.$aRoot.'" title="Return To Forms">',$data->phrases['dynamic-forms']['returnToForms'],'</a>
		</div>';
}

function theme_dynamicFormsDeleteDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'delete/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>'.$data->phrases['dynamic-forms']['deleteFormConfirmHeading'].'</legend>
			</fieldset>
			<input type="submit" name="delete" value="'.$data->phrases['core']['actionConfirmDelete'].'" />
			<input type="submit" name="cancel" value="'.$data->phrases['core']['actionCancelDelete'].' />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_dynamicFormsDeleteFieldCancelled($data,$aRoot) {
	echo $data->phrases['dynamic-forms']['deleteFieldCancelledHeading'],'
		<div class="buttonList"><a href="'.$aRoot.'listFields/'.$data->output['formItem']['id'].'" title="Return To Field List">',$data->phrases['dynamic-forms']['returnToFields'],'</a></div>';
}

function theme_dynamicFormsDeleteFieldDeleted($data,$aRoot) {
	echo '<h2>',$data->phrases['dynamic-forms']['deleteFieldSuccessHeading'],'</h2><p>',$data->phrases['dynamic-forms']['deleteFieldSuccessMessage'],$data->phrases['core']['messageRedirect'],'<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a></p>';
}

function theme_dynamicFormsDeleteFieldDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteField/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				'.$data->phrases['dynamic-forms']['deleteFieldConfirmHeading'].'</legend>
			</fieldset>
			<input type="submit" name="delete" value="'.$data->phrases['core']['actionConfirmDelete'].'" />
			<input type="submit" name="cancel" value="'.$data->phrases['core']['actionCancelDelete'].'" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_dynamicFormsDeleteOptionCancelled($data,$aRoot) {
	echo 'You have cancelled the deletion. <div class="buttonList"><a href="'.$aRoot.'listOptions/'.$data->output['optionItem']['fieldId'].'" title="Return To Options">Return To Options.</a></div>';
}

function theme_dynamicFormsDeleteOptionDeleted($data,$aRoot) {
	echo '<h2>',$data->phrases['dynamic-forms']['deleteOptionCancelledHeading'],'</h2><p>',$data->phrases['core']['deleteOptionSuccessMessage'],'<br />',$data->phrases['core']['messageRedirect'],'<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a></p>';
}

function theme_dynamicFormsDeleteOptionDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteOption/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>'.$data->phrases['dynamic-forms']['deleteOptionConfirmHeading'].'</legend>
			</fieldset>
			<input type="submit" name="delete" value="'.$data->phrases['core']['actionConfirmDelete'].'" />
			<input type="submit" name="cancel" value="'.$data->phrases['core']['actionCancelDelete'].'" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_dynamicFormsListNewButton($data) {
	echo '
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addForm">',$data->phrases['dynamic-forms']['newForm'],'</a>
		</div>';
}

function theme_dynamicFormsListNoForms($data) {
	echo '
		<p class="formsListNoForms">',$data->phrases['dynamic-forms']['noFormsExist'],'</p>';
}

function theme_dynamicFormsListTableHead($data) {
	echo '
		<table class="formsList">
			<caption>',$data->phrases['dynamic-forms']['manageFormsHeading'],'</caption>
			<thead>
				<tr>
					<th>',$data->phrases['core']['name'],'</th>
					<th>URL</th>
					<th>',$data->phrases['dynamic-forms']['requireLogin'],'</th>
					<th>',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_dynamicFormsListTableRow($data,$form,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td>', $form['name'], '</td>
				<td>', $form['shortName'], '</td>
				<td>', ($form['requireLogin'] == 1 ? 'Yes' : 'No'), '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/editForm/', $form['id'], '">',$data->phrases['core']['actionEdit'],'</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listFields/', $form['id'], '">',$data->phrases['dynamic-forms']['manageFields'],'</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/viewData/', $form['id'], '">',$data->phrases['dynamic-forms']['viewData'],'</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/sidebars/',$form['id'],'" title="Sidebars">',$data->phrases['core']['sidebars'],'</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/delete/', $form['id'], '">',$data->phrases['core']['actionDelete'],'</a>
				</td>
			</tr>
		';
}

function theme_dynamicFormsListTableFoot() {
	echo '
		</tbody>
	</table>
	';
}

function theme_dynamicFormsListFieldsTableHead($data) {
	echo '
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/'.$data->output['moduleShortName']['dynamicForms'].'/newField/',$data->output['form']['id'],'">
				',$data->phrases['dynamic-forms']['addField'],'
			</a>
		</div>
		<table class="formsList">
			<tr>
				<th>',$data->phrases['core']['name'],'</th>
				<th>',$data->phrases['dynamic-forms']['type'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
			';
}

function theme_dynamicFormsListFieldsTableRow($data,$field,$count) {
	echo '
		<tr class="',($count%2 == 0 ? 'even' : 'odd'),'">
			<td>', $field['name'], '</td>
			<td>', $field['type'], '</td>
			<td class="buttonList">';
	if($field['type'] == 'select'){
		echo '
			<a href="',$data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listOptions/',$field['id'],'">',$data->phrases['dynamic-forms']['options'],'</a>';
	}
	echo'
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/editField/', $field['id'], '">',$data->phrases['core']['actionEdit'],'</a>
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/deleteField/', $field['id'], '">',$data->phrases['core']['actionDelete'],'</a>
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listFields/', $data->output['form']['id'], '/moveUp/', $field['id'], '">&uArr;</a>
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listFields/', $data->output['form']['id'], '/moveDown/', $field['id'], '">&dArr;</a>
		</td>
	</tr>
	';
}

function theme_dynamicFormsListFieldsTableFoot() {
	echo '
		</table>
	';
}

function theme_dynamicFormsListOptionsButtons($data) {
	echo 
	'<div class="panel buttonList">
		<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listFields/'.$data->output['fieldItem']['form'].'" title="Back To Fields">',$data->phrases['dynamic-forms']['returnToFields'],'</a>
		<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addOption/'.$data->output['fieldItem']['id'].'" title="Add An Option">',$data->phrases['dynamic-forms']['addOption'],'</a>
	</div>';
}

function theme_dynamicFormsListOptionsTableHead($data) {
	echo '
		<table class="formsList">
			<tr>
				<th>',$data->phrases['core']['text'],'</th>
				<th>',$data->phrases['core']['value'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
			';
}

function theme_dynamicFormsListOptionsNoOptions($data) {
	echo '
			<tr>
				<td colspan="3">',$data->phrases['dynamic-forms']['noOptionsFound'],'</td>
			</tr>
		</table>';
}

function theme_dynamicFormsListOptionsTableRow($data,$option,$i) {
	echo '
		<tr class="',($i%2 == 0 ? 'even' : 'odd'),'">';
	echo '
			<td>', $option['text'], '</td>
			<td>', $option['value'], '</td>
			<td class="buttonList">
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/editOption/',$option['id'],'/">',$data->phrases['core']['actionEdit'],'</a>
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/deleteOption/',$option['id'],'/">',$data->phrases['core']['actionDelete'],'</a>
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listOptions/',$data->output['fieldItem']['id'],'/moveUp/',$option['id'],'">&uArr;</a>
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listOptions/',$data->output['fieldItem']['id'],'/moveDown/',$option['id'],'">&dArr;</a>
			</td>
		</tr>
	';
}

function theme_dynamicFormsListOptionsTableFoot() {
	echo '
		</table>
	';
}

function theme_dynamicFormsSidebarsTableHead($data) {
	echo '
		<table class="sidebarList">
			<caption>Manage Sidebars on the "',$data->output['formItem']['title'],'" Form</caption>
			<thead>
				<tr>
					<th class="name">',$data->phrases['core']['name'],'</th>
					<th>',$data->phrases['core']['status'],'</th>
					<th>',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_dynamicFormsSidebarsTableRow($data,$sidebar,$action,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td class="name">',$sidebar['name'],'</td>
				<td>', ($sidebar['enabled'] ? $data->phrases['core']['yes'] : $data->phrases['core']['no']), '</td>
				<td class="buttonList">
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/sidebars/', $data->output['formItem']['id'], '/', $action, '/', $sidebar['id'], '">
						', ucfirst($action), '
					</a>
					<a href="',$data->linkRoot,'admin/'.$data->output['moduleShortName']['dynamicForms'].'/sidebars/',$data->output['formItem']['id'],'/moveUp/',$sidebar['id'],'" title="Move Up">&uArr;</a>
					<a href="',$data->linkRoot,'admin/'.$data->output['moduleShortName']['dynamicForms'].'/sidebars/',$data->output['formItem']['id'],'/moveDown/',$sidebar['id'],'" title="Move Down">&dArr;</a>
				</td>
			</tr>';
}

function theme_dynamicFormsSidebarsTableFoot() {
	echo '
			</tbody>
		</table>';
}

function theme_viewdataTableHead() {
	echo '
		<table class="formsList">
			<tr>
		';
}

function theme_viewdataTableHeadCell($field) {
	echo '
			<th>', $field['name'], '</th>
		';
}

function theme_viewdataTableStartRow() {
	echo '
			<tr>
		';
}

function theme_viewdataTableCell($value) {
	echo '
			<td>', $value, '</td>
		';
}

function theme_viewdataTableFoot() {
	echo '
		</table>
	';
}

?>