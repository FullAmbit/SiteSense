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
$this->enctype = 'multipart/form-data';
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
				<b>Blog Name</b> - The name used for creating the URL. It must be unique!
			</p>
		',
		'cannotEqual' => array()
	),
	'title' => array(
		'label' => 'Blog Title',
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Blog Title</b> - The title of this blog
			</p>
		',
		'cannotEqual' => array()
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
	'picture' => array(
		'contentAfter' => (is_numeric($data->action[3])) ? '<a href="'.$data->settings['cdnLarge'].$data->themeDir.'images/blogs/'.$data->output['blogItem']['shortName'].'/rss.jpg">Current Image</a>' : '',
		'required' => false,
		'label' => 'RSS Channel Image',
		'tag' => 'input',
		'params' => array(
			'type' => 'file',
		),
		'images' => array(
			
			'full' => array(
				'mandateType' => array(
					'jpeg'
				),
				'path' => (is_numeric($data->action[3])) ?'themes/'.$data->settings['theme'].'/images/blogs/'.$data->output['blogItem']['shortName'] : 'themes/'.$data->settings['theme'].'/images/blogs/tmp',
				'overwrite' => true,
				'maxsize' => array(
					'width' => 1404,
					'height' => 4000
				),
				'customName' => 'rss',
				
			)
		)
	),
	'managingEditor' => array(
		'label' => 'Managing Editor',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '<p><b>RSS Managing Editor</b> - The managing editor email that will be displayed on the RSS feed.</p>'
	),
	'webMaster' => array(
		'label' => 'Web Master',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '<p><b>RSS WebMaster</b> - The webmaster email that will be displayed on the RSS feed.</p>'
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
	'topLevel' => array(
		'label' => 'Make Blog A Top-Level Page?',
		'tag' => 'input',
		'checked' => ((isset($data->output['topLevel']) && $data->output['topLevel']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				Do you want to access this blog by /blogname as an alternative to /blogs/blogname ?
			</p>
		'
	),
	'allowComments' => array(
		'label' => 'Allow Comments',
		'tag' => 'input',
		'params' => array(
			'type' => 'checkbox',
		),
		'description' => '
			<p>
				<b>Allow Comments</b> - Allow comments in blog?
			</p>
		'
	),
	/*'minPermission' => array(
		'label' => 'Minimum Permissions',
		'tag' => 'select',
		'options' => array(),
		'description' => '
			<p>
				<b>Minimum Permissions</b> - Lowest ranked user group allowed to edit/post to this blog.
			</p>
		'
	),*/
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
	'rssOverride' => array(
		'label' => 'RSS Link Override',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<b>RSS Link Override</b> - Entering a link here will change the links of the "Subscribe" buttons on the blog pages. This is useful, for example, for a Feedburner URL.
			</p>
		',
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
