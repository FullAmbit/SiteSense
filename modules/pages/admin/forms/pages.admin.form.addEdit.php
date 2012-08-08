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
$this->formPrefix='pageEdit_';
$this->caption=$data->phrases['pages']['captionAddPage'];
$this->submitTitle=$data->phrases['pages']['submitAddEditForm'];
$this->fromForm='pagesEdit';
$this->fields=array(
	'name' => array(
		'label' => $data->phrases['pages']['labelAddEditName'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 128,
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditName'].'</b><br />
				'.$data->phrases['pages']['descriptionAddEditName'].'
			</p>
		',
		'cannotEqual' => ''
	),
	'title' => array(
		'label' => $data->phrases['pages']['labelAddEditTitle'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditTitle'].'</b><br />
				'.$data->phrases['pages']['descriptionAddEditTitle'].'
			</p>
		'
	),
/*
	'keywords' => array(
		'label' => 'Keywords Meta',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Keywords Meta</b> - Displayed in the keywords META tag, good for SEO. If left blank the master CMS keywords will be used instead.
			</p>
		'
	),
*/
	'parent' => array(
		'label' => $data->phrases['pages']['labelAddEditParent'],
		'tag' => 'select',
		'value' => 0,
		'options' => array(
			array(
				'text' => '-- none --',
				'value' => 0
			),
			array(
				'text' => '-- homepage --',
				'value' => -0x1000
			),
			array(
				'text' => '-- sidebar --',
				'value' => -0x500
			)
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditParent'].'</b><br />
				'.$data->phrases['pages']['descriptionAddEditParent'].'
			</p>
		'
	),
	'showOnMenu' => array(
		'label' => $data->phrases['pages']['labelAddEditShowOnMenu'],
		'tag' => 'input',
		'hideChild' => 'menuTitle',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditShowOnMenu'].'</b><br />
				'.$data->phrases['pages']['descriptionAddEditShowOnMenu'].'
			</p>
		'
	),
	'menuParent' => array(
		'label' => $data->phrases['pages']['labelAddEditMenuParent'],
		'tag' => "select",
		'options' => array(
			array(
				'value' => 0,
				'text' => 'Site Root'
			)
		)
	),
	'menuText' => array(
		'label' => $data->phrases['pages']['labelAddEditMenuText'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => '128',
			'maxLength' => '128'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditMenuText'].'</b> <br />
				'.$data->phrases['pages']['descriptionAddEditMenuText'].'
			</p>
		'
	),
	'rawContent' => array(
		'label' => $data->phrases['pages']['labelAddEditRawContent'],
		'tag' => 'textarea',
		'required' => true,
		'value' => '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditRawContent'].'</b><br />
				'.$data->phrases['pages']['descriptionAddEditRawContent'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContent')
	),
	'live' => array(
		'label' => $data->phrases['pages']['labelAddEditLive'],
		'tag' => 'input',
		'checked' => '',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['pages']['labelAddEditLive'].'</b><br />
				'.$data->phrases['pages']['descriptionAddEditLive'].'
			</p>
		'
	)
);
?>