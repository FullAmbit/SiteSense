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
function theme_blogsDeleteDeleted($data,$aRoot) {
	echo '
		<h2>Entry #',$data->action[3],' - ',$data->output['thisBlog']['name'],' Deleted</h2>
		<p>
			This action deleted a total of ',$data->output['deleteCount'],' blogs containing ',$data->output['deletePostCount'],' Posts!
		</p>
		<div class="buttonList">
			<a href="',$aRoot,'list">Return to List</a>
		</div>
		';
}

function theme_blogsDeleteCancelled($aRoot) {
	echo '
		<h2>Deletion Cancelled</h2>
		<p>
			You should be auto redirected to the blog list in three seconds.
			<a href="',$aRoot,'list">Click Here if you don not wish to wait.</a>
		</p>';
}

function theme_blogsDeleteDefault($data,$aRoot) {
	echo '
		<form action="',$aRoot,'delete/',$data->action[3],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>Are you sure you want to delete blog #',$data->action[3],' "',$data->output['thisBlog']['name'],'"?</span></legend>
				<input type="submit" name="delete" value="Yes, Delete it" />
				<input type="submit" name="cancel" value="Cancel" />
				<input type="hidden" name="fromForm" value="',$data->action[3],'" />
			</fieldset>
		</form>';
}

function theme_blogsDeleteCatDeleted($data,$aRoot) {
	echo 'This category has been deleted. <div class="buttonList"><a href="'.$aRoot.'listCategories/'.$data->output['blogItem']['id'].'" title="Return To Categories">Return to blogs.</a></div>';
}

function theme_blogsDeleteCatCancelled($aRoot) {
	echo '<h2>Deletion Cancelled</h2><p>You should be auto redirected to the blogs list in three seconds. <a href="',$aRoot,'list">Click Here if you do not wish to wait.</a></p>';
}

function theme_blogsDeleteCatDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteCategory/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>Are you sure you want to delete this category?</legend>
			</fieldset>
			<input type="submit" name="delete" value="Yes, Delete it" />
			<input type="submit" name="cancel" value="Cancel" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_blogsDeleteCommentDeleted($aRoot) {
	echo 'This comment has been deleted. <div class="buttonList"><a href="'.$aRoot.'" title="Return To Blogs">Return to blogs.</a></div>';
}

function theme_blogsDeleteCommentCancelled($aRoot) {
	echo '<h2>Deletion Cancelled</h2><p>You should be auto redirected to the blog list in three seconds. <a href="',$aRoot,'list">Click Here if you don not wish to wait.</a></p>';
}

function theme_blogsDeleteCommentDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteComment/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>Are you sure you want to delete this comment?</legend>
			</fieldset>
			<input type="submit" name="delete" value="Yes, Delete it" />
			<input type="submit" name="cancel" value="Cancel" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_blogsDeletePostsDeleted($data,$aRoot) {
	echo '
		<h2>Entry #',$data->action[3],' Deleted</h2>
		<p>
			This action deleted a total of ',$data->output['deleteCount'],' post items!
		</p>
		<div class="buttonList">
			<a href="',$aRoot,'listPosts/',$data->output['thisBlog']['blogId'],'">Return to List</a>
		</div>
		';
}

function theme_blogsDeletePostsCancelled($data,$aRoot) {
	echo '
		<h2>Deletion Cancelled</h2>
		<p>
			You should be auto redirected to the post list in three seconds.
			<a href="',$aRoot,'listPosts/',$data->output['thisBlog']['blogId'],'">Click Here if you don not wish to wait.</a>
		</p>';
}

function theme_blogsDeletePostsDefault($data,$aRoot) {
	echo '
		<form action="',$aRoot,'deletePosts/',$data->action[3],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>Are you sure you want to delete blog post id#',$data->action[3],'?</span></legend>
				<input type="submit" name="delete" value="Yes, Delete it" />
				<input type="submit" name="cancel" value="Cancel" />
				<input type="hidden" name="fromForm" value="',$data->action[3],'" />
			</fieldset>
		</form>';
}

function theme_blogsListHead($aRoot) {
	echo '
		<div class="navPanel buttonList">
			<a href="',$aRoot,'add">
				Add New Blog
			</a>
		</div>';
}

function theme_blogsListNoBlogs() {
	echo '
		<p class="blogsListNoBlogs">No Blogs exist</p>';
}

function theme_blogsListTableHead() {
	echo '
		<table class="blogsList">
		<caption>Manage Blogs</caption>
			<thead>
				<tr>
					<th class="shortName">Blog Title</th>
					<th class="module">Owner</th>
					<th class="module">Entries</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
}

