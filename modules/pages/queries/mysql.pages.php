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
function page_addQueries() {
	return array(
		'getPagesByShortName' => '
			SELECT * FROM !prefix!pages
			WHERE shortName = :shortName
		',
		'getPagesByParent' => '
			SELECT *
			FROM !prefix!pages
			WHERE parent = :parent
		',
		'getPageByShortNameAndParent' => '
			SELECT * FROM !prefix!pages
			WHERE shortName = :shortName
			AND parent = :parent
		',
		'getTopLevelPages' => '
			SELECT * FROM !prefix!pages WHERE parent = 0
		',
		'getEnabledSideBarsByPage' => '
			SELECT a.enabled,a.sortOrder,b.* FROM !prefix!pages_sidebars a, !prefix!sidebars b WHERE a.page = :pageId AND a.sidebar = b.id AND a.enabled = 1 ORDER BY a.sortOrder ASC
				
		',
        // Admin
        'getAllPageIds' => '
			SELECT id FROM !prefix!pages
		',
        'getPageListOrdered' => '
			SELECT id,name,sortOrder,parent,shortName
			FROM !prefix!pages
			ORDER BY parent,sortOrder,id
		',
        'updatePageSortOrderById' => '
			UPDATE !prefix!pages
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
        'getPageOrderById' => '
			SELECT id,parent,sortOrder
			FROM !prefix!pages
			WHERE id = :id
		',
        'getPageListOrphans' => '
			SELECT id,name,shortName,parent,sortOrder
			FROM !prefix!pages
			WHERE parent <= 0
			ORDER BY parent,sortOrder,id
		',
        'getPageListByParent' => '
			SELECT id,name,shortName,parent,sortOrder
			FROM !prefix!pages
			WHERE parent = :parent
			ORDER BY parent,sortOrder,id
		',
        'deletePageById' => '
			DELETE FROM !prefix!pages
			WHERE id = :id
		',
        'deletePageByParent' => '
			DELETE FROM !prefix!pages
			WHERE parent = :id
		',
        'getPageIdByShortName' => '
			SELECT id FROM !prefix!pages
			WHERE shortName = :shortName
		',
        'getPageIdByShortNameAndParent' => '
			SELECT id FROM !prefix!pages
			WHERE shortName = :shortName
			AND parent = :parent
		',
        'getIdShortNameOrphans' => '
			SELECT id,shortName FROM !prefix!pages
			WHERE parent <= 0
		',
        'insertPage' => '
			INSERT INTO !prefix!pages
			(name,shortName,title,parent,rawContent,parsedContent,sortOrder,live) VALUES (:name,:shortName, :title, :parent, :rawContent, :parsedContent, :sortOrder, :live)
		',
        'updateShortNameById' => '
			UPDATE !prefix!pages
			SET shortName = :shortName
			WHERE id = :id
		',
        'updatePageById' => '
			UPDATE !prefix!pages
			SET
				name = :name,
				shortName 	 = :shortName,
				title 			 = :title,
				parent			 = :parent,
				rawContent 		 = :rawContent,
				parsedContent 		 = :parsedContent,
				live 		     = :live
			WHERE id = :id
		',
        'getLastPageId' => '
			SELECT id FROM !prefix!pages
			ORDER BY id DESC
			LIMIT 1
		',
        'getMenuPages' => '
			SELECT * FROM !prefix!pages
			WHERE showOnMenu = 1
		',
        'getTopLevelPages' => '
			SELECT * FROM !prefix!pages WHERE parent = 0
		',
        'getPageById' => '
			SELECT * FROM !prefix!pages WHERE id = :id
		',
        'getSideBarsByPage' => '
			SELECT a.id,a.page,a.sidebar,a.enabled,a.sortOrder,b.name FROM !prefix!pages_sidebars a, !prefix!sidebars b WHERE a.page = :pageId AND a.sidebar = b.id ORDER BY a.sortOrder ASC
		',
        'enableSideBar' => '
			UPDATE !prefix!pages_sidebars
			SET
				enabled  =  1
			WHERE id = :id
		',
        'disableSideBar' => '
			UPDATE !prefix!pages_sidebars
			SET
				enabled  =  0
			WHERE id = :id
		',
        'getExistingShortNames' => '
			SELECT shortName FROM !prefix!pages
		',
        'countPagesByParent' => '
			SELECT COUNT(*) AS sortOrder FROM !prefix!pages WHERE parent = :parent
		',
        'shiftOrderUpByID' => '
			UPDATE !prefix!pages
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
        'shiftOrderUpRelative' => '
			UPDATE !prefix!pages
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND parent = :parent
			ORDER BY sortOrder DESC LIMIT 1
		',
        'shiftOrderDownByID' => '
			UPDATE !prefix!pages
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
        'shiftOrderDownRelative' => '
			UPDATE !prefix!pages
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND parent = :parent
			ORDER BY sortOrder ASC LIMIT 1
		',
        'fixSortOrderGap' => '
			UPDATE !prefix!pages
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND parent = :parent
		',
        'countSideBarsByPage' => '
			SELECT COUNT(*) as rowCount FROM !prefix!pages_sidebars WHERE page = :pageId
		',
        'createSideBarSetting' => '
			REPLACE INTO !prefix!pages_sidebars
			SET
				page = :pageId,
				sidebar = :sideBarId,
				enabled = :enabled,
				sortOrder = :sortOrder
		',
        'shiftSideBarOrderUpByID' => '
			UPDATE !prefix!pages_sidebars
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
        'shiftSideBarOrderUpRelative' => '
			UPDATE !prefix!pages_sidebars
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND page = :pageId
			ORDER BY sortOrder DESC LIMIT 1
		',
        'shiftSideBarOrderDownByID' => '
			UPDATE !prefix!pages_sidebars
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
        'shiftSideBarOrderDownRelative' => '
			UPDATE !prefix!pages_sidebars
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND page = :pageId
			ORDER BY sortOrder ASC LIMIT 1
		',
        'fixSideBarSortOrderGap' => '
			UPDATE !prefix!pages_sidebars
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND page = :pageId
		',
        'getSideBarSetting' => '
			SELECT * FROM !prefix!pages_sidebars WHERE id = :id
		',
        'deleteSideBarSettingBySideBar' => '
			DELETE FROM !prefix!pages_sidebars WHERE sidebar = :sidebar
		'
	);
}
?>