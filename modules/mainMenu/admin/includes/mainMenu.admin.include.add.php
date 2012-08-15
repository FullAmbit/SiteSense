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
	if(!checkPermission('add','mainMenu',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    $data->output['MenuItemForm'] = new formHandler('item',$data,true);
	$options = admin_mainMenuOptions($db);
	$data->output['MenuItemForm']->fields['parent']['options'] = array_merge($data->output['MenuItemForm']->fields['parent']['options'], admin_mainMenuOptions($db));
	if(!empty($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['MenuItemForm']->fromForm))
	{
		$data->output['MenuItemForm']->populateFromPostData();
		if($data->output['MenuItemForm']->validateFromPost())
		{
			$data->output['MenuItemForm']->sendArray[':sortOrder'] =
                admin_sortOrder_new($data,$db,'main_menu','sortOrder','parent',$data->output['MenuItemForm']->sendArray[':parent'],TRUE);
			$data->output['MenuItemForm']->sendArray[':url'] = str_replace('|',$data->linkRoot,$data->output['MenuItemForm']->sendArray[':url']);
			
			$statement = $db->prepare('newMenuItem','admin_mainMenu');
			$statement->execute($data->output['MenuItemForm']->sendArray) or die('Saving Menu Item Failed');
			$id = $db->lastInsertId();
			// Duplicate Across Languages
			common_populateLanguageTables($data,$db,'main_menu','id',$id);
			
			if(empty($data->output['secondSidebar']))
			{
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['main-menu']['saveItemSuccessHeading'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/main-menu/add/">
							'.$data->phrases['main-menu']['addMenuItem'].'
						</a>
						<a href="'.$data->linkRoot.'admin/main-menu/list/">
							'.$data->phrases['main-menu']['returnToMenuItems'].'
						</a>
					</div>';
			}
		} else {
			// Invalid Data
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
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