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
common_include('libraries/forms.php');
function admin_mainMenuBuild($data,$db) {
	if(!checkPermission('mainMenu_add','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    $data->output['MenuItemForm'] = new formHandler('menuItem',$data,true);
	$data->output['MenuItemForm']->caption = 'New Menu Item';
	$options = admin_mainMenuOptions($db);
	$data->output['MenuItemForm']->fields['parent']['options'] = array_merge($data->output['MenuItemForm']->fields['parent']['options'], admin_mainMenuOptions($db));
	if(!empty($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['MenuItemForm']->fromForm))
	{
		$data->output['MenuItemForm']->populateFromPostData();
		if($data->output['MenuItemForm']->validateFromPost())
		{
			$statement = $db->prepare('countItemsByParent','admin_mainMenu');
			$statement->execute(array(':parent' => $data->output['MenuItemForm']->sendArray[':parent']));
			list($rowCount) = $statement->fetch();
			$data->output['MenuItemForm']->sendArray[':sortOrder'] = $rowCount + 1;
			
			$data->output['MenuItemForm']->sendArray[':url'] = str_replace('|',$data->linkRoot,$data->output['MenuItemForm']->sendArray[':url']);
			
			$statement = $db->prepare('newMenuItem','admin_mainMenu');
			$statement->execute($data->output['MenuItemForm']->sendArray) or die('Saving Menu Item Failed');
			if(empty($data->output['secondSideBar']))
			{
				$data->output['savedOkMessage']='
					<h2>Menu Item Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/main-menu/edit/">
							Add New Menu Item
						</a>
						<a href="'.$data->linkRoot.'admin/main-menu/list/">
							Return to MenuItem List
						</a>
					</div>';
			}
		} else {
			// Invalid Data
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}
function admin_mainMenuShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['MenuItemForm']);
	}
}
function admin_mainMenuOptions($db,$parent = 0,$level = 0,$options = array()) {
	// Get All Items In Current Level
	$statement = $db->prepare('getMenuItemByParent','admin_mainMenu');
	$statement->execute(array(
		':parent' => $parent
	));
	$menuItemList = $statement->fetchAll();
	foreach($menuItemList as $menuItem)
	{
		$hypen = '';
		for($i=0;$i<$level;$i++)
		{
			$hypen .= '--';
		}
		$options[$menuItem['id']]['text'] = $hypen.' '.$menuItem['text'];
		$options[$menuItem['id']]['value'] = $menuItem['id'];
		// Now Get This Item's Children
		$options = admin_mainMenuOptions($db,$menuItem['id'],$level + 1,$options);
	}
	return $options;
}
?>