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
$this->caption='Create/Edit Module';
$this->submitTitle='Save Module';
$this->fields=array(
	'text' => array(
		'label' => 'Text',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['menuItem']['text']) ? $data->output['menuItem']['text'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
		'description' => '
			<p>
				<b>Text</b> - What will the user see as the text on the menu item?
			</p>
		'
	),
	'title' => array(
		'label' => 'Title',
		'tag' => 'input',
		'required' => false,
		'value' => isset($data->output['menuItem']['title']) ? $data->output['menuItem']['title'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Title</b> - What will the user see in the tooltip when they keep their mouse over the menu item?
			</p>
		'
	),
	'url' => array(
		'label' => 'URL',
		'tag' => 'input',
		'value' => isset($data->output['menuItem']['url']) ? $data->output['menuItem']['url'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>URL</b> - Where is this menu item linking to? Placing a \'|\' at the beginning will prepend your local URL.
			</p>
		'
	),
	/*'module' => array(
		'label' => 'Module',
		'tag' => 'input',
		'required' => true,
		'value' => isset($data->output['menuItem']['module']) ? $data->output['menuItem']['module'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Module</b> - Which module is this associated with?
			</p>
		'
	),*/
	'enabled' => array(
		'label' => 'Enable?',
		'tag' => 'input',
		'value' => 1,
		'checked' => (isset($data->output['menuItem']['enabled']) && $data->output['menuItem']['enabled'] == 1) ? 'checked' : '',
		'params' => array(
			'type' => 'checkbox',
		),
		'description' => '
			<p>
				<b>Enabled</b> - Will the user see this menu item?
			</p>
		'
	),
	'parent' => array(
		'label' => 'Parent Menu Item',
		'tag' => 'select',
		'options' => array(
			array(
				'text' => '-- none --',
				'value' => 0
			)
		),
		'value' => (isset($data->output['menuItem']['parent'])) ? $data->output['menuItem']['parent'] : ''
	)
);
