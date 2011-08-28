<?php

function admin_blogsBuild($data,$db) {
	if (is_numeric($data->action[3])) {
		$statement=$db->prepare('getBlogById','admin_blogs');
		$statement->execute(array(
			':id' => $data->action[3]
		));
		$data->output['parentBlog']=$statement->fetch();

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
	echo '
		<h2 class="preTable">Posts Currently in the "',$data->output['parentBlog']['name'],'" Blog</h2>';
	theme_pagination(
		$data->output['blogsCount'],
		$data->output['blogsStart'],
		$aRoot.'list/'
	);
	echo '
			<div class="navPanel buttonList">
				<a href="',$aRoot,'editPosts/'.$data->output['parentBlog']['id'].'/new">
					Add New Post
				</a>
				<a href="'.$aRoot.'list">
					Back to Blog List
				</a>
			</div>';

	if (empty($data->output['blogPosts'])) {
		echo '
			<p class="pageListNoPages">No Blogs exist</p>';
	} else {
		echo '
			<table class="pagesList">
				<thead>
					<tr>
						<th class="title">Post Title</th>
						<th class="date">Date Created</th>
						<th class="date">Last Edited</th>
						<th class="controls">Controls</th>
					</tr>
				</thead><tbody>';
		$count=0;
		foreach ($data->output['blogPosts'] as $item) {
			echo '
					<tr class="',($count%2==0 ? 'odd' : 'even'),'">
						<td class="title">
							<a href="',$aRoot,'editPosts/',$item['blogId'],'/',$item['id'],'">
								',$item['shortName'],'
							</a>
						</td>
						<td class="date">
							<span>',date('d M Y',$item['postTime']),'</span>
							<span>',date('H:i T',$item['postTime']),'</span>
						</td>
						<td class="date">',(
							empty($item['modifiedTime']) ? '' : '
							<span>'.date('d M Y',$item['modifiedTime']).'</span>
							<span>'.date('H:i T',$item['modifiedTime']).'</span>
						'),'</td>
						<td class="buttonList">
							<a href="'.$aRoot.'deletePosts/',$item['id'],'">Delete</a>
						</td>
					</tr>';

			$count++;
		}
	echo '
				</tbody>
			</table>';
	}

	theme_pagination(
		$data->output['blogsCount'],
		$data->output['blogsStart'],
		$aRoot.'list/'
	);
	echo '
			<div class="navPanel buttonList">
				<a href="',$aRoot,'editPosts/'.$data->output['parentBlog']['id'].'/new">
					Add New Post
				</a>
				<a href="'.$aRoot.'list">
					Back to Blog List
				</a>
		</div>';

}

?>