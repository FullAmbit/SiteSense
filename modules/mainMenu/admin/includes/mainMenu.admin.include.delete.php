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
function admin_mainMenuBuild($data,$db) {
	$data->output["delete"] = "";
	// Make Sure We Have An ID
	if(empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
		return;
	}
	// Check for User Permissions
	if (!checkPermission('delete','mainMenu',$data))
	{
		$data->output['rejectError']='Insufficient User Permissions';
		$data->output['rejectText']='You do not have sufficient access to perform this action.';
		return;
	}
	// Check To See If The Menu Item Exists
	$check = $db->prepare('getMenuItemById','admin_mainMenu');
	$check->execute(array(':id' => $data->action[3]));
	if(($data->output['menuItem'] = $check->fetch()) === FALSE) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>The ID does not exist in database</h2>';
		return;
	}
	// Delete Menu Item
	if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3]) {
		if(!empty($_POST['delete'])) {
			// Delete All Children //
			deleteChildren($db,$data->output['menuItem']);
			/*=========================================================================*/
			// Delete Across All Languages
			common_deleteFromLanguageTables($data,$db,'main_menu','id',$data->action[3],TRUE);
			$data->output['delete']='deleted';
		} else {
			$data->output['delete']='cancelled';
		}
	}
}

function deleteChildren($db,$item)
{
	// First Retrieve The Children
	$statement = $db->prepare('getMenuItemByParent','admin_mainMenu');
	$statement->execute(array(':parent' => $item['id']));
	$children = $statement->fetchAll();
	foreach($children as $child)
	{
		deleteChildren($db,$child);
	}
	// Delete All With This Parent ID
	if($item['parent'] !== '0')
	{
		common_deleteFromLanguageTables($data,$db,'main_menu','parent',$item['parent'],TRUE);
	}
}

function admin_mainMenuShow($data)
{
	$aRoot=$data->linkRoot.'admin/main-menu/';
	if(empty($data->output['rejectError']))
	{
		switch($data->output['delete'])
		{
			case 'deleted':
				theme_menuDeleteDeleted($aRoot);
				break;
			case 'cancelled':
				theme_menuDeleteCancelled($aRoot);
				break;
			default:
				theme_menuDeleteDefault($aRoot,$data);
				break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>