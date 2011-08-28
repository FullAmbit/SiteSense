<?php

$this->action=$data->linkRoot.'admin/pages/edit/'.(
	(is_numeric($data->action[3])) ?
	$data->action[3] : 'new'
);

$this->formPrefix='pageEdit_';
$this->caption='Create New Static Page';
$this->submitTitle='Save Changes';
$this->fromForm='pagesEdit';

$this->fields=array(
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
				<b>Page Title</b> - Used as the content of the heading tag for this text, it is also used
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
	'showOnParent' => array(
		'label' => 'Show on Parent',
		'tag' => 'input',
		'hideChild' => 'parent',
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Show on Parent</b> - makes this "page" appear as a second contentBox after the parent page\'s content.
			</p>
		'
	),
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
		'label' => 'Show on Menu',
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
	'menuTitle' => array(
		'label' => 'Menu Title',
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
	'content' => array(
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
		'
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