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
$this->formPrefix='sideBarEdit_';
$this->caption='Create New SideBar';
$this->submitTitle='Save Changes';
$this->fromForm='sideBarEdit';
$this->fields=array(
	'name' => array(
		'label' => 'Name',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Name</b> - Used for reference in the admin panel. Must be unique.
		'
	),
	'title' => array(
		'label' => 'Title',
		'required' => false,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Title</b> - The title displayed over the sidebar on the user end.
			</p>
		'
	),
	'titleURL' => array(
		'label' => 'Title URL',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Title URL</b> - Optional Field that will turn the title into a link. Should be a full url - IF the first character is a vertical break (|) then the \'linkRoot\' will be appended before it -- in other words a local link in the CMS.
			</p>
		'
	),
	'side' => array(
		'label' => 'Side',
		'tag' => 'select',
		'options' => array(
			array(
				'value' => 'left',
				'text' => 'left'
			),
			array(
				'value' => 'right',
				'text' => 'right',
			)
		)
	),
	'rawContent' => array(
		'label' => 'Content Text',
		'tag' => 'textarea',
		'required' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'useEditor' => true,
		'description' => '
			<p>
				<b>Quote Text</b> - The actual text that will go inside the automatically generated <code>blockquote</code> tag. HTML is allowed in here, though any sub-headings you wish to use should start at H3, since H2 is already in use.
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContent')
	)
);
