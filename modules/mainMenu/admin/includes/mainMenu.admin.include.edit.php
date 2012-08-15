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
    if(!checkPermission('edit','mainMenu',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    if($data->action[3] === false){
		$existing = false;
	}else{
		$existing = (int)$data->action[3];
		$check = $db->prepare('getMenuItemById','admin_mainMenu');
		$check->execute(array(':id' => $existing));
		if(($data->output['menuItem'] = $check->fetch()) === false){
			$data->output['abort'] = true;
			$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
			return;
		}
	}
	$data->output['MenuItemForm'] = new formHandler('item',$data,true);
	$data->output['MenuItemForm']->caption = 'Editing Menu Item';
	$data->output['MenuItemForm']->fields['parent']['options'] = array_merge($data->output['MenuItemForm']->fields['parent']['options'], admin_mainMenuOptions($data,$db));
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['MenuItemForm']->fromForm)
	) {
		$data->output['MenuItemForm']->populateFromPostData();
		if ($data->output['MenuItemForm']->validateFromPost()) {
			// Are We Updating Sort-Order Based Off Parent?
			$newParent = $data->output['MenuItemForm']->sendArray[':parent'];
			if($newParent !== $data->output['menuItem']['parent']) {
				$data->output['MenuItemForm']->sendArray[':sortOrder'] = admin_sortOrder_new($data,$db,'main_menu','sortOrder','parent',$newParent);
			} else {
				$data->output['MenuItemForm']->sendArray[':sortOrder'] = $data->output['menuItem']['sortOrder'];
			}
			$statement = $db->prepare('editMenuItem','admin_mainMenu');
			$data->output['MenuItemForm']->sendArray[':id'] = $existing;
			$statement->execute($data->output['MenuItemForm']->sendArray) or die('Saving Menu Item failed');
			// -- Push The Constant Fields Across Other Languages
			common_updateAcrossLanguageTables($data,$db,'main_menu',array('id'=>$data->action[3]),array(
				'enabled' => $data->output['MenuItemForm']->sendArray[':enabled'],
				'parent' => $data->output['MenuItemForm']->sendArray[':parent']
			));
			
			if (empty($data->output['secondSidebar'])) {
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
function admin_mainMenuOptions($data,$db,$parent = 0,$level = 0,$options = array())
{
	// Get All Items In Current Level
	$statement = $db->prepare('getMenuItemByParent','admin_mainMenu');
	$statement->execute(array(
		':parent' => $parent
	));
	$menuItemList = $statement->fetchAll();
	foreach($menuItemList as $menuItem)
	{
		if($menuItem['id'] == $data->output['menuItem']['id'])
		{
			continue;
		}	
		$hypen = '';
		for($i=0;$i<$level;$i++)
		{
			$hypen .= '--';
		}
		$options[$menuItem['id']]['text'] = $hypen.' '.$menuItem['text'];
		$options[$menuItem['id']]['value'] = $menuItem['id'];
		// Now Get This Item's Children
		$options = admin_mainMenuOptions($data,$db,$menuItem['id'],$level + 1,$options);
	}
	return $options;
}
?>