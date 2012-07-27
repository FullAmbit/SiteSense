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
function theme_menuDeleteDeleted($aRoot) {
	echo 'This menu item has been deleted. <div class="buttonList"><a href="'.$aRoot.'" title="Return To Menu Items">Return to Menu Items.</a></div>';
}

function theme_menuDeleteCancelled($aRoot) {
	echo '<h2>Deletion Cancelled</h2><p>You should be auto redirected to the menu list in three seconds. <a href="',$aRoot,'list">Click Here if you do not wish to wait.</a></p>';
}

function theme_menuDeleteDefault($aRoot,$data) {
	echo '
		<form action="'.$aRoot.'delete/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>Are you sure you want to delete this menu item?<br /><b>Warning: </b>All menu-children will also be deleted.</legend>
			</fieldset>
			<input type="submit" name="delete" value="Yes, Delete it" />
			<input type="submit" name="cancel" value="Cancel" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_menuItemRow($level,$data,$item,$enable) {
	echo '<tr class="level',$level,'">
				<td class="text"><a href="',$data->linkRoot,'admin/main-menu/edit/',$item['id'],'">',$item['text'],'</a></td>
				<td class="side">',(($item['parent'] == '0') ? $item['side'] : ''),'</td>
				<td class="buttonList">
					',$enable,'
					
					<a href="',$data->linkRoot,'admin/main-menu/delete/',$item['id'],'">Delete</a>',
					(($item['parent'] == '0') ? '
					<a href="'.$data->linkRoot.'admin/main-menu/list/switch/'.$item['id'].'"
						title="Switch side of menu this item is shown on"
					>Switch Side</a>' : ''),'
					<a href="',$data->linkRoot,'admin/main-menu/list/moveUp/',$item['id'],'" title="Move Up">&uArr;</a>
					<a href="',$data->linkRoot,'admin/main-menu/list/moveDown/',$item['id'],'" title="Move Down">&dArr;</a>
				</td>
			</tr>';
}

function theme_menuShowHead($data) {
	echo '<div class="panel buttonList"><a href="'.$data->linkRoot.'admin/main-menu/add/" title="Add A Menu Item">Add A Menu Item</a></div>
			<table class="mainMenuList">
				<caption>Manage Main Menu</caption>
				<thead>
					<tr>
						<th class="text">Text</th>
						<th class="side">Side</th>
						<th class="buttonList">Controls</th>
					</tr>
				</thead><tbody>';
}

function theme_menuShowFoot() {
	echo '
			</tbody>
		</table>';
}

?>