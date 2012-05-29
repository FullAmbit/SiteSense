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
$this->caption = 'Ban User: ' . $data->output['userItem']['name'];
$this->submitTitle = 'Ban User';

$this->fields = array(
	'banTime' => array(
		'label' => 'Ban Time',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'maxlength' => 2
		)
	),
	'banUnit' => array(
		'label' => 'Ban Time Unit',
		'tag' => 'select',
		'options' => array(
			array(
				'value' => 1,
				'text' => 'seconds'
			),
			array(
				'value' => 60,
				'text' => 'minutes'
			),
			array(
				'value' => 3600,
				'text' => 'hours'
			),
			array(
				'value' => 84600,
				'text' => 'days'
			),
			array(
				'value' => 259200,
				'text' => 'months'
			),
			array(
				'value' => 31104000,
				'text' => 'years'
			)
		)
	),
	'banEmail' => array(
		'label' => 'Ban Email Address',
		'tag' => 'select',
		'options' => array(
			array(
				'value' => 0,
				'text' => 'No'
			),
			array(
				'value' => 1,
				'text' => 'Yes'
			)
		),
		'value' => 1
	),
	'banIp' => array(
		'label' => 'Ban IP Address',
		'tag' => 'select',
		'options' => array(
			array(
				'value' => 0,
				'text' => 'No'
			),
			array(
				'value' => 1,
				'text' => 'Yes'
			)
		),
		'value' => 1
	),
);
?>