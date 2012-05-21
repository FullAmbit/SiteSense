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
    if(!checkPermission('mainMenu_list','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    switch ($data->action[3]) {
		case 'switch':
			if (is_numeric($data->action[4])) {
				$statement=$db->prepare('getMenuItemById','admin_mainMenu');
				$statement->execute(array(
					':id' => $data->action[4]
				));
				if ($item=$statement->fetch()) {
					if($item['parent'] == '0')
					{
						$statement=$db->prepare('updateSideById','admin_mainMenu');
						$statement->execute(array(
							':side' => ( $item['side']=='left' ? 'right' : 'left' ),
							':id' => $item['id']
						));
					}
				}
			}
		break;
		case 'moveUp':
		case 'moveDown':
			if (is_numeric($data->action[4])) {
				$statement=$db->prepare('getMenuItemById','admin_mainMenu');
				$statement->execute(array(
					':id' => $data->action[4]
				));
				if ($item=$statement->fetch()) {
					/**
					DEPRECATED BY SHAFIQUE ARNAB
					SEPTEMBER 4 2011
					if ($data->action[3]=='moveUp') {
						$item['sortOrder']-=3;
					} else {
						$item['sortOrder']+=3;
					}
					$statement=$db->prepare('updateOrderById','admin_mainMenu');
					$statement->execute(array(
						':sortOrder' => $item['sortOrder'],
						':id' => $item['id']
					));
					**/
					/**
					 * Get the total row count
					 * If the object is at the bottom or top, do not sort
					**/
					$statement = $db->prepare('countItemsByParent','admin_mainMenu');
					$statement->execute(array(':parent' => $item['parent']));
					list($rowCount) = $statement->fetch();
					
					if($data->action[3] == 'moveUp' && intval($item['sortOrder']) > 1) {
						$query1 = 'shiftOrderUpRelative';
						$query2 = 'shiftOrderUpByID';
					} else if($data->action[3] == 'moveDown' && intval($item['sortOrder']) < $rowCount) {
						$query1 = 'shiftOrderDownRelative';
						$query2 = 'shiftOrderDownByID';
					}
					if(isset($query1))
					{
						$statement = $db->prepare($query1,'admin_mainMenu');
						$statement->execute(array(
							':sortOrder' => $item['sortOrder'],
							':parent' => $item['parent']
						));
						$statement = $db->prepare($query2,'admin_mainMenu');
						$statement->execute(array(
							':id' => $item['id']
						));
					}
					/**$statement->execute(array(
						':sortOrder' => $item['sortOrder'],
						':id' => $item['id']
					));
					**/
				}
			}
		break;
	}
	//admin_mainMenuRebuild($data,$db);
	$statement=$db->query('getMenuItemsOrdered','admin_mainMenu');
	$data->output['menuList'] = $menuList = $statement->fetchAll();
	$menuParents = array();
	// Prepare Parent to Child Associative Array
	foreach($menuList as $item)
	{
		if($item['parent'] == '0') continue;
		$menuParents[$item['parent']][] = $item;	
	}
	
	$data->output['menuParents'] = $menuParents;
	
	$data->output['level'] = 0;
}

function scanChildren($data,$item,$level = 0)
{
	// First Thing First, Print This Out
	itemRow($data,$item,$level);
	
	// Add To Completed
	$data->output['completed'][$item['id']] = true;

	if(isset($data->output['menuParents'][$item['id']]))
	{
		$level++;
		// We have kids! Loop Through them.
		foreach($data->output['menuParents'][$item['id']] as $child)
		{
			// Now Process This Child (recursive)
			scanChildren($data,$child,$level);
		}
	}
}

function itemRow($data,$item,$level)
{
	
	if($item['enabled'] == '0')
		{
			$enable = '<a href="'.$data->linkRoot.'admin/main-menu/enable/'.$item['id'].'/" title="Enable">Enable</a>';
		} else {
			$enable = '<a href="'.$data->linkRoot.'admin/main-menu/disable/'.$item['id'].'/" title="Disable">Disable</a>';
		}
		theme_menuItemRow($level,$data,$item,$enable);
}

function admin_mainMenuShow($data) {
	theme_menuShowHead($data);
	foreach ($data->output['menuList'] as $item) {
		// Skip It If It Was Already Done
		if(isset($data->output['completed'][$item['id']])) continue;
		// If We Got To You Early, Quit
		if($item['parent'] > 0) continue; 
		// Processs Kids
		scanChildren($data,$item);
	}
	theme_menuShowFoot();
}
?>
