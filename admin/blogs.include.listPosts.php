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
function admin_blogsBuild($data,$db) {
	if (is_numeric($data->action[3])) {
		//---If You're a Blogger, You Can Only Load Your OWN Blog--//
		if($data->user['userLevel'] < USERLEVEL_MODERATOR)
		{
			$check = $db->prepare('getBlogByIdAndOwner','admin_blogs');
			$check->execute(array(
				':id' => $data->action[3],
				':owner' => $data->user['id']
			));
		} else {
			$check = $db->prepare('getBlogById','admin_blogs');
			$check->execute(array(':id' => $data->action[3]));
		}
		// Check For Results
		if(($data->output['parentBlog'] = $check->fetch()) === FALSE)
		{
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>The ID does not exist in database</h2>';
			return;
		}
		
		$statement=$db->prepare('countBlogPostsByBlogId','admin_blogs');
		$statement->execute(array(
			':id' => $data->output['parentBlog']['id']
		));
		if ($count=$statement->fetch()) {
			$data->output['blogStart']=(
				is_numeric($data->action[4]) ?
				$data->action[4] :
				0
			);
			$data->output['blogLimit']=ADMIN_SHOWPERPAGE;
			$data->output['blogsCount']=$count['count'];
			$statement=$db->prepare('getBlogPostsByBlogIdLimited','admin_blogs');
			/* limit only works with bind, damned if I know why */
			$statement->bindParam(':blogId',$data->output['parentBlog']['id'],PDO::PARAM_INT);
			$statement->bindParam(':blogStart',$data->output['blogStart'],PDO::PARAM_INT);
			$statement->bindParam(':blogLimit',$data->output['blogLimit'],PDO::PARAM_INT);
			$statement->execute();
			$data->output['blogPosts']=$statement->fetchAll();
		}
	}
}
function admin_blogsShow($data) {
	$aRoot=$data->linkRoot.'admin/blogs/';
	theme_pagination(
		$data->output['blogsCount'],
		$data->output['blogsStart'],
		$aRoot.'list/'
	);
	theme_blogsListPostsHead($data,$aRoot);
	if (empty($data->output['blogPosts'])) {
		theme_blogsListPostsNoPosts();
	} else {
		theme_blogsListsPostsTableHead($data);
		$count=0;
		foreach ($data->output['blogPosts'] as $item) {
			theme_blogsListPostsTableRow($item,$aRoot,$count);
			$count++;
		}
	theme_blogsListPostsTableFoot();
	}
	theme_pagination(
		$data->output['blogsCount'],
		$data->output['blogsStart'],
		$aRoot.'list/'
	);
	theme_blogsListPostsFoot($data,$aRoot);
}
?>
