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
function theme_dynamicURLsListTableHead($linkRoot) {
	echo '
		<div class="panel buttonList">
			<a href="',$linkRoot,'admin/dynamic-urls/add/">
				Add New URL Remap
			</a>
		</div>
		<table class="remapList">
			<caption>URL Remaps</caption>
			<thead>
				<tr>
					<th class="match">Pattern</th>
					<th class="replacement">Replacement</th>
					<th class="hostname">Hostname</th>
					<th class="replacement">Mode</th>
					<th class="buttonList">Controls</th>
				</tr>
			</thead>
			<tbody>
	';
}

function theme_dynamicURLsListTableRow($remap,$linkRoot,$key) {
	if(!$remap['regex']) {
        $remap['match']=str_replace('^','',$remap['match']);
        $remap['match']=str_replace('(/.*)?$','',$remap['match']);
        $remap['replace']=str_replace('\1','',$remap['replace']);
    }
    echo '
		<tr class="', ($key%2==0 ? 'even' : 'odd'),'">
			<td class="match">', $remap['match'], '</td>
			<td class="replacement">', $remap['replace'], '</td>
			<td class="hostname">',($remap['hostname']=='') ? 'Global' : $remap['hostname'],'</td>
            <td class="replacement">', ($remap['regex'] ? 'Regular Expressions':'Standard'), '</td>
			<td class="buttonList">
			    <a href="',$linkRoot,'admin/dynamic-urls/list/moveUp/',$remap['id'],'" title="Move Up">&uArr;</a>
		        <a href="',$linkRoot,'admin/dynamic-urls/list/moveDown/',$remap['id'],'" title="Move Down">&dArr;</a>
				<a href="',$linkRoot,'admin/dynamic-urls/edit/',$remap['id'],'">Modify</a>
				<a href="',$linkRoot,'admin/dynamic-urls/delete/',$remap['id'],'">Delete</a>
			</td>
		</tr>';
}

function theme_dynamicURLsListTableFoot($linkRoot) {
	echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="',$linkRoot,'admin/dynamic-urls/add/">
				Add New URL Remap
			</a>
		</div>
		';
}

function theme_dynamicURLsDeleteSuccess($linkRoot) {
	echo '
			<h2>Removal Successful</h2>
			<p>The remap has been successfully deleted</p>
			<p><a href="',$linkRoot, 'admin/dynamic-urls/list">Return to remap list</a></p>
		';
}

function theme_dynamicURLsDeleteError($exists,$linkRoot) {
	echo '
			<h2>Cannot remove remap</h2>
			<p>The remap cannot be removed. It ',($exists ? 'does' : 'doesn\'t'), ' exist in the database.</p>
			<p><a href="',$linkRoot, 'admin/dynamic-urls/list">Return to remap list</a></p>
		';
}

function theme_dynamicURLsDeleteConfirm($action3,$linkRoot) {
	echo '
		<h2>Confirm Removal</h2>
		<p>Are you sure that you want to remove this remap?</p>
		<div class="buttonList">
			<a href="',$linkRoot, 'admin/dynamic-urls/delete/',$action3, '/confirm">Yes, Confirm Delete</a>
			<a href="',$linkRoot, 'admin/dynamic-urls/list">No, Cancel Delete</a>
		</div>
		';
}

?>