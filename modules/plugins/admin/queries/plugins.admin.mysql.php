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
function admin_plugins_addQueries() {
	return array(
		'getAllPlugins' => '
			SELECT * FROM !prefix!plugins ORDER BY name ASC
		',
		'newPlugin' => '
			INSERT INTO !prefix!plugins (name,enabled,isCDN,isEditor) VALUES (:name,:enabled,:isCDN,:isEditor)
		',
		'getPluginById' => '
			SELECT * FROM !prefix!plugins WHERE id = :pluginId LIMIT 1
		',
		'getPluginByName' => '
			SELECT * FROM !prefix!plugins WHERE name = :name
		',
		'getModulesEnabledForPlugin' => '
			SELECT !prefix!plugins_modules.module
				FROM !prefix!plugins_modules
				WHERE plugin = :plugin
		',
		'updatePluginModules' => '
			UPDATE !prefix!plugins SET modules = :modules WHERE id = :pluginId
		',
		'enableAndUpdate' => '
			UPDATE !prefix!plugins
				SET
					enabled = 1,
					isCDN = :isCDN,
					isEditor = :isEditor
				WHERE
					name = :name
		',
		'setPluginToCDN' => '
			UPDATE !prefix!plugins SET isCDN = 1 WHERE id = :id LIMIT 1
		',
		'getCDNPlugins' => '
			SELECT * FROM !prefix!plugins WHERE isCDN = 1 AND enabled = 1
		',
		'setPluginToEditor' => '
			UPDATE !prefix!plugins SET isEditor = 1 WHERE id = :id LIMIT 1
		',
		'getEditorPlugins' => '
			SELECT * FROM !prefix!plugins WHERE isEditor = 1 AND enabled = 1
		',
		'insertCMSSetting' => '
			INSERT INTO !prefix!settings (name,category,value) VALUES (:name,"cms",:value)
		',
		'deletePlugin' => '
			DELETE FROM !prefix!plugins WHERE id = :id
		',
		'enablePluginForModule' => '
			INSERT INTO !prefix!plugins_modules
				(plugin,module)
			VALUES
				(:plugin,:module)
		',
		'disablePluginForModule' => '
			DELETE FROM !prefix!plugins_modules
			WHERE plugin = :plugin
			AND module = :module
		',
		'disable' => '
			UPDATE !prefix!plugins
			SET enabled = 0
			WHERE name = :name
		',
		'enable' => '
			UPDATE !prefix!plugins
			SET enabled = 1
			WHERE name = :name
		'
	);
}
?>