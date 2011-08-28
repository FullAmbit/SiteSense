<?php

$this->action=$data->linkRoot.'admin/blogs/edit/'.(
	(is_numeric($data->action[3])) ?
	$data->action[3] : 'new'
);

$this->formPrefix='blogEdit_';
$this->caption='Create New Blog';
$this->submitTitle='Save Changes';
$this->fromForm='blogEdit';

$this->fields=array(
	'name' => array(
		'label' => 'Blog Name',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Blog Name</b> - The title of this blog
			</p>
		'
	),
	'owner' => array(
		'label' => 'Blog Owner',
		'tag' => 'select',
		'options' => array(
			array(
				'value' => 0,
				'text' => 'NONE'
			)
		),
		'description' => '
			<p>
				<b>Blog Owner</b> - Who owns this blog, will always have post/edit/reply approval rights regardless of user access level.
			</p>
		'
	),
	'commentsRequireLogin' => array(
		'label' => 'Require login to comment?',
		'tag' => 'input',
		'checked' => ((isset($data->output['commentsRequireLogin']) && $data->output['commentsRequireLogin']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				Do you want readers to have to log-in before being able to comment on this blog? 
			</p>
		'
	),
	'minPermission' => array(
		'label' => 'Minimum Permissions',
		'tag' => 'select',
		'options' => array(),
		'description' => '
			<p>
				<b>Minimum Permissions</b> - Lowest ranked user group allowed to edit/post to this blog.
			</p>
		'
	),
	'numberPerPage' => array(
		'label' => 'Number Per Page',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 2
		),
		'description' => '
			<p>
				<b>Number Per Page</b> - The number of post entries to show on a page
			</p>
		'
	),
	'description' => array(
		'label' => 'Description',
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['content']) ? $data->output['content'] : '',
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>
				<b>Description</b> - Just some short text to describe this blog. Not actually used anywhere the end user can see it (yet)
			</p>
		'
	)
);
