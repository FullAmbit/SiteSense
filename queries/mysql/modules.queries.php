<?php
/*
!table! = $tableName
!prefix! = dynamicPDO::tablePrefix
*/

function modules_addQueries() {
	return array(
		'getModuleByShortName' => '
			SELECT * FROM !prefix!modules
			WHERE shortName = :shortName		
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
		'getSideBarById' => '
			SELECT * FROM !prefix!sidebars
			WHERE id = :id
		',
		'getSideBarsByModule' => '
				SELECT
					id, name, enabled
				FROM !prefix!sidebars
				WHERE id NOT IN (
					SELECT sidebar FROM !prefix!modulesidebars WHERE module = :module
				)
			UNION
				SELECT
					s.id, s.name, m.enabled
				FROM !prefix!sidebars s
					INNER JOIN
						!prefix!modulesidebars m
					ON
						s.id = m.sidebar
				WHERE m.module = :module
		',
		'getEnabledSideBarsByModule' => '
				SELECT
					id, name
				FROM !prefix!sidebars
				WHERE
					enabled = 1
				AND
					id NOT IN (
						SELECT sidebar FROM !prefix!modulesidebars WHERE module = :module
					)
			UNION
				SELECT
					s.id, s.name
				FROM !prefix!sidebars s
					INNER JOIN
						!prefix!modulesidebars m
					ON
						s.id = m.sidebar
				WHERE m.module = :module AND m.enabled = 1 
		',
		'enableSideBar' => '
			REPLACE INTO !prefix!modulesidebars
			SET
				module   =  :module,
				sidebar  =  :sidebar,
				enabled  =  1
		',
		'disableSideBar' => '
			REPLACE INTO !prefix!modulesidebars
			SET
				module   =  :module,
				sidebar  =  :sidebar,
				enabled  =  0
		',
		'newModule' => '
			INSERT INTO !prefix!modules
				(name, shortName, enabled)
			VALUES
				(:name, :shortName, :enabled)
		',
		'editModule' => '
			UPDATE !prefix!modules
				SET name = :name,
					shortName = :shortName,
					enabled = :enabled
				WHERE
					id = :id
		',
		'deleteModule' => '
			DELETE FROM !prefix!modules WHERE id = :id
		'
	);
}