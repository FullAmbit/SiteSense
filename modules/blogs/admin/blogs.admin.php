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
function admin_buildContent($data,$db) {
	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	if ($data->action[2]=='list') {
		$statement=$db->query('getAllBlogs','admin_blogs');
		$data->output['blogList']=$statement->fetchAll();
		$statement=$db->prepare('countBlogPosts','admin_blogs');
		foreach ($data->output['blogList'] as $item) {
			$statement->execute(array(
				':blogId' => $item['id']
			));
		}
	}
	$target='blogs/admin/includes/blogs.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_blogsBuild')) admin_blogsBuild($data,$db);
	$data->output['pageTitle']='Blogs';
}
function admin_content($data) {
	if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_blogsShow($data);
		} else admin_unknown();
	}
}
?>