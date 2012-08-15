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
    if(!checkPermission('enable','mainMenu',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	// Check To See If The Project Exists
	$check = $db->prepare('getMenuItemById','admin_mainMenu');
	$check->execute(array(':id' => $data->action[3]));
	if(($data->output['menuItem'] = $check->fetch()) === FALSE)
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>The ID does not exist in database</h2>';
		return;
	}
	// Enable Menu Item
	$statement = $db->prepare('enableOrDisableMenuItem','admin_mainMenu');
	$statement->execute(array(
		':id' => $data->output['menuItem']['id'],
		':enabled' => '1'
	));
	
	// Push Change Across Languages
	common_updateAcrossLanguageTables($data,$db,'main_menu',array('id'=>$data->output['menuItem']['id']),array('enabled' => '1'));
	
	// Success Message
	if (empty($data->output['secondSidebar'])) {
	  $data->output['savedOkMessage']='
		<h2>'.$data->phrases['main-menu']['enableItemSuccessHeading'].'</h2>
		<div class="panel buttonList">
			<a href="'.$data->linkRoot.'admin/main-menu/add/">
				'.$data->phrases['main-menu']['addMenuItem'].'
			</a>
			<a href="'.$data->linkRoot.'admin/main-menu/list/">
				'.$data->phrases['main-menu']['returnToMenuItems'].'
			</a>
		</div>';
  }
}
function admin_mainMenuShow($data)
{
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	}
}
?>