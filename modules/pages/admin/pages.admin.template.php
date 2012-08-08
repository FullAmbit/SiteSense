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
function theme_pagesDeleteDeteled($data) {
	echo '
		<h2>',$data->phrases['pages']['pageDeleteSuccessHeading'],'</h2>
		<p>
			',$data->phrases['pages']['pageDeleteSuccessMessage'],': ',$data->output['deleteCount'],'
		</p>
		<div class="buttonList">
			<a href="',$data->linkRoot,'admin/pages/list">',$data->phrases['pages']['returnToPages'],'</a>
		</div>
		';
}

function theme_pagesDeleteCancelled($data) {
	echo '
		<h2>Deletion Cancelled</h2>
		<p>
			',$data->phrases['core']['messageRedirect'],'
			<a href="',$data->linkRoot,'admin/pages/list">',$data->phrases['core']['linkSkipWait'],'</a>
		</p>';
}

function theme_pagesDeleteDefault($data) {
	echo '
		<form action="',$data->linkRoot,'admin/pages/delete/',$data->action[3],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>',$data->phrases['pages']['pageDeleteConfirmHeading'],'</span></legend>
				<p class="warning">',$data->phrases['pages']['pageDeleteConfirmMessage'],'</p>
				<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
				<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
				<input type="hidden" name="fromForm" value="',$data->action[3],'" />
			</fieldset>
		</form>';
}

function theme_pagesListHead($data) {
	echo '
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/pages/add/">
				',$data->phrases['pages']['addNewPage'],'
			</a>
		</div>';
}

function theme_pagesListNoPages($data) {
	echo '
		<p class="pageListNoPages">',$data->phrases['pages']['noPagesExist'],'</p>';
}

function theme_pagesListTableHead($data) {
	echo '
		<table class="pagesList">
			<caption>',$data->phrases['pages']['managePagesHeading'],'</caption>
			<thead>
				<tr>
					<th class="name">',$data->phrases['core']['name'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_pagesListTableRow($data,$item) {
	echo '
        <tr class="level',$item['level'],'">
          <td class="name">
            <a href="',$data->linkRoot,'admin/pages/edit/',$item['id'],'">',$item['name'],'</a>
          </td>
          <td class="buttonList">
            <a href="',$data->linkRoot,'admin/pages/list/moveUp/',$item['id'],'" title="Move Up">&uArr;</a>
            <a href="',$data->linkRoot,'admin/pages/list/moveDown/',$item['id'],'" title="Move Down">&dArr;</a>
            <a href="',$data->linkRoot,'admin/pages/sidebars/',$item['id'],'">',$data->phrases['core']['sidebars'],'</a>
            <a href="',$data->linkRoot,'admin/pages/add/childOf/',$item['id'],'">',$data->phrases['pages']['addChild'],'</a>
            <a href="',$data->linkRoot,'admin/pages/delete/',$item['id'],'">',$data->phrases['core']['actionDelete'],'</a>
          </td>
        </tr>';
}

function theme_pagesListTableFoot() {
	echo '
			</tbody>
		</table>';
}

function theme_pagesListFoot($data) {
	echo '
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/pages/add/">
				',$data->phrases['pages']['addNewPage'],'
			</a>
		</div>';
}

function theme_sidebarsTableHead($data) {
	echo '
		<table class="sidebarList">
			<caption>',$data->phrases['pages']['manageSidebarsHeading'],'</caption>
			<thead>
				<tr>
					<th class="name">',$data->phrases['core']['name'],'</th>
					<th>',$data->phrases['core']['status'],'</th>
					<th>',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_sidebarsTableList($data,$sidebar,$count,$action) {
	echo '
		<tr class="',($count%2==0 ? 'odd' : 'even'),'">
			<td class="name">',$sidebar['name'],'</td>
			<td>', ($sidebar['enabled'] ? $data->phrases['core']['yes'] : $data->phrases['core']['no']), '</td>
			<td class="buttonList">
				<a href="', $data->linkRoot, 'admin/pages/sidebars/', $data->output['pageItem']['id'], '/', $action, '/', $sidebar['id'], '">
					', ($action=='enable') ? $data->phrases['core']['enable'] : $data->phrases['core']['disable'], '
				</a>
				<a href="',$data->linkRoot,'admin/pages/sidebars/',$data->output['pageItem']['id'],'/moveUp/',$sidebar['id'],'" title="Move Up">&uArr;</a>
				<a href="',$data->linkRoot,'admin/pages/sidebars/',$data->output['pageItem']['id'],'/moveDown/',$sidebar['id'],'" title="Move Down">&dArr;</a>
			</td>
		</tr>';
}

function theme_sidebarsTableFoot() {
	echo '
			</tbody>
		</table>';
}

?>