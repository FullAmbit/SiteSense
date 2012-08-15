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
function theme_menuDeleteDeleted($data,$aRoot) {
	echo $data->phrases['main-menu']['deleteItemSuccessHeading'].'<div class="buttonList"><a href="'.$aRoot.'" title="'.$data->phrases['main-menu']['returnToMenuItems'].'">'.$data->phrases['main-menu']['returnToMenuItems'].'</a></div>';
}

function theme_menuDeleteCancelled($data,$aRoot) {
	echo '<h2>',$data->phrases['main-menu']['deleteItemCancelledHeading'],'</h2><p>',$data->phrases['core']['messageRedirect'],'<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a></p>';
}

function theme_menuDeleteDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'delete/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>'.$data->phrases['main-menu']['deleteItemConfirmHeading'].'<br /><b>'.$data->phrases['main-menu']['warning'].'</b>&nbsp;'.$data->phrases['main-menu']['deleteItemConfirmMessage'].'</legend>
			</fieldset>
			<input type="submit" name="delete" value="'.$data->phrases['core']['actionConfirmDelete'].'" />
			<input type="submit" name="cancel" value="'.$data->phrases['core']['actionCancelDelete'].'" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_menuItemRow($level,$data,$item) {
	if($item['enabled'] == '0'){
		$enable = '<a href="'.$data->linkRoot.'admin/main-menu/enable/'.$item['id'].'/" title="'.$data->phrases['main-menu']['enable'].'">'.$data->phrases['main-menu']['enable'].'</a>';
	} else {
		$enable = '<a href="'.$data->linkRoot.'admin/main-menu/disable/'.$item['id'].'/" title="'.$data->phrases['main-menu']['disable'].'">'.$data->phrases['main-menu']['disable'].'</a>';
	}
		
	echo '<tr class="level',$level,'">
				<td class="text"><a href="',$data->linkRoot,'admin/main-menu/edit/',$item['id'],'">',$item['text'],'</a></td>
				<td class="side">',(($item['parent'] == '0') ? (($item['side']=='left') ? $data->phrases['main-menu']['left'] : $data->phrases['main-menu']['right']) : ''),'</td>
				<td class="buttonList">
					',$enable,'
					
					<a href="',$data->linkRoot,'admin/main-menu/delete/',$item['id'],'">',$data->phrases['core']['actionDelete'],'</a>',
					(($item['parent'] == '0') ? '
					<a href="'.$data->linkRoot.'admin/main-menu/list/switch/'.$item['id'].'"
						title="'.$data->phrases['main-menu']['linkSwitchSideTitle'].'"
					>'.$data->phrases['main-menu']['switchSide'].'</a>' : ''),'
					<a href="',$data->linkRoot,'admin/main-menu/list/moveUp/',$item['id'],'" title="Move Up">&uArr;</a>
					<a href="',$data->linkRoot,'admin/main-menu/list/moveDown/',$item['id'],'" title="Move Down">&dArr;</a>
				</td>
			</tr>';
}

function theme_menuShowHead($data) {
	echo '<div class="panel buttonList"><a href="'.$data->linkRoot.'admin/main-menu/add/" title="'.$data->phrases['main-menu']['addMenuItem'].'">'.$data->phrases['main-menu']['addMenuItem'].'</a></div>
			<table class="mainMenuList">
				<caption>'.$data->phrases['main-menu']['manageItemsHeading'].'</caption>
				<thead>
					<tr>
						<th class="text">'.$data->phrases['main-menu']['text'].'</th>
						<th class="side">'.$data->phrases['main-menu']['side'].'</th>
						<th class="buttonList">'.$data->phrases['core']['controls'].'</th>
					</tr>
				</thead><tbody>';
}

function theme_menuShowFoot() {
	echo '
			</tbody>
		</table>';
}

?>