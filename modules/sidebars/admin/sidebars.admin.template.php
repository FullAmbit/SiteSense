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
function theme_sidebarsDeleteDeleted($data,$aRoot) {
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

function theme_sidebarsDeleteCancelled($data,$aRoot) {
	echo '
			<h2>Deletion Cancelled</h2>
			<p>
				',$data->phrases['sidebars']['redirectedInThreeSeconds'],'
				<a href="',$aRoot,'list">',$data->phrases['sidebars']['clickToWait'],'</a>
			</p>';
}

function theme_sidebarsDeleteDefault($data,$aRoot) {
	echo '
			<form action="',$aRoot,'delete/',$data->action[3],'" method="post" class="verifyForm">
				<fieldset>
					<legend><span>',$data->phrases['sidebars']['deleteSidebarSure'],$data->action[3],'?</span></legend>
					<input type="submit" name="delete" value="',$data->phrases['sidebars']['yesToDelete'],'" />
					<input type="submit" name="cancel" value="',$data->phrases['sidebars']['delete'],'" />
					<input type="hidden" name="fromForm" value="',$data->action[3],'" />
				</fieldset>
			</form>';
}

function theme_sidebarsListAddNewButton($data,$aRoot) {
	echo '
			<div class="panel buttonList">
				<a href="',$aRoot,'add">
					',$data->phrases['sidebars']['addNewSidebar'],'
				</a>
			</div>';
}

function theme_sidebarsListNoSidebars($data) {
	echo '
			<p class="sidebarListNoSidebars">',$data->phrases['sidebars']['noSidebars'],'</p>';
}

function theme_sidebarsListTableHead($data) {
	echo '
			<table class="sidebarList">
				<caption>',$data->phrases['sidebars']['manageSidebars'],'</caption>
				<thead>
					<tr>
						<th class="name">',$data->phrases['sidebars']['sidebarTitle'],'</th>
						<th>',$data->phrases['sidebars']['side'],'</td>
						<th>',$data->phrases['sidebars']['controls'],'</th>
					</tr>
				</thead><tbody>';
}

function theme_sidebarsListTableRow($data) {
	echo '
			<tr class="',($data->output['count']%2==0 ? 'odd' : 'even'),'">
				<td class="name">
					',$data->output['titleStartTag'],$data->output['item']['name'],$data->output['titleEndTag'],'
				</td>
				<td>',$data->output['item']['side'],'</td>
				<td class="buttonList">
				    <a href="',$data->output['aRoot'],'list/moveUp/',$data->output['item']['id'],'" title="',$data->phrases['sidebars']['moveUp'],'">&uArr;</a>
					<a href="',$data->output['aRoot'],'list/moveDown/',$data->output['item']['id'],'" title="',$data->phrases['sidebars']['moveDown'],'">&dArr;</a>
					',(
						$data->output['item']['fromFile'] ?
						'' :
						'<a href="'.$data->output['aRoot'].'delete/'.$data->output['item']['id'].'">'.$data->phrases['sidebars']['delete'].'</a> '
					),'
					<a href="',$data->output['aRoot'],'list/switch/',$data->output['item']['id'],'" title="'.$data->phrases['sidebars']['switchSide'].'">'.$data->phrases['sidebars']['switchSide'].'</a>
				</td>
			</tr>';
}

function theme_sidebarsListTableFoot() {
	echo '
				</tbody>
			</table>';
}

?>