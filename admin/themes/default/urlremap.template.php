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
function theme_urlremapListTableHead($linkRoot) {
	echo '
		<div class="panel buttonList">
			<a href="',$linkRoot,'admin/urlremap/add/">
				Add New URL Remap
			</a>
		</div>
		<table class="remapList">
			<caption>URL Remaps</caption>
			<thead>
				<tr>
					<th class="match">Pattern</th>
					<th class="replacement">Replacement</th>
					<th class="buttonList">Controls</th>
				</tr>
			</thead>
			<tbody>
	';
}

function theme_urlremapListTableRow($remap,$linkRoot,$key) {
	echo '
		<tr class="', ($key%2==0 ? 'even' : 'odd'),'">
			<td class="match">', $remap['match'], '</td>
			<td class="replacement">', $remap['replace'], '</td>
			<td class="buttonList">
				<a href="'.$linkRoot.'admin/urlremap/edit/'.$remap['id'].'">Modify</a>
				<a href="'.$linkRoot.'admin/urlremap/delete/'.$remap['id'].'">Delete</a>
			</td>
		</tr>';
}

function theme_urlremapListTableFoot($linkRoot) {
	echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="',$linkRoot,'admin/urlremap/add/">
				Add New URL Remap
			</a>
		</div>
		';
}

function theme_urlremapDeleteSuccess($linkRoot) {
	echo '
			<h2>Removal Successful</h2>
			<p>The remap has been successfully deleted</p>
			<p><a href="',$linkRoot, 'admin/urlremap/list">Return to remap list</a></p>
		';
}

function theme_urlremapDeleteError($exists,$linkRoot) {
	echo '
			<h2>Cannot remove remap</h2>
			<p>The remap cannot be removed. It ',($exists ? 'does' : 'doesn\'t'), ' exist in the database.</p>
			<p><a href="',$linkRoot, 'admin/urlremap/list">Return to remap list</a></p>
		';
}

function theme_urlremapDeleteConfirm($action3,$linkRoot) {
	echo '
			<h2>Confirm Removal</h2>
			<p>Are you sure that you want to remove this remap?</p>
			<ul>
				<li><a href="',$linkRoot, 'admin/urlremap/delete/',$action3, '/confirm">Yes</a></li>
				<li><a href="',$linkRoot, 'admin/urlremap/list">No</a></li>
			</ul>
		';
}

?>