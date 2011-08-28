<?php

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
	$target='admin/blogs.include.'.$data->action[2].'.php';
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