function theme_blogsListTableRow($item,$aRoot,$count) {
	echo '
		<tr class="',($count%2==0 ? 'odd' : 'even'),'">
			<td class="shortName">
				<a href="'.$aRoot.'listPosts/'.$item['id'].'">
					',$item['title'],'
				</a>
			</td>
			<td class="module">
				',$item['ownerName'],'
			<td class="module">',$item['count'],'</td>
			<td class="buttonList">
				<a href="'.$aRoot.'addPost/'.$item['id'].'/new">New Post</a>
				<a href="',$aRoot,'edit/',$item['id'],'">Modify Blog</a>
				<a href="'.$aRoot.'delete/'.$item['id'].'">Delete</a>
			</td>
		</tr>';
}

function theme_blogsListTableFoot() {
	echo '
			</tbody>
		</table>';
}

function theme_blogsListFoot($aRoot) {
	echo '
		<div class="navPanel buttonList">
			<a href="',$aRoot,'add">
				Add New Blog
			</a>
		</div>';
}

function theme_blogsListCatTableHead($data,$aRoot) {
	echo '
		<h2 class="preTable">Categories of the blog titled ',$data->output['blogItem']['name'],'</h2>
		<div class="navPanel buttonList">
			<a href="',$aRoot,'addCategory/',$data->output['blogItem']['id'],'">Add Category</a>
		</div>
		<table class="pagesList">
			<thead>
				<tr>
					<th class="title">Category Title</th>
					<th class="controls">Controls</th>
				</tr>
			</thead>
		';
}

function theme_blogsListCatNoCats() {
	echo '
			<tr>
				<td colspan="2">No categories found</td>
			</tr>
		';
}

function theme_blogsListCatTableRow($categoryItem,$aRoot,$count) {
	echo
		'<tr class="',($count%2==0 ? 'odd' : 'even'),'">
			<td class="title">'.$categoryItem['name'].'</td>
			<td class="buttonList">
				<a href="'.$aRoot.'editCategory/'.$categoryItem['id'].'">Modify Category</a>
				<a href="'.$aRoot.'deleteCategory/'.$categoryItem['id'].'">Delete Category</a>
			</td>
		</tr>';
}

function theme_blogsListCatTableFoot() {
	echo '</table>';
}

function theme_blogsListCommentsNoComments() {
	echo 'No comments were found for this blog post.';
}

function theme_blogsListCommentsPendingTableHead() {
	echo '
	<table class="pagesList">
		<caption>Comments Awaiting Approval</caption>
		<tr>
			<th class="title">Author</th>
			<th>Comment</th>
			<th class="time">Time</th>
			<th class="loggedIP">IP Address</th>
			<th class="buttonList">Controls</th>
		</tr>
	';
}

function theme_blogsListCommentsPendingTableRow($data,$item,$count) {
	echo '<tr class="'.($count%2==0 ? 'even' : 'odd').'">
				<td class="title">'.$item['authorFirstName'].' '.$item['authorLastName'].'</td>
				<td>'.substr($item['parsedContent'],0,128) 	 . '</td>
				<td class="time">'.date('F j, Y \a\t g:i A',$item['time']).'</td>
				<td class="loggedIP">'.$item['loggedIP'].'</td>
				<td class="buttonList">
					<a href="'.$data->linkRoot.'admin/blogs/approveComment/'.$item['id'].'" title="Approve Comment">Approve</a>
					<a href="'.$data->linkRoot.'admin/blogs/disapproveComment/'.$item['id'].'" title="Disapprove Comment">Disapprove</a>
					<a href="'.$data->linkRoot.'admin/blogs/editComment/'.$item['id'].'" title="Edit Comment">Edit</a>
					<a href="'.$data->linkRoot.'admin/blogs/deleteComment/'.$item['id'].'" title="Delete Comment">Delete</a>
				</td>
			</tr>';
}

function theme_blogsListCommentsNoPending() {
	echo '
		<tr>
			<td colspan="5">No comments awaiting approval.</td>
		</tr>';
}

function theme_blogsListCommentsApprovedTableHead() {
	echo '
	<table class="pagesList">
		<caption>Approved Comments</caption>
		<tr>
			<th class="title">Author</th>
			<th>Comment</th>
			<th class="time">Time</th>
			<th class="loggedIP">IP Address</th>
			<th class="buttonList">Controls</th>
		</tr>
	';
}

