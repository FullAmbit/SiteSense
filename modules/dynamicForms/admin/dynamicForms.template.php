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
		<h2>',$data->output['rejectText'],'</h2>
		<div class="buttonList">
			<a href="'.$aRoot.'" title="Return To Forms">Return to Forms</a>
		</div>';
}

function theme_dynamicFormsDeleteCancelled($aRoot) {
	echo '
		<h2>Deletion Cancelled</h2>
		<p>
			You should be auto redirected to the forms list in three seconds.
			<a href="',$aRoot,'list">Click Here if you do not wish to wait.</a>
		</p>';
}

function theme_dynamicFormsDeleteDeleted($aRoot) {
	echo '
		This form and all data associated with it has been deleted.
		<div class="buttonList">
			<a href="'.$aRoot.'" title="Return To Forms">Return to Forms.</a>
		</div>';
}

function theme_dynamicFormsDeleteDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'delete/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>Are you sure you want to delete this form?</legend>
			</fieldset>
			<input type="submit" name="delete" value="Yes, Delete it" />
			<input type="submit" name="cancel" value="Cancel" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_dynamicFormsDeleteFieldCancelled($data,$aRoot) {
	echo 'This field has been deleted. <div class="buttonList"><a href="'.$aRoot.'listfields/'.$data->output['formItem']['id'].'" title="Return To Field List">Return to field list.</a></div>';
}

function theme_dynamicFormsDeleteFieldDeleted($aRoot) {
	echo '<h2>Deletion Cancelled</h2><p>You should be auto redirected to the forms list in three seconds. <a href="',$aRoot,'list">Click Here if you do not wish to wait.</a></p>';
}

function theme_dynamicFormsDeleteFieldDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteField/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>Are you sure you want to delete this field?</legend>
			</fieldset>
			<input type="submit" name="delete" value="Yes, Delete it" />
			<input type="submit" name="cancel" value="Cancel" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_dynamicFormsDeleteOptionCancelled($data,$aRoot) {
	echo 'This option has been deleted. <div class="buttonList"><a href="'.$aRoot.'listoptions/'.$data->output['fieldItem']['id'].'" title="Return To Options">Return Top Options.</a></div>';
}

function theme_dynamicFormsDeleteOptionDeleted($aRoot) {
	echo '<h2>Deletion Cancelled</h2><p>You should be auto redirected to the forms list in three seconds. <a href="',$aRoot,'list">Click Here if you do not wish to wait.</a></p>';
}

function theme_dynamicFormsDeleteOptionDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteoption/'.$data->action[3].'/'.$data->action[4].'" method="post" class="verifyForm">
			<fieldset>
				<legend>Are you sure you want to delete this field?</legend>
			</fieldset>
			<input type="submit" name="delete" value="Yes, Delete it" />
			<input type="submit" name="cancel" value="Cancel" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_dynamicFormsListNewButton($data) {
	echo '
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addform">New Form</a>
		</div>';
}

function theme_dynamicFormsListNoForms() {
	echo '
		<p class="formsListNoForms">No forms exist</p>';
}

function theme_dynamicFormsListTableHead() {
	echo '
		<table class="formsList">
			<caption>Manage Forms</caption>
			<thead>
				<tr>
					<th>Table Name</th>
					<th>URL</th>
					<th>Require Login?</th>
					<th>Controls</th>
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
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/editform/', $form['id'], '">Edit Settings</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listfields/', $form['id'], '">Manage Fields</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/viewdata/', $form['id'], '">View Data</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/sidebars/',$form['id'],'" title="Sidebars">Sidebars</a>
					<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/delete/', $form['id'], '">Delete</a>
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
			<a href="',$data->linkRoot,'admin/'.$data->output['moduleShortName']['dynamicForms'].'/newfield/',$data->output['form']['id'],'">
				New Field
			</a>
		</div>
		<table class="formsList">
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Controls</th>
			</tr>
			';
}

function theme_dynamicFormsListFieldsTableRow($data,$field,$count) {
	echo '
		<tr class="',($count%2 == 0 ? 'even' : 'odd'),'">
			<td>', $field['name'], '</td>
			<td>', $field['type'], '</td>
			<td class="buttonList">';
	if($field['type'] == 'select')
	{
		echo '
			<a href="',$data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listoptions/',$field['id'],'">Options</a>';
	}
	echo'
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/editfield/', $field['id'], '">Edit</a>
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/deleteField/', $field['id'], '">Delete</a>
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listfields/', $data->output['form']['id'], '/moveUp/', $field['id'], '">&uArr;</a>
			<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listfields/', $data->output['form']['id'], '/moveDown/', $field['id'], '">&dArr;</a>
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
		<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listfields/'.$data->output['fieldItem']['form'].'" title="Back To Fields">Back To Fields</a>
		<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addoption/'.$data->output['fieldItem']['id'].'" title="Add An Option">Add An Option</a>
	</div>';
}

function theme_dynamicFormsListOptionsTableHead() {
	echo '
		<table class="formsList">
			<tr>
				<th>Text</th>
				<th>Value</th>
				<th>Controls</th>
			</tr>
			';
}

function theme_dynamicFormsListOptionsNoOptions() {
	echo '
			<tr>
				<td colspan="3">No options found</td>
			</tr>
		</table>';
}

function theme_dynamicFormsListOptionsTableRow($data,$option,$optionIndex,$i) {
	echo '
		<tr class="',($i%2 == 0 ? 'even' : 'odd'),'">';
	echo '
			<td>', $option['text'], '</td>
			<td>', $option['value'], '</td>
			<td class="buttonList">
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/editoption/',$data->output['fieldItem']['id'],'/">Edit</a>
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/deleteoption/',$data->output['fieldItem']['id'],'/">Delete</a>
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listoptions/',$data->output['fieldItem']['id'],'/moveUp/',$optionIndex,'">&uArr;</a>
				<a href="', $data->linkRoot, 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listoptions/',$data->output['fieldItem']['id'],'/moveDown/',$optionIndex,'">&dArr;</a>
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
					<th class="name">Name</th>
					<th>Status</th>
					<th>Controls</th>
				</tr>
			</thead><tbody>';
}

function theme_dynamicFormsSidebarsTableRow($data,$sidebar,$action,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td class="name">',$sidebar['name'],'</td>
				<td>', ($sidebar['enabled'] ? 'Yes' : 'No'), '</td>
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