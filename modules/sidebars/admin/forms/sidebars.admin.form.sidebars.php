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
$this->formPrefix='sidebarEdit_';
$this->caption=$data->phrases['sidebars']['captionSidebarsAdd'];

$this->submitTitle=$data->phrases['sidebars']['submitSidebarsForm'];
$this->fromForm='sidebarEdit';
$this->fields=array(
	'name' => array(
		'label' => $data->phrases['sidebars']['labelSidebarsName'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['sidebars']['labelSidebarsName'].'</b><br />
				'.$data->phrases['sidebars']['descriptionSidebarsName'].'
		'
	),
	'title' => array(
		'label' => $data->phrases['sidebars']['labelSidebarsTitle'],
		'required' => false,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['sidebars']['labelSidebarsTitle'].'</b><br />
				'.$data->phrases['sidebars']['descriptionSidebarsTitle'].'
			</p>
		'
	),
	'titleURL' => array(
		'label' => $data->phrases['sidebars']['labelSidebarsTitleURL'],
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['sidebars']['labelSidebarsTitleURL'].'</b><br />
				'.$data->phrases['sidebars']['descriptionSidebarsTitleURL'].'
			</p>
		'
	),
	'side' => array(
		'label' => $data->phrases['sidebars']['labelSidebarsSide'],
		'tag' => 'select',
		'options' => array(
			array(
				'value' => $data->phrases['sidebars']['left'],
				'text'  => $data->phrases['sidebars']['left']
			),
			array(
				'value' => $data->phrases['sidebars']['right'],
				'text'  => $data->phrases['sidebars']['right']
			)
		)
	),
	'rawContent' => array(
		'label' => $data->phrases['sidebars']['labelSidebarsRawContent'],
		'tag' => 'textarea',
		'required' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'useEditor' => true,
		'description' => '
			<p>
				<b>'.$data->phrases['sidebars']['labelSidebarsRawContent'].'</b><br />
				'.$data->phrases['sidebars']['descriptionSidebarsRawContent'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContent')
	)
);
