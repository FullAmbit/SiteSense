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
function theme_sideBarsDeleteDeleted($data,$aRoot) {
	echo '
			<h2>Entry #',$data->action[3],' Deleted</h2>
			<p>
				This action deleted a total of ',$data->output['deleteCount'],' sidebar items!
			</p>
			<div class="buttonList">
				<a href="',$aRoot,'list">Return to List</a>
			</div>
			';
}

function theme_sideBarsDeleteCancelled($aRoot) {
	echo '
			<h2>Deletion Cancelled</h2>
			<p>
				You should be auto redirected to the page list in three seconds.
				<a href="',$aRoot,'list">Click Here if you do not wish to wait.</a>
			</p>';
}

function theme_sideBarsDeleteDefault($data,$aRoot) {
	echo '
			<form action="',$aRoot,'delete/',$data->action[3],'" method="post" class="verifyForm">
				<fieldset>
					<legend><span>Are you sure you want to delete sidebar #',$data->action[3],'?</span></legend>
					<input type="submit" name="delete" value="Yes, Delete it" />
					<input type="submit" name="cancel" value="Cancel" />
					<input type="hidden" name="fromForm" value="',$data->action[3],'" />
				</fieldset>
			</form>';
}

function theme_sideBarsListAddNewButton($aRoot) {
	echo '
			<div class="panel buttonList">
				<a href="',$aRoot,'add">
					Add New Sidebar
				</a>
			</div>';
}

function theme_sideBarsListNoSidebars() {
	echo '
			<p class="sidebarListNoSidebars">No sidebars exist</p>';
}

function theme_sideBarsListTableHead() {
	echo '
			<table class="sidebarList">
				<caption>Manage Sidebars</caption>
				<thead>
					<tr>
						<th class="name">Sidebar Title</th>
						<th>Side</td>
						<th>Controls</th>
					</tr>
				</thead><tbody>';
}

function theme_sideBarsListTableRow($item,$aRoot,$titleStartTag,$titleEndTag,$count) {
	echo '
			<tr class="',($count%2==0 ? 'odd' : 'even'),'">
				<td class="name">
					',$titleStartTag,$item['name'],$titleEndTag,'
				</td>
				<td>',$item['side'],'</td>
				<td class="buttonList">
					',(
						$item['fromFile'] ?
						'' :
						'<a href="'.$aRoot.'delete/'.$item['id'].'">Delete</a> '
					),'
					<a href="',$aRoot,'list/switch/',$item['id'],'" title="Switch Side">Switch Side</a>
				</td>
			</tr>';
}

function theme_sideBarsListTableFoot() {
	echo '
				</tbody>
			</table>';
}

?>