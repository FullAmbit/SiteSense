<?php

$this->action=$data->linkRoot.'admin/sideBars/edit/'.(
	(is_numeric($data->action[3])) ?
	$data->action[3] : 'new'
);

$this->formPrefix='sideBarEdit_';
$this->caption='Create New SideBar';
$this->submitTitle='Save Changes';
$this->fromForm='sideBarEdit';

$this->fields=array(
	'title' => array(
		'label' => 'Title',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Title</b> - Used to generate the shortname used in the URI, as well as
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
	'content' => array(
		'label' => 'Content Text',
		'tag' => 'textarea',
		'required' => true,
		'value' => $data->output['content'],
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>
				<b>Quote Text</b> - The actual text that will go inside the automatically generated <code>blockquote</code> tag. HTML is allowed in here, though any sub-headings you wish to use should start at H3, since H2 is already in use.
			</p>
		'
	)
);
