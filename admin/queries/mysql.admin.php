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
/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/
function admin_addQueries() {
	return array(
		'getMainMenu' => '
			SELECT * FROM !prefix!main_menu
		',
		'insertMenuItem' => '
			INSERT INTO !prefix!main_menu
			(text,title,url,module,side,sortOrder,enabled) VALUES (:text,:title,:url,:module,\'left\',999999999,:enabled)
		',
		'insertMenuItemWithSortAndSide' => '
			INSERT INTO !prefix!main_menu
			(text,title,url,module,side,sortOrder,enabled) VALUES (:text,:title,:url,:module,:side,:sortOrder,:enabled)
		',
		'deleteMenuItemById' => '
			DELETE FROM !prefix!main_menu
			WHERE id = :id
		',
		'deletePageMenuItems' => '
			DELETE FROM !prefix!main_menu
			WHERE module = \'pages\'
		',
		'getMenuItemsOrdered' => '
			SELECT * FROM !prefix!main_menu
			ORDER BY side ASC, sortOrder ASC
		',
		'updateMenuSortOrder' => '
			UPDATE !prefix!main_menu
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
	);
}
?>