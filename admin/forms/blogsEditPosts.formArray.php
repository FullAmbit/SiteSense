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
if(!isset($data->output['blogItem']))
{
	$checked = ($data->output['parentBlog']['allowComments'] == '1') ? 'checked' : '';
} else {
	$checked = '';
}

$this->formPrefix='blogEdit_';
$this->caption='Create New Blog Post For '.$data->output['parentBlog']['name'];
$this->submitTitle='Save Changes';
$this->fromForm='blogEdit';
$this->fields=array(
	'name' => array(
		'label' => 'Post Name',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Post Name</b> -  Used to generate the URL. Must be unique!
			</p>
		'
	),
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
	'tags' => array(
		'label' => 'Tags',
		'required' => false,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<bTags</b> - Assign tags to the post. Seperate each tag with a comma, no spaces.
			</p>
		'
	),
	'categoryId' => array(
		'label' => 'Categories',
		'tag' => 'select',
		'options' => array(
			array(
				'value' => '0',
				'text' => 'No Category'
			)
		)
	),
	'rawSummary' => array(
		'label' => 'Summary',
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['rawSummary']) ? $data->output['rawSummary'] : '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>???
				<b>Summary</b> - What will the user see when viewing the list of posts?
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawSummary')
	),
	'rawContent' => array(
		'label' => 'Content',
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['rawContent']) ? $data->output['rawContent'] : '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>???
				<b>Content</b> - Pretty self explanatory; The content of the post you are writing/editing.
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContent')
	),
	'allowComments' => array(
		'label' => 'Allow Comments',
		'tag' => 'input',
		'value' => '1',
		'checked' => $checked,
		'params' => array(
			'type' => 'checkbox'
		)
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