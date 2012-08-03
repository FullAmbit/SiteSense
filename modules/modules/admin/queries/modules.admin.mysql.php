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
function admin_modules_addQueries() {
	return array(
		'getAllModuleIds' => '
			SELECT id FROM !prefix!modules
		',
		'getModuleByShortName' => '
			SELECT * FROM !prefix!modules
			WHERE shortName = :shortName AND enabled = 1
		',
		'getModuleById' => '
			SELECT * FROM !prefix!modules
			WHERE id = :id
		',
		'getAllModules' => '
			SELECT * FROM !prefix!modules
			ORDER BY name ASC
		',
		'getEnabledModules' => ' 
			SELECT * FROM !prefix!modules
			WHERE enabled = 1
			ORDER BY name ASC
		',
		'getAllSidebars' => '
			SELECT * FROM !prefix!sidebars
		',
		'getSidebarById' => '
			SELECT * FROM !prefix!sidebars
			WHERE id = :id
		',
		'getSidebarsByModule' => '
			SELECT a.id,a.module,a.sidebar,a.enabled,a.sortOrder,b.name FROM !prefix!module_sidebars a, !prefix!sidebars b WHERE a.module = :module AND a.sidebar = b.id ORDER BY a.sortOrder ASC
		',
		'getEnabledSidebarsByModule' => '
				SELECT a.enabled,a.sortOrder,b.* FROM !prefix!module_sidebars a, !prefix!sidebars b WHERE a.module = :module AND a.sidebar = b.id AND a.enabled = 1 ORDER BY a.sortOrder ASC
		',
		'enableSidebar' => '
			UPDATE !prefix!module_sidebars
			SET
				enabled  =  1
			WHERE id = :id
		',
		'disableSidebar' => '
			UPDATE !prefix!module_sidebars
			SET
				enabled  =  0
			WHERE id = :id
		',
		'newModule' => '
			INSERT INTO !prefix!modules
				(name, shortName, enabled)
			VALUES
				(:name, :shortName, :enabled)
		',
		'editModule' => '
			UPDATE !prefix!modules
				SET
					name = :name,
					shortName = :shortName,
					enabled = :enabled
				WHERE
					id = :id
		',
		'updateModule' => '
			UPDATE !prefix!modules
				SET
					name = :name,
					enabled = :enabled
				WHERE
					shortName = :shortName
		',
		'enableModule' => '
			UPDATE !prefix!modules
				SET
				enabled = 1
				WHERE shortName = :shortName
		',
		'disableModule' => '
			UPDATE !prefix!modules
				SET enabled = 0
				WHERE shortName = :shortName
		',
		'deleteModule' => '
			DELETE FROM !prefix!modules WHERE id = :id
		',
		'countSidebarsByModule' => '
			SELECT COUNT(*) AS rowCount FROM !prefix!module_sidebars WHERE module = :moduleId
		',
		'createSidebarSetting' => '
			REPLACE INTO !prefix!module_sidebars
			SET
				module = :moduleId,
				sidebar = :sidebarId,
				enabled = :enabled,
				sortOrder = :sortOrder
		',
		'getSidebarSetting' => '
			SELECT * FROM !prefix!module_sidebars WHERE id = :id
		',
		'shiftSidebarOrderUpByID' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
		'shiftSidebarOrderUpRelative' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND module = :moduleId
			ORDER BY sortOrder DESC LIMIT 1
		',
		'shiftSidebarOrderDownByID' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
		'shiftSidebarOrderDownRelative' => '
			UPDATE !prefix!module_sidebars
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND module = :moduleId
			ORDER BY sortOrder ASC LIMIT 1
		',
		'deleteSidebarSettingBySidebar' => '
			DELETE FROM !prefix!module_sidebars WHERE sidebar = :sidebar
		'
	);
}
?>