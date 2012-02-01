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
function pages_settings($data)
{
	return array(
		'name' => 'pages',
		'shortName' => 'pages'
	);
}
function pages_install($data,$drop=false) {
	$structures = array(
		'pages' => array(
			'id'										 => SQR_IDKey,
			'shortName'							 => SQR_shortName,
			'name'									 => SQR_name,
			'title'									 => SQR_title,
			'rawContent'						 => 'MEDIUMTEXT NOT NULL',
			'parsedContent'					 => 'MEDIUMTEXT NOT NULL',
			'parent'								 => SQR_ID,
			'sortOrder'							 => SQR_sortOrder.' DEFAULT \'1\'',
			'live'									 => SQR_boolean,
			'KEY `shortName` (`shortName`,`parent`,`sortOrder`)'
		),
		'pages_sidebars' => array(
			'id'										 => SQR_IDKey,
			'page'									 => SQR_ID,
			'sidebar'								 => SQR_ID,
			'enabled'								 => SQR_boolean,
			'sortOrder'							 => SQR_sortOrder.' DEFAULT \'1\'',
			'UNIQUE KEY `module` (`page`,`sidebar`)'
		)
	);
	if($drop) {
		$data->dropTable('pages');
		$data->dropTable('pages_sidebars');
	}
	$data->createTable('pages',$structures['pages'],true);
	$data->createTable('pages_sidebars',$structures['pages_sidebars'],true);
	if($data->countRows('pages')==0) {
		try {
			echo '
				<h3>Attempting:</h3>';
			$data->exec('makeRegistrationAgreement','installer');
			echo '
				<div>
					Registration Agreement Page Generated!
				</div><br />
			';
		} catch(PDOException $e) {
			$data->installErrors++;
			echo '
				<h2>Failed to create registration agreement!</h2>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
		try {
			echo '
				<h3>Attempting:</h3>';
			$data->exec('makeRegistrationEMail','installer');
			echo '
				<div>
					Registration E-Mail Page Generated!
				</div><br />
			';
		} catch(PDOException $e) {
			$data->installErrors++;
			echo '
				<h2>Failed to create registration E-Mail!</h2>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
	} else echo '<p class="exists">"pages database" already contains records</p>';
}
?>