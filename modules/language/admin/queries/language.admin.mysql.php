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
function admin_language_addQueries() {
	return array(
		'getSideById' => '
			SELECT id,side
			FROM !prefix!main_menu
			WHERE id = :id
		',
		'updateSideById' => '
			UPDATE !prefix!main_menu
			SET side = :side
			WHERE id = :id
		',
		'getSortOrderById' => '
			SELECT id,sortOrder
			FROM !prefix!main_menu
			WHERE id = :id
		',
		'updateOrderById' => '
			UPDATE !prefix!main_menu
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
		'getMenuItemsOrdered' => '
			SELECT * FROM !prefix!main_menu
			ORDER BY sortOrder ASC, side ASC
		',
		'getMenuItemById' => '
			SELECT * FROM !prefix!main_menu
			WHERE id = :id
		',
		'getMenuItemByParent' => '
			SELECT * FROM !prefix!main_menu WHERE parent = :parent
		',
		'editMenuItem' => '
			UPDATE !prefix!main_menu
			SET
				text = :text,
				title = :title,
				url = :url,
				enabled = :enabled,
				sortOrder = :sortOrder,
				parent = :parent
			WHERE id = :id
		',
		'newMenuItem' => '
			INSERT INTO !prefix!main_menu (text,title,url,sortOrder,enabled,parent) VALUES (:text,:title,:url,:sortOrder,:enabled,:parent)
		',
		'shiftOrderUpByID' => '
			UPDATE !prefix!main_menu
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
		'shiftOrderUpRelative' => '
			UPDATE !prefix!main_menu
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND parent = :parent
			ORDER BY sortOrder DESC LIMIT 1
		',
		'shiftOrderDownByID' => '
			UPDATE !prefix!main_menu
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
		'shiftOrderDownRelative' => '
			UPDATE !prefix!main_menu
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND parent = :parent
			ORDER BY sortOrder ASC LIMIT 1
		',
		'getMenuItemByParent' => '
			SELECT * FROM !prefix!main_menu WHERE parent = :parent
		',
		'enableOrDisableMenuItem' => '
			UPDATE !prefix!main_menu SET enabled = :enabled WHERE id = :id LIMIT 1
		',
		'fixSortOrderGap' => '
			UPDATE !prefix!main_menu
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND parent = :parent
		',
		'deleteMenuItemById' => '
			DELETE FROM !prefix!main_menu WHERE id = :id LIMIT 1
		',
		'countItemsByParent' => '
			SELECT COUNT(*) as sortOrder FROM !prefix!main_menu WHERE parent = :parent
		',
		'deleteItemsByParent' => '
			DELETE FROM !prefix!main_menu WHERE parent = :parent
		'
	);
}
?>