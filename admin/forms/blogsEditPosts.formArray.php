<?php

$this->action=$data->linkRoot.'admin/blogs/editPosts/'.$data->action[3].'/'.(
	(is_numeric($data->action[4])) ?
	$data->action[4] : 'new'
);

$this->formPrefix='blogEdit_';
$this->caption='Create New Blog Post For '.$data->output['parentBlog']['name'];
$this->submitTitle='Save Changes';
$this->fromForm='blogEdit';

$this->fields=array(
	'title' => array(
		'label' => 'Post title',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Post title</b> -  Shown as the Heading for this post and in the TITLE attribute on the page.
			</p>
		'
	),
	'summary' => array(
		'label' => 'Summary',
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['summary']) ? $data->output['summary'] : '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>???
				<b>Summary</b> - What will the user see when viewing the list of posts?
			</p>
		'
	),
	'content' => array(
		'label' => 'Content',
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['content']) ? $data->output['content'] : '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>???
				<b>Content</b> - Pretty self explanatory; The content of the post you are writing/editing.
			</p>
		'
	),
	'live' => array(
		'label' => 'Live',
		'tag' => 'input',
		'checked' => ((isset($data->output['live']) && $data->output['live']) ? 'checked' : ''),
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