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
function forms_settings() {
	return array(
		'name' => 'forms',
		'shortName' => 'forms'
	);
}
function forms_install($data,$drop=false) {
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
			'sortOrder'            => SQR_sortOrder.' DEFAULT \'1\'',
			'isEmail'              => SQR_boolean.' DEFAULT \'0\''
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
	if($drop) {
		$data->dropTable('forms');
		$data->dropTable('form_fields');
		$data->dropTable('form_rows');
		$data->dropTable('form_values');
		$data->dropTable('form_sidebars');
	}
	$data->createTable('forms',$structures['forms'],false);
	$data->createTable('form_fields',$structures['form_fields'],false);
	$data->createTable('form_rows',$structures['form_rows'],false);
	$data->createTable('form_values',$structures['form_values'],false);
	$data->createTable('form_sidebars',$structures['form_sidebars'],false);
}
?>