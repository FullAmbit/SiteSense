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
function theme_urlsListTableHead($data) {
	echo '
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/urls/add/">
				',$data->phrases['urls']['addRemap'],'
			</a>
		</div>
		<table class="remapList">
			<caption>',$data->phrases['urls']['manageURLsHeading'],'</caption>
			<thead>
				<tr>
					<th class="match">',$data->phrases['urls']['pattern'],'</th>
					<th class="replacement">',$data->phrases['urls']['replacement'],'</th>
					<th class="type">',$data->phrases['urls']['type'],'</th>
					<th class="hostname">',$data->phrases['urls']['hostname'],'</th>
					<th class="replacement">',$data->phrases['urls']['mode'],'</th>
					<th class="buttonList">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead>
			<tbody>
	';
}

function theme_urlsListTableRow($remap,$linkRoot,$key) {
	if(!$remap['regex']) {
        $remap['match']=str_replace('^','',$remap['match']);
        $remap['match']=str_replace('(/.*)?$','',$remap['match']);
        $remap['replace']=str_replace('\1','',$remap['replace']);
    }
    echo '
		<tr class="', ($key%2==0 ? 'even' : 'odd'),'">
			<td class="match">', $remap['match'], '</td>
			<td class="replacement">', $remap['replace'], '</td>
			<td class="type">',($remap['isRedirect']=='0') ? "Remap" : "Redirect",'</td>
			<td class="hostname">',($remap['hostname']=='') ? 'Global' : $remap['hostname'],'</td>
            <td class="replacement">', ($remap['regex'] ? 'Regular Expressions':'Standard'), '</td>
			<td class="buttonList">
			    <a href="',$linkRoot,'admin/urls/list/moveUp/',$remap['id'],'" title="Move Up">&uArr;</a>
		        <a href="',$linkRoot,'admin/urls/list/moveDown/',$remap['id'],'" title="Move Down">&dArr;</a>
				<a href="',$linkRoot,'admin/urls/edit/',$remap['id'],'">Modify</a>
				<a href="',$linkRoot,'admin/urls/delete/',$remap['id'],'">Delete</a>
			</td>
		</tr>';
}

function theme_urlsListTableFoot($data) {
	echo '
			</tbody>
		</table>
		<div class="panel buttonList">
			<a href="',$data->linkRoot,'admin/urls/add/">
				',$data->phrases['urls']['addRemap'],'
			</a>
		</div>
		';
}

function theme_urlsDeleteSuccess($data) {
	echo '
			<h2>',$data->phrases['urls']['deleteURLSuccessHeading'],'</h2>
			<p>',$data->phrases['urls']['deleteURLSuccessMessage'],'</p>
			<p><a href="',$data->linkRoot, 'admin/urls/list">',$data->phrases['urls']['returnToList'],'</a></p>
		';
}

function theme_urlsDeleteError($data) {
	echo '
			<h2>',$data->phrases['urls']['deleteURLErrorHeading'],'</h2>
			<p>',($data->output['exists']) ? $data->phrases['deleteURLErrorMessageDoesNotExist'] : $data->phrases['deleteURLErrorMessageDoesExist'],'</p>
			<p><a href="',$data->linkRoot, 'admin/urls/list">',$data->phrases['urls']['returnToList'],'</a></p>
		';
}

function theme_urlsDeleteConfirm($data) {
	echo '
		<h2>',$data->phrases['urls']['deleteURLConfirmHeading'],'</h2>
		<p>',$data->phrases['urls']['deleteURLConfirmMessage'],'</p>
		<div class="buttonList">
			<a href="',$data->linkRoot, 'admin/urls/delete/',$data->action[3], '/confirm">',$data->phrases['core']['actionConfirmDelete'],'</a>
			<a href="',$data->linkRoot, 'admin/urls/list">',$data->phrases['core']['actionCancelDelete'],'</a>
		</div>
		';
}

?>