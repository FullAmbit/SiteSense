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
function dynamicForms_settings() {
	return array(
		'name'      => 'dynamicForms',
		'shortName' => 'dynamic-forms'
	);
}
function dynamicForms_install($db,$drop=false,$lang='en_us') {
	$lang = rtrim($lang,'_').'_';
	$structures = array(
		'forms' => array(
			'id'                   => SQR_IDKey,
			'enabled'              => SQR_boolean,
			'shortName'            => SQR_shortName,
			'name'                 => SQR_name,
			'title'                => SQR_title,
			'rawContentBefore'     => 'TEXT NOT NULL',
			'parsedContentBefore'  => 'TEXT NOT NULL',
			'rawContentAfter'      => 'TEXT NOT NULL',
			'parsedContentAfter'   => 'TEXT NOT NULL',
			'rawSuccessMessage'    => 'TEXT NOT NULL',
			'parsedSuccessMessage' => 'TEXT NOT NULL',
			'requireLogin'         => SQR_boolean,
			'topLevel'             => SQR_boolean,
			'eMail'                => SQR_email,
			'submitTitle'          => 'VARCHAR(63) NOT NULL',
			'api'                  => 'VARCHAR(255) DEFAULT NULL'
		),
		'form_fields' => array(
			'id'                   => SQR_IDKey,
			'form'                 => SQR_ID,
			'name'                 => 'VARCHAR(31) NOT NULL',
			'type'                 => 'VARCHAR(15) NOT NULL',
			'description'          => 'VARCHAR(255) NOT NULL',
			'options'              => 'TEXT',
			'required'             => SQR_boolean.' DEFAULT \'0\'',
			'enabled'              => SQR_boolean.' DEFAULT \'0\'',
			'apiFieldToMapTo'      => 'VARCHAR(255) DEFAULT NULL',
			'moduleHook'           => 'VARCHAR(127) DEFAULT NULL',
			'sortOrder'            => SQR_sortOrder.' DEFAULT \'1\'',
			'isEmail'              => SQR_boolean.' DEFAULT \'0\'',
			'compareTo'            => SQR_ID
		),
		'form_sidebars' => array(
			'id'                   => SQR_IDKey,
			'form'                 => SQR_ID,
			'sidebar'              => SQR_ID,
			'enabled'              => SQR_boolean.' DEFAULT \'0\'',
			'sortOrder'            => SQR_sortOrder.' DEFAULT \'1\'',
			'UNIQUE KEY `form` (`form`,`sidebar`)'
		),
		'form_rows' => array(
			'id'                   => SQR_IDKey,
			'form'                 => SQR_ID
		),
		'form_values' => array(
			'id'                   => SQR_IDKey,
			'row'                  => SQR_ID,
			'field'                => SQR_ID,
			'value'                => 'TEXT NOT NULL'
		)
	);
	if($drop)
        dynamicForms_uninstall($db,$lang);

	$db->createTable($lang.'forms',$structures['forms'],false);
	$db->createTable($lang.'form_fields',$structures['form_fields'],false);
	$db->createTable('form_rows',$structures['form_rows'],false);
	$db->createTable('form_values',$structures['form_values'],false);
	$db->createTable('form_sidebars',$structures['form_sidebars'],false);

    // Set up default permission groups
    $defaultPermissionGroups=array(
        'Moderator' => array(
            'dynamicForms_access',
			'dynamicForms_add',
			'dynamicForms_edit',
			'dynamicForms_delete',
			'dynamicForms_viewData'
        ),
        'Writer' => array(
            'dynamicForms_access',
			'dynamicForms_add',
			'dynamicForms_edit',
			'dynamicForms_delete',
			'dynamicForms_viewData'
        )
    );
    foreach($defaultPermissionGroups as $groupName => $permissions) {
        foreach($permissions as $permissionName) {
            $statement=$db->prepare('addPermissionByGroupName');
            $statement->execute(
                array(
                    ':groupName' => $groupName,
                    ':permissionName' => $permissionName
                )
            );
        }
    }

}
function dynamicForms_uninstall($db,$lang) {
	$lang = rtrim($lang,'_').'_';
	
    $db->dropTable($lang.'forms');
    $db->dropTable($lang.'form_fields');
    $db->dropTable('form_rows');
    $db->dropTable('form_values');
    $db->dropTable('form_sidebars');
}
?>