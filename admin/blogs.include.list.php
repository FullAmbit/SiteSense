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
	$statement=$db->query('countBlogs','admin_blogs');
	if ($count=$statement->fetch()) {
		$data->output['blogStart']=(
			is_numeric($data->action[3]) ?
			$data->action[3] :
			0
		);
		$data->output['blogLimit']=ADMIN_SHOWPERPAGE;
		$data->output['blogsCount']=$count['count'];
		//---If Less Then Moderator, Load Only OWN Blog Posts
		if(checkPermission('canListBlogPosts','blogs',$data))
		{
			$statement = $db->prepare('getBlogsByUser','admin_blogs');
			$statement->bindParam(':blogStart',$data->output['blogStart'],PDO::PARAM_INT);
			$statement->bindParam(':blogLimit',$data->output['blogLimit'],PDO::PARAM_INT);
			$statement->bindParam(':owner',$data->user['id'],PDO::PARAM_INT);
			$statement->execute();
		} elseif (checkPermission('canListOthersBlogPosts','blogs',$data)) {
			$statement=$db->prepare('getBlogsByOwner','admin_blogs');
			$statement->bindParam(':blogStart',$data->output['blogStart'],PDO::PARAM_INT);
			$statement->bindParam(':blogLimit',$data->output['blogLimit'],PDO::PARAM_INT);
			$statement->execute();
		}
		/*
			limit only works with bind, damned if I know why
		*/
		
		$data->output['blogs']=$statement->fetchAll();
		/*
			we can't do a joined query -- why? Because blogs without any
			corresponding blogPosts wouldn't show up in our results
			set! So we're stuck pulling the above list and then
			iterating through it to pull our post counts
			It's either that or run two queries...
		*/
		$statement=$db->prepare('countBlogPostsByBlogId','admin_blogs');
		$getBlogOwner=$db->prepare('pullUserInfoById','common');
		/* we also can't foreach, as we need to actually CHANGE it's values */
		for ($t=0; $t<count($data->output['blogs']); $t++) {
			if ($data->output['blogs'][$t]['owner']==0) {
				$data->output['blogs'][$t]['ownerName']='NONE';
			} else {
				$getBlogOwner->execute(array(
					':userId' => $data->output['blogs'][$t]['owner']
				));
				$owner=$getBlogOwner->fetch();
				$data->output['blogs'][$t]['ownerName']=$owner['name'];
			}
			$statement->execute(array(
				':id' => $data->output['blogs'][$t]['id']
			));
			if ($count=$statement->fetch()) {
				$data->output['blogs'][$t]['count']=$count['count'];
			} else {
				$data->output['blogs'][$t]['count']='-';
			}
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
	theme_blogsListHead($aRoot);
	if (empty($data->output['blogs'])) {
		theme_blogsListNoBlogs();
	} else {
		theme_blogsListTableHead();
		$count=0;
		foreach ($data->output['blogs'] as $item) {
			theme_blogsListTableRow($item,$aRoot,$count);
			$count++;
		}
	theme_blogsListTableFoot();
	}
	theme_pagination(
		$data->output['blogsCount'],
		$data->output['blogsStart'],
		$aRoot.'list/'
	);
	theme_blogsListFoot($aRoot);
}
?>