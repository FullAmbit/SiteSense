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
$this->caption='Create New Static Page';
$this->submitTitle='Save Changes';
$this->fromForm='pagesEdit';
$this->fields=array(
	'name' => array(
		'label' => 'Page Name',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 128,
		),
		'description' => '
			<p>
				<b>Page Name</b> - Must be unique. Used for the URL.
			</p>
		',
		'cannotEqual' => ''
	),
	'title' => array(
		'label' => 'Page Title',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Page Title</b> - Used as the content of the heading tag for this text.
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
		'label' => 'Parent Page',
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
				<b>Parent Page</b> - If sidebar navigation is enabled this sub-page will appear in a separate menu off the parent. If you choose to enable "show on parent" the sub-page will appear as a second contentBox and H2 below the parent. The order child pages are shown can be controlled from the \'list\' page.
			</p>
		'
	),
	'showOnMenu' => array(
		'label' => 'Create Main Menu Link',
		'tag' => 'input',
		'hideChild' => 'menuTitle',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Show on Menu</b> - Adds this static page to the main menu
			</p>
		'
	),
	'menuParent' => array(
		'label' => "Parent Menu Item",
		'tag' => "select",
		'options' => array(
			array(
				'value' => 0,
				'text' => 'Site Root'
			)
		)
	),
	'menuText' => array(
		'label' => 'Menu Link Text',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => '128',
			'maxLength' => '128'
		),
		'description' => '
			<p>
				<b>Menu Title</b> - if this element is to be shown on the menu, this is the text that will be shown inside it\'s anchor.
			</p>
		'
	),
	'rawContent' => array(
		'label' => 'Page Content',
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
				<b>Page Content</b> - The actual text that will go inside the automatically generated \'contentBox\'. HTML is allowed in here, though any sub-headings you wish to use should start at H3, since \'Page Title\' is used as the h2.
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContent')
	),
	'live' => array(
		'label' => 'Live',
		'tag' => 'input',
		'checked' => '',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Live</b> - By default new blog entries are \'hidden\' from normal users until you check this box or enable them on the blog post list.
			</p>
		'
	)
);
?>