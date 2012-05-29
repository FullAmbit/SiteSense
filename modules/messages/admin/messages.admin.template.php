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
function theme_messagesDeleteSuccess($data) {
	echo '
			<h2>Removal Successful</h2>
			<p>The Message has been successfully deleted</p>
			<p><a href="', $data->linkRoot, 'admin/messages/list">Return to message list</a></p>
		';
}

function theme_messagesDeleteFailure($data) {
	echo '
			<h2>Cannot remove message</h2>
			<p>The message cannot be removed. It ', ($data->output['exists'] ? 'does' : 'doesn\'t'), ' exist in the database.</p>
			<p><a href="', $data->linkRoot, 'admin/messages/list">Return to message list</a></p>
		';
}

function theme_messagesDeleteConfirm($data) {
	echo '
			<h2>Confirm Removal</h2>
			<p>Are you sure that you want to remove this message?</p>
			<ul>
				<li><a href="', $data->linkRoot, 'admin/messages/delete/', $data->action[3], '/confirm">Yes</a></li>
				<li><a href="', $data->linkRoot, 'admin/messages/list">No</a></li>
			</ul>
		';
}

function theme_messagesViewShow($message,$data) {
	echo '
	<table class="adminMessageTable">
		<tr>
			<th>From</th>
			<td>', $message['from_name'], '</td>
		</tr>
		<tr class="odd">
			<th>To</th>
			<td>', $message['to_name'], '</td>
		</tr>
			<th>Sent</th>
			<td>', $message['sent'], '</td>
		</tr>
		<tr class="odd">
			<th>Message</th>
			<td>', htmlspecialchars($message['message']), '</td>
		</tr>
		<tr>
			<th>Controls</th>
			<td class="buttonList">
				<a href="'.$data->linkRoot.'admin/messages/delete/'.$message['id'].'">Delete</a>
			</td>
		</tr>
	</table>
	';
}

function theme_messageListShow($data) {
	echo '
		<table class="messageList">
			<caption>
				Messages
				',$data->output['messageListStart']+1,' through
				',$data->output['messageListStart']+count($data->output['messageList']),'
			</caption>
			<thead>
				<tr>
					<th class="from">From</th>
					<th class="to">To</th>
					<th class="message">Message</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
}

function theme_messageListRow($message,$data,$key) {
	echo '
		<tr class="', ($message['deleted'] == 1) ? ('deleted') : ($key%2==0 ? 'even' : 'odd'),'">
			<td class="from"><a href="', $data->linkRoot, 'admin/users/edit/', $message['from'], '">',$message['from_name'],'</a></td>
			<td class="to"><a href="', $data->linkRoot, 'admin/users/edit/', $message['from'], '">',$message['to_name'],'</a></td>
			<td class="message"><a href="', $data->linkRoot, 'admin/messages/view/', $message['id'], '">', substr(strip_tags($message['message']), 0, 50), '...</a></td>
			<td class="buttonList">
				',(
					($message['deleted'] == 1)
						?
							'deleted'
						:
							'
				<a href="'.$data->linkRoot.'admin/messages/delete/'.$message['id'].'">Delete</a>
							'
				),'
			</td>
		</tr>';
}

function theme_messageListFoot() {
	echo '
			</tbody>
		</table>
		';
}

?>