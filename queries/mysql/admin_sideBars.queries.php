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
function admin_sideBars_addQueries() {
	return array(
		'getSorted' => '
			SELECT id
			FROM !prefix!sideBars
			ORDER BY id
		',
		'getSortOrderById' => '
			SELECT id
			FROM !prefix!sidebars
			WHERE id = :id
		',
		'updateSortOrderById' => '
			UPDATE !prefix!sidebars
			SET sortOrder = :sortOrder
			WHERE id = :id
		',
		'updateEnabledById' => '
			UPDATE !prefix!sidebars
			SET enabled = :enabled
			WHERE id = :id
		',
		'getSideBarNameByName' => '
			SELECT name FROM !prefix!sidebars
			WHERE name = :name
		',
		'insertSideBarFile' => '
			INSERT INTO !prefix!sidebars
			(name,shortName,enabled,fromFile,side) VALUES (:name,:shortName,false,true,"left")
		',
		'insertSideBar' => '
			INSERT INTO !prefix!sidebars
			(name,shortName,enabled,fromFile,title,side,titleURL,rawContent,parsedContent) VALUES (:name,:shortName, true, false, :title, :side, :titleURL, :rawContent,:parsedContent)
		',
		'getFromFiles' => '
			SELECT * FROM !prefix!sidebars
			WHERE fromFile = TRUE
			ORDER BY id ASC
		',
		'getFromFileById' => '
			SELECT fromFile FROM !prefix!sidebars
			WHERE id = :id
		',
		'deleteById' => '
			DELETE FROM !prefix!sidebars
			WHERE id = :id
		',
		'getAllSideBars' => '
			SELECT * FROM !prefix!sidebars
		',
		'getAllOrdered' => '
			SELECT * FROM !prefix!sidebars
			ORDER BY id ASC
		',
		'getById' => '
			SELECT * FROM !prefix!sidebars
			WHERE id = :id
		',
		'getIdByShortName' => '
			SELECT id FROM !prefix!sideBars
			WHERE shortName = :shortName
		',
		'updateShortNameById' => '
			UPDATE !prefix!sideBars
			SET shortName = :shortName
			WHERE id = :id
		',
		'updateById' => '
			UPDATE !prefix!sidebars
			SET
				name      = :name,
				shortName = :shortName,
				title     = :title,
				titleURL  = :titleURL,
				side = :side,
				rawContent   = :rawContent,
				parsedContent = :parsedContent
			WHERE id = :id
		',
		'shiftOrderUpByID' => '
			UPDATE !prefix!sidebars
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
		'shiftOrderUpRelative' => '
			UPDATE !prefix!sidebars
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder
			ORDER BY sortOrder DESC LIMIT 1
		',
		'shiftOrderDownByID' => '
			UPDATE !prefix!sidebars
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
		'shiftOrderDownRelative' => '
			UPDATE !prefix!sidebars
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder
			ORDER BY sortOrder ASC LIMIT 1
		',
		'updateSideById' => '
			UPDATE !prefix!sidebars SET side = :side WHERE id = :id LIMIT 1
		',
		'getExistingShortNames' => '
			SELECT shortName FROM !prefix!sidebars
		',
		'deleteSettings' => '
			(DELETE FROM !prefix!form_sidebars WHERE sidebar = :sidebar)
			(DELETE FROM !prefix!pages_sidebars WHERE sidebar = :sidebar)
			(DELETE FROM !prefix!module_sidebars WHERE sidebar = :sidebar)
		'
	);
}
?>