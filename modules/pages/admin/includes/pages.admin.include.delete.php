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
function admin_pagesBuild($data, $db) {
	//permission check for pages delete
	if (!checkPermission('delete', 'pages', $data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
		return;
	}

	$data->output['delete']='';

	// Check To See If The Menu Item Exists
	$check = $db->prepare('getPageById', 'admin_pages');
	$check->execute(array(':id' => $data->action[3]));
	$data->output['pageItem'] = $check->fetch();

	if ($data->output['pageItem']['shortName'] == $data->settings['homepage']) {
		$data->output['rejectError']='Permission Denied';
		$data->output['rejectText']='This page is being used as the homepage. It cannot be deleted.';
	}

	if ($data->output['pageItem'] == FALSE || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
	} else {
		if (isset($_POST['fromForm'])) {
			if ($_POST['fromForm']==$data->action[3] && !empty($_POST['delete'])) {
				if ($data->output['pageItem']['parent']==0) {
					$statement=$db->prepare('deleteReplacementByMatch', 'admin_urls');
					$statement->execute(array(
							':match' => '^'.$data->output['pageItem']['shortName'].'(/.*)?$'
						));
				}
				// Fix Gap In Sort Order By Subtracting 1 From Each One Larger Than It
				$statement = $db->prepare('fixSortOrderGap', 'admin_pages');
				$statement->execute(array(
						':sortOrder' => $data->output['pageItem']['sortOrder'],
						':parent' => $data->output['pageItem']['parent']
					));
				//--Delete This Page--//
				common_deleteFromLanguageTables($data, $db, 'pages', 'id', $data->action[3], TRUE);
				//--Delete This Page's Children--//
				common_deleteFromLanguageTables($data, $db, 'pages', 'parent', $data->action[3], TRUE);
				$data->output['delete']='deleted';
			} else {
				/* from form plus not deleted must == cancelled. */
				$data->output['delete']='cancelled';
			}
		}
	}
}
function admin_pagesShow($data) {
	if (empty($data->output['rejectError'])) {
		switch ($data->output['delete']) {
		case 'deleted':
			theme_pagesDeleteDeteled($data);
			break;
		case 'cancelled':
			theme_pagesDeleteCancelled($data);
			break;
		default:
			theme_pagesDeleteDefault($data);
			break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>