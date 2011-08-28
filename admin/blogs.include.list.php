<?php

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
		$statement=$db->prepare('getBlogsByOwner','admin_blogs');
		/*
			limit only works with bind, damned if I know why
		*/
		$statement->bindParam(':blogStart',$data->output['blogStart'],PDO::PARAM_INT);
		$statement->bindParam(':blogLimit',$data->output['blogLimit'],PDO::PARAM_INT);
		$statement->execute();
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
	echo '
		<h2 class="preTable">List of current Blogs</h2>';
	$aRoot=$data->linkRoot.'admin/blogs/';
	theme_pagination(
		$data->output['blogsCount'],
		$data->output['blogsStart'],
		$aRoot.'list/'
	);
	echo '
			<div class="navPanel buttonList">
				<a href="',$aRoot,'edit/new">
					Add New Blog
				</a>
			</div>';

	if (empty($data->output['blogs'])) {
		echo '
			<p class="pageListNoPages">No Blogs exist</p>';
	} else {
		echo '
			<table class="pagesList">
				<thead>
					<tr>
						<th class="shortName">Blog Title</th>
						<th class="module">Owner</th>
						<th class="module">Entries</th>
						<th class="controls">Controls</th>
					</tr>
				</thead><tbody>';
		$count=0;
		foreach ($data->output['blogs'] as $item) {
			echo '
					<tr class="',($count%2==0 ? 'odd' : 'even'),'">
						<td class="shortName">
							<a href="'.$aRoot.'listPosts/'.$item['id'].'">
								',$item['shortName'],'
							</a>
						</td>
						<td class="module">
							',$item['ownerName'],'
						<td class="module">',$item['count'],'</td>
						<td class="buttonList">
							<a href="'.$aRoot.'editPosts/'.$item['id'].'/new">New Post</a>
							<a href="',$aRoot,'edit/',$item['id'],'">Edit Description</a>
							<a href="'.$aRoot.'delete/'.$item['id'].'">Delete</a>
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
				<a href="',$aRoot,'edit/new">
					Add New Blog
				</a>
			</div>';

}

?>