function theme_blogsListCommentsApprovedTableRow($data,$item,$count) {
	echo '<tr class="'.($count%2==0 ? 'even' : 'odd').'">
				<td class="title">'.$item['authorFirstName'].' '.$item['authorLastName'].'</td>
				<td>'.$item['parsedContent'].'</td>
				<td class="time">'.date('F j, Y \a\t g:i A',$item['time']).'</td>
				<td class="loggedIP">'.$item['loggedIP'].'</td>
				<td class="buttonList">
					<a href="'.$data->linkRoot.'admin/blogs/disapproveComment/'.$item['id'].'" title="Disapprove Comment">Disapprove</a>
					<a href="'.$data->linkRoot.'admin/blogs/editComment/'.$item['id'].'" title="Edit Comment">Edit</a>
					<a href="'.$data->linkRoot.'admin/blogs/deleteComment/'.$item['id'].'" title="Delete Comment">Delete</a>
				</td>
			</tr>';
}

function theme_blogsListCommentsNoApproved() {
	echo '
		<tr>
			<td colspan="5">No approved comments exist for this post.</td>
		</tr>';
}

function theme_blogsListCommentsDisapprovedTableHead() {
	echo '
	<table class="pagesList">
		<caption>Disapproved</caption>
		<tr>
			<th class="title">Author</th>
			<th>Comment</th>
			<th class="time">Time</th>
			<th class="loggedIP">IP Address</th>
			<th class="buttonList">Controls</th>
		</tr>
	';
}

function theme_blogsListCommentsDisapprovedTableRow($data,$item,$count) {
	echo '<tr class="'.($count%2==0 ? 'even' : 'odd').'">
				<td class="title">'.$item['authorFirstName'].' '.$item['authorLastName'].'</td>
				<td>'.substr($item['parsedContent'],0,128) 	 . '</td>
				<td class="time">'.date('F j, Y \a\t g:i A',$item['time']).'</td>
				<td class="loggedIP">'.$item['loggedIP'].'</td>
				<td class="buttonList">
					<a href="'.$data->linkRoot.'admin/blogs/approveComment/'.$item['id'].'" title="Approve Comment">Approve</a>
					<a href="'.$data->linkRoot.'admin/blogs/editComment/'.$item['id'].'" title="Edit Comment">Edit</a>
					<a href="'.$data->linkRoot.'admin/blogs/deleteComment/'.$item['id'].'" title="Delete Comment">Delete</a>
				</td>
			</tr>';
}

function theme_blogsListCommentsNoDisapproved() {
	echo '
		<tr>
			<td colspan="5">No disapproved comments.</td>
		</tr>';
}

function theme_blogsListCommentsTableFoot() {
	echo '</table>';
}

function theme_blogsListPostsHead($data,$aRoot) {
	echo '
		<div class="navPanel buttonList">
			<a href="',$aRoot,'listCategories/'.$data->output['parentBlog']['id'].'">Categories</a>
			<a href="',$aRoot,'addPost/'.$data->output['parentBlog']['id'].'">
				Add New Post
			</a>
			<a href="'.$aRoot.'list">
				Back to Blog List
			</a>
		</div>';
}

function theme_blogsListPostsNoPosts() {
	echo '
		<p class="blogPostsListNoBlogPosts">No blogs posts exist</p>';
}

function theme_blogsListsPostsTableHead($data) {
	echo '
		<table class="blogPostsList">
			<caption>Manage Posts in the "',$data->output['parentBlog']['title'],'" Blog</caption>
			<thead>
				<tr>
					<th class="title">Post Title</th>
					<th class="date">Date Created</th>
					<th class="date">Last Edited</th>
					<th class="controls">Controls</th>
				</tr>
			</thead><tbody>';
}

function theme_blogsListPostsTableRow($item,$aRoot,$count) {
    echo '
		<tr class="',($count%2==0 ? 'odd' : 'even'),'">
			<td class="title">
				<a href="',$aRoot,'editPosts/',$item['blogId'],'/',$item['id'],'">
					',$item['title'],'
				</a>
			</td>
			<td class="date">
				<span>',date('d M Y',$item['postTime']),'</span>
				<span>',date('H:i T',$item['postTime']),'</span>
			</td>
			<td class="date">
				<span>'.date('d M Y',$item['modifiedTime']).'</span>
				<span>'.date('H:i T',$item['modifiedTime']).'</span>
			</td>
			<td class="buttonList">
				<a href="'.$aRoot.'listComments/',$item['id'],'">Edit Comments</a>
				<a href="'.$aRoot.'deletePosts/',$item['id'],'">Delete</a>
			</td>
		</tr>';
}

function theme_blogsListPostsTableFoot() {
	echo '
			</tbody>
		</table>';
}

function theme_blogsListPostsFoot($data,$aRoot) {
	echo '
			<div class="navPanel buttonList">
				<a href="',$aRoot,'listCategories/'.$data->output['parentBlog']['id'].'">Categories</a>
				<a href="',$aRoot,'addPost/'.$data->output['parentBlog']['id'].'">
					Add New Post
				</a>
				<a href="'.$aRoot.'list">
					Back to Blog List
				</a>
		</div>';
}

?>