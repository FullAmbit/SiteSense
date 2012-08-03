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
function admin_dynamicForms_addQueries() {
	return array(
		'getAllFormIds' => '
			SELECT id FROM !prefix!!lang!forms
		',
		'getAllForms' => '
			SELECT * FROM !prefix!!lang!forms ORDER BY id DESC
		',
		'getFormById' => '
			SELECT * FROM !prefix!!lang!forms WHERE id = :id
		',
		'getFormByShortName' => '
			SELECT * FROM !prefix!!lang!forms WHERE shortName = :shortName AND enabled = 1
		', 
		'getTopLevelFormByShortName' => '
			SELECT * FROM !prefix!!lang!forms WHERE shortName = :shortName and topLevel = 1
		', 
		'getFieldById' => '
			SELECT * FROM !prefix!!lang!form_fields WHERE id = :id
		',
		'getRowById' => '
			SELECT * FROM !prefix!form_rows WHERE id = :id
		',
		'getValueById' => '
			SELECT * FROM !prefix!form_values WHERE id = :id
		',
		'getFieldsByForm' => '
			SELECT * FROM !prefix!!lang!form_fields WHERE form = :form ORDER BY sortOrder ASC
		',
		'getRowsByForm' => '
			SELECT * FROM !prefix!form_rows WHERE form = :form ORDER BY ID DESC
		',
		'getValuesByRow' => '
			SELECT * FROM !prefix!form_rows WHERE row = :row
		',
		'getValuesByField' => '
			SELECT * FROM !prefix!form_values WHERE field = :field
		',
		'getValuesByRow' => '
			SELECT * FROM !prefix!form_values WHERE row = :row AND field = :field
		',
		'getValuesByForm' => '
			SELECT v.* FROM !prefix!form_values v
				INNER JOIN !prefix!form_rows r
					ON r.id = v.row
			WHERE r.form = :form
		',
		'newForm' => '
			INSERT INTO !prefix!!lang!forms (
				enabled, 
				shortName, 
				name, 
				title, 
				rawContentBefore, 
				parsedContentBefore, 
				rawContentAfter, 
				parsedContentAfter, 
				rawSuccessMessage, 
				parsedSuccessMessage,
				requireLogin, 
				topLevel, 
				eMail, 
				submitTitle,
				api
			) 
			VALUES (
				:enabled, 
				:shortName, 
				:name, 
				:title, 
				:rawContentBefore, 
				:parsedContentBefore, 
				:rawContentAfter, 
				:parsedContentAfter, 
				:rawSuccessMessage, 
				:parsedSuccessMessage, 
				:requireLogin, 
				:topLevel, 
				:eMail, 
				:submitTitle,
				:api
			)
		',
		'newField' => '
			INSERT INTO !prefix!!lang!form_fields
			(form, name, type, description, enabled, required, moduleHook, apiFieldToMapTo, sortOrder, isEmail, compareTo)
			VALUES
			(:form,:name,:type,:description,:enabled,:required,:moduleHook,:apiFieldToMapTo,:sortOrder,:isEmail,:compareTo)
		',
		'newRow' => '
			INSERT INTO !prefix!form_rows (form) VALUES (:form)
		',
		'newValue' => '
			INSERT INTO !prefix!form_values (row, field, value) VALUES (:row, :field, :value)
		',
		'editForm' => '
			UPDATE !prefix!!lang!forms SET 
				enabled = :enabled, 
				name = :name, 
				title = :title, 
				shortName = :shortName, 
				rawContentBefore = :rawContentBefore, 
				parsedContentBefore =:parsedContentBefore, 
				rawContentAfter =:rawContentAfter, 
				parsedContentAfter =:parsedContentAfter, 
				rawSuccessMessage =:rawSuccessMessage, 
				parsedSuccessMessage =:parsedSuccessMessage, 
				requireLogin = :requireLogin, 
				topLevel = :topLevel, 
				eMail = :eMail, 
				submitTitle = :submitTitle, 
				api = :api 
			WHERE id = :id
		',
		'editField' => '
			UPDATE !prefix!!lang!form_fields SET 
			name            = :name,
			description     = :description,
			type            = :type,
			enabled         = :enabled,
			required        = :required,
			apiFieldToMapTo = :apiFieldToMapTo,
			moduleHook      = :moduleHook,
			isEmail         = :isEmail,
			compareTo       = :compareTo
			WHERE id        = :id
		',
		'editValue' => '
			UPDATE !prefix!form_values SET value = :value WHERE id = :id
		',
		'deleteForm' => '
			DELETE FROM !prefix!!lang!forms WHERE id = :id
		',
		'deleteField' => '
			DELETE FROM !prefix!!lang!form_fields WHERE id = :id
		',
		'deleteRow' => '
			DELETE FROM !prefix!form_rows WHERE id = :id
		',
		'deleteValue' => '
			DELETE FROM !prefix!form_values WHERE id = :id
		',
		'deleteValueByRow' => '
			DELETE FROM !prefix!form_values WHERE row = :rowID
		',
		'deleteRowsByForm' => '
			DELETE FROM !prefix!form_rows WHERE form = :formID
		',
		'deleteFieldsByForm' => '
			DELETE FROM !prefix!!lang!form_fields WHERE form = :formID
		',
		'saveMenuItem' => '
			INSERT INTO !prefix!main_menu (text,title,url,module,side,sortOrder,enabled) VALUES (:name,:title,:shortName,:module,:side,:sortOrder,:enabled)
		',
		'getOptionsByFieldId' => '
			SELECT id,sortOrder,text,value FROM !prefix!!lang!form_fields_options WHERE fieldId = :fieldId ORDER BY sortOrder ASC
		',
		'addOption' => '
			INSERT INTO
				!prefix!!lang!form_fields_options 
				(formId,fieldId,sortOrder,text,value)
			VALUES 
				(:formId,:fieldId,:sortOrder,:text,:value)
		',
		'updateOptionById' => '
			UPDATE
				!prefix!!lang!form_fields_options
			SET
				text = :text,
				value = :value
			WHERE id = :id
		',
		'getOptionById' => '
			SELECT 
				* 
			FROM 
				!prefix!!lang!form_fields_options 
			WHERE 
				id = :id 
			LIMIT 
				1
		',
		'deleteOption' => '
			DELETE FROM 
				!prefix!!lang!form_fields_options 
			WHERE 
				id = :id 
			LIMIT
				1
		',
		'getExistingShortNames' => '
			SELECT shortName FROM !prefix!!lang!forms
		',
		'countSidebarsByForm' => '
			SELECT COUNT(*) FROM !prefix!form_sidebars WHERE form = :formId
		',
		'createSidebarSetting' => '
			REPLACE INTO !prefix!form_sidebars
			SET
				form = :formId,
				sidebar = :sidebarId,
				enabled = :enabled,
				sortOrder = :sortOrder
		',
		'getSidebarsByForm' => '
			SELECT a.id,a.form,a.sidebar,a.enabled,a.sortOrder,b.name FROM !prefix!form_sidebars a, !prefix!!lang!sidebars b WHERE a.form = :formId AND a.sidebar = b.id ORDER BY a.sortOrder ASC
		',
		'enableSidebar' => '
			UPDATE !prefix!form_sidebars
			SET
				enabled  =  1
			WHERE id = :id
		',
		'disableSidebar' => '
			UPDATE !prefix!form_sidebars
			SET
				enabled  =  0
			WHERE id = :id
		',
		'getSidebarSetting' => '
			SELECT * FROM !prefix!form_sidebars WHERE id = :id
		',
		'shiftSidebarOrderUpByID' => '
			UPDATE !prefix!form_sidebars
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
		'shiftSidebarOrderUpRelative' => '
			UPDATE !prefix!form_sidebars
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND form = :formId
			ORDER BY sortOrder DESC LIMIT 1
		',
		'shiftSidebarOrderDownByID' => '
			UPDATE !prefix!form_sidebars
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
		'shiftSidebarOrderDownRelative' => '
			UPDATE !prefix!form_sidebars
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND form = :formId
			ORDER BY sortOrder ASC LIMIT 1
		',
		'deleteSidebarSettingBySidebar' => '
			DELETE FROM !prefix!form_sidebars WHERE sidebar = :sidebar
		',
		'getEnabledSidebarsByForm' => '
			SELECT a.enabled,a.sortOrder,b.* FROM !prefix!form_sidebars a, !prefix!sidebars b WHERE a.form = :formId AND a.sidebar = b.id AND a.enabled = 1 ORDER BY a.sortOrder ASC
		',
		'countFieldsByForm' => '
			SELECT COUNT(*) as rowCount FROM !prefix!!lang!form_fields WHERE form = :formId
		',
		'shiftFieldOrderUpByID' => '
			UPDATE !prefix!!lang!form_fields
			SET sortOrder = sortOrder - 1
			WHERE id = :id
		',
		'shiftFieldOrderUpRelative' => '
			UPDATE !prefix!!lang!form_fields
			SET sortOrder = sortOrder + 1
			WHERE sortOrder < :sortOrder AND form = :formId
			ORDER BY sortOrder DESC LIMIT 1
		',
		'shiftFieldOrderDownByID' => '
			UPDATE !prefix!!lang!form_fields
			SET sortOrder = sortOrder + 1
			WHERE id = :id
		',
		'shiftFieldOrderDownRelative' => '
			UPDATE !prefix!!lang!form_fields
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND form = :formId
			ORDER BY sortOrder ASC LIMIT 1
		',
		'fixFieldSortOrderGap' => '
			UPDATE !prefix!!lang!form_fields
			SET sortOrder = sortOrder - 1
			WHERE sortOrder > :sortOrder AND form = :formId
		',
	);
}
?>