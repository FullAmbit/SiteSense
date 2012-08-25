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
function theme_welcomeMessage($data,$notification) {
	echo '
		<div class="aboutBox">
			',$notification,'
			<h2>',$data->phrases['dashboard']['welcomeMessageHeading'],'</h2>
			<p>
				',$data->phrases['dashboard']['welcomeMessage'],'
			</p>
		</div>
		';
}
function theme_dashboardUpdateList($data) {
	echo '<table class="modulesList" style="width:70%;">
			<caption>',$data->phrases['dashboard']['updatesAvailable'],'</caption>
			<thead>
				<tr>
					<th class="name">',$data->phrases['dashboard']['module'],'</th>
					<th>',$data->phrases['dashboard']['oldVersion'],'</th>
					<th>',$data->phrases['dashboard']['newVersion'],'</th>
					<th>',$data->phrases['dashboard']['updateNow'],'</th>
				</tr>
			</thead><tbody>
	';
}
function theme_dashboardUpdateListRow($data,$moduleUpdate) {
	echo '<tr>
			<td class="name">',$moduleUpdate['name'],' (',$moduleUpdate['shortName'],')</td>
			<td>',$moduleUpdate['oldVersion'],'</td>
			<td>',$moduleUpdate['newVersion'],'</td>
			<td class="buttonList"><a href="',$data->linkRoot,'admin/modules/upgrade/',$moduleUpdate['shortName'],'">',$data->phrases['dashboard']['updateNow'],'</a></td>
		</tr>';
}
function theme_dashboardUpdateListFoot() {
	echo '</tbody>
		</table>
	';
}
function theme_dashboardFoot() {
}
?>