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
		<h2>',$data->phrases['blogs']['deleteBlogSuccessHeading'],' - ',$data->output['thisBlog']['name'],' Deleted</h2>
		<div class="buttonList">
			<a href="',$aRoot,'list">',$data->phrases['blogs']['returnToBlogs'],'</a>
		</div>
		';
}

function theme_blogsDeleteCancelled($aRoot) {
	echo '
		<h2>',$data->phrases['blogs']['deleteBlogCancelledHeading'],'</h2>
		<p>
			',$data->phrases['core']['messageRedirect'],'
			<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a>
		</p>';
}

function theme_blogsDeleteDefault($data,$aRoot) {
	echo '
		<form action="',$aRoot,'delete/',$data->action[3],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>',$data->phrases['blogs']['deleteBlogConfirmMessage'],' - ',$data->output['thisBlog']['name'],'</span></legend>
				<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
				<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
				<input type="hidden" name="fromForm" value="',$data->action[3],'" />
			</fieldset>
		</form>';
}

function theme_blogsDeleteCatDeleted($data,$aRoot) {
	echo $data->phrases['blogs']['deleteCategorySuccessMessage'].'<div class="buttonList"><a href="'.$aRoot.'listCategories/'.$data->output['blogItem']['id'].'" title="Return To Categories">'.$data->phrases['blogs']['returnToCategories'].'</a></div>';
}

function theme_blogsDeleteCatCancelled($data,$aRoot) {
	echo '<h2>',$data->phrases['blogs']['deleteCategoryCancelledHeading'],'</h2><p>',$data->phrases['core']['messageRedirect'],'<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a></p>';
}

function theme_blogsDeleteCatDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteCategory/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>',$data->phrases['blogs']['deleteCategoryConfirmMessage'],'</legend>
			</fieldset>
			<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
			<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_blogsDeleteCommentDeleted($data,$aRoot) {
	echo $data->phrases['blogs']['deleteCommentSuccessMessage'].'<div class="buttonList"><a href="'.$aRoot.'" title="Return To Blogs">'.$data->phrases['blogs']['returnToBlogs'].'</a></div>';
}

function theme_blogsDeleteCommentCancelled($data,$aRoot) {
	echo $data->phrases['blogs']['deleteCommentCancelledHeading'],'<p>',$data->phrases['core']['messageRedirect'],'<a href="',$aRoot,'list">',$data->phrases['core']['linkSkipWait'],'</a></p>';
}

function theme_blogsDeleteCommentDefault($data,$aRoot) {
	echo '
		<form action="'.$aRoot.'deleteComment/'.$data->action[3].'" method="post" class="verifyForm">
			<fieldset>
				<legend>'.$data->phrases['blogs']['deleteCommentConfirmMessage'].'</legend>
			</fieldset>
			<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
			<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
			<input type="hidden" name="fromForm" value="'.$data->action[3].'" />
		</form>';
}

function theme_blogsDeletePostsDeleted($data,$aRoot) {
	echo '
		<h2>',$data->phrases['blogs']['deletePostSuccessHeading'],' - ',$data->action[3],'</h2>
		<div class="buttonList">
			<a href="',$aRoot,'listPosts/',$data->output['thisBlog']['blogId'],'">',$data->phrases['blogs']['returnToPosts'],'</a>
		</div>
		';
}

function theme_blogsDeletePostsCancelled($data,$aRoot) {
	echo '
		<h2>',$data->phrases['blogs']['deletePostCancelledHeading'],'</h2>
		<p>
			',$data->phrases['core']['messageRedirect'],'
			<a href="',$aRoot,'listPosts/',$data->output['thisBlog']['blogId'],'">',$data->phrases['core']['linkSkipWait'],'</a>
		</p>';
}

function theme_blogsDeletePostsDefault($data,$aRoot) {
	echo '
		<form action="',$aRoot,'deletePosts/',$data->action[3],'" method="post" class="verifyForm">
			<fieldset>
				<legend><span>',$data->phrases['blogs']['deletePostConfirmHeading'],' - ',$data->action[3],'</span></legend>
			<input type="submit" name="delete" value="',$data->phrases['core']['actionConfirmDelete'],'" />
			<input type="submit" name="cancel" value="',$data->phrases['core']['actionCancelDelete'],'" />
				<input type="hidden" name="fromForm" value="',$data->action[3],'" />
			</fieldset>
		</form>';
}

function theme_blogsListHead($data,$aRoot) {
	echo '
		<div class="navPanel buttonList">
			<a href="',$aRoot,'add">
				',$data->phrases['blogs']['addNewBlog'],'
			</a>
		</div>';
}

function theme_blogsListNoBlogs($data) {
	echo '
		<p class="blogsListNoBlogs">',$data->phrases['blogs']['noBlogsExist'],'</p>';
}

function theme_blogsListTableHead($data) {
	echo '
		<table class="blogsList">
		<caption>',$data->phrases['blogs']['manageBlogsHeading'],'</caption>
			<thead>
				<tr>
					<th class="shortName">',$data->phrases['blogs']['blogTitle'],'</th>
					<th class="module">',$data->phrases['blogs']['owner'],'</th>
					<th class="module">',$data->phrases['blogs']['entries'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_blogsListTableRow($data,$item,$aRoot,$count) {
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
				<a href="'.$aRoot.'addPost/'.$item['id'].'/new">',$data->phrases['blogs']['newPost'],'</a>
				<a href="',$aRoot,'edit/',$item['id'],'">',$data->phrases['core']['actionModify'],'</a>
				<a href="'.$aRoot.'delete/'.$item['id'].'">',$data->phrases['core']['actionDelete'],'</a>
			</td>
		</tr>';
}

function theme_blogsListTableFoot() {
	echo '
			</tbody>
		</table>';
}

function theme_blogsListFoot($data,$aRoot) {
	echo '
		<div class="navPanel buttonList">
			<a href="',$aRoot,'add">
				',$data->phrases['blogs']['addNewBlog'],'
			</a>
		</div>';
}

function theme_blogsListCatTableHead($data,$aRoot) {
	echo '
		<h2 class="preTable">',$data->phrases['blogs']['manageCategoriesHeading'],' - ',$data->output['blogItem']['name'],'</h2>
		<div class="navPanel buttonList">
			<a href="',$aRoot,'addCategory/',$data->output['blogItem']['id'],'">',$data->phrases['blogs']['addCategory'],'</a>
		</div>
		<table class="pagesList">
			<thead>
				<tr>
					<th class="title">',$data->phrases['core']['title'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead>
		';
}

function theme_blogsListCatNoCats($data) {
	echo '
			<tr>
				<td colspan="2">',$data->phrases['blogs']['noCategoriesFound'],'</td>
			</tr>
		';
}

function theme_blogsListCatTableRow($data,$categoryItem,$aRoot,$count) {
	echo
		'<tr class="',($count%2==0 ? 'odd' : 'even'),'">
			<td class="title">'.$categoryItem['name'].'</td>
			<td class="buttonList">
				<a href="'.$aRoot.'editCategory/'.$categoryItem['id'].'">',$data->phrases['core']['actionModify'],'</a>
				<a href="'.$aRoot.'deleteCategory/'.$categoryItem['id'].'">',$data->phrases['core']['actionDelete'],'</a>
			</td>
		</tr>';
}

function theme_blogsListCatTableFoot() {
	echo '</table>';
}

function theme_blogsListCommentsNoComments($data) {
	echo $data->phrases['blogs']['noCommentsFound'];
}

function theme_blogsListCommentsPendingTableHead($data) {
	echo '
	<table class="pagesList">
		<caption>',$data->phrases['blogs']['manageCommentsPendingHeading'],'</caption>
		<tr>
			<th class="title">',$data->phrases['blogs']['author'],'</th>
			<th>',$data->phrases['blogs']['comment'],'</th>
			<th class="time">',$data->phrases['core']['time'],'</th>
			<th class="loggedIP">',$data->phrases['blogs']['ipAddress'],'</th>
			<th class="buttonList">',$data->phrases['core']['controls'],'</th>
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
					<a href="'.$data->linkRoot.'admin/blogs/approveComment/'.$item['id'].'" title="Approve Comment">'.$data->phrases['blogs']['approve'],'</a>
					<a href="'.$data->linkRoot.'admin/blogs/disapproveComment/'.$item['id'].'" title="Disapprove Comment">'.$data->phrases['blogs']['disapprove'].'</a>
					<a href="'.$data->linkRoot.'admin/blogs/editComment/'.$item['id'].'" title="Edit Comment">'.$data->phrases['core']['actionEdit'].'</a>
					<a href="'.$data->linkRoot.'admin/blogs/deleteComment/'.$item['id'].'" title="Delete Comment">'.$data->phrases['core']['actionDelete'].'</a>
				</td>
			</tr>';
}

function theme_blogsListCommentsNoPending($data) {
	echo '
		<tr>
			<td colspan="5">',$data->phrases['blogs']['noCommentsPending'],'</td>
		</tr>';
}

function theme_blogsListCommentsApprovedTableHead($data) {
	echo '
	<table class="pagesList">
		<caption>',$data->phrases['blogs']['manageCommentsApprovedHeading'],'</caption>
		<tr>
			<th class="title">',$data->phrases['blogs']['author'],'</th>
			<th>',$data->phrases['blogs']['comment'],'</th>
			<th class="time">',$data->phrases['core']['time'],'</th>
			<th class="loggedIP">',$data->phrases['blogs']['ipAddress'],'</th>
			<th class="buttonList">',$data->phrases['core']['controls'],'</th>
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
					<a href="'.$data->linkRoot.'admin/blogs/disapproveComment/'.$item['id'].'" title="Disapprove Comment">'.$data->phrases['blogs']['disapprove'].'</a>
					<a href="'.$data->linkRoot.'admin/blogs/editComment/'.$item['id'].'" title="Edit Comment">'.$data->phrases['core']['actionEdit'].'</a>
					<a href="'.$data->linkRoot.'admin/blogs/deleteComment/'.$item['id'].'" title="Delete Comment">'.$data->phrases['core']['actionDelete'].'</a>
				</td>
			</tr>';
}

function theme_blogsListCommentsNoApproved($data) {
	echo '
		<tr>
			<td colspan="5">',$data->phrases['blogs']['noCommentsApproved'],'</td>
		</tr>';
}

function theme_blogsListCommentsDisapprovedTableHead($data) {
	echo '
	<table class="pagesList">
		<caption>',$data->phrases['blogs']['manageCommentsDisapprovedHeading'],'</caption>
		<tr>
			<th class="title">',$data->phrases['blogs']['author'],'</th>
			<th>',$data->phrases['blogs']['comment'],'</th>
			<th class="time">',$data->phrases['core']['time'],'</th>
			<th class="loggedIP">',$data->phrases['blogs']['ipAddress'],'</th>
			<th class="buttonList">',$data->phrases['core']['controls'],'</th>
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

function theme_blogsListCommentsNoDisapproved($data) {
	echo '
		<tr>
			<td colspan="5">',$data->phrases['blogs']['noCommentsDisapproved'],'</td>
		</tr>';
}

function theme_blogsListCommentsTableFoot() {
	echo '</table>';
}

function theme_blogsListPostsHead($data,$aRoot) {
	echo '
		<div class="navPanel buttonList">
			<a href="',$aRoot,'listCategories/'.$data->output['parentBlog']['id'].'">',$data->phrases['blogs']['categories'],'</a>
			<a href="',$aRoot,'addPost/'.$data->output['parentBlog']['id'].'">
				',$data->phrases['blogs']['addNewPost'],'
			</a>
			<a href="'.$aRoot.'list">
				',$data->phrases['blogs']['returnToBlogs'],'
			</a>
		</div>';
}

function theme_blogsListPostsNoPosts($data) {
	echo '
		<p class="blogPostsListNoBlogPosts">',$data->phrases['noPostsExist'],'</p>';
}

function theme_blogsListsPostsTableHead($data) {
	echo '
		<table class="blogPostsList">
			<caption>',$data->phrases['blogs']['managePostsHeading'],' - ',$data->output['parentBlog']['title'],'</caption>
			<thead>
				<tr>
					<th class="title">',$data->phrases['core']['title'],'</th>
					<th class="date">',$data->phrases['blogs']['dateCreated'],'</th>
					<th class="date">',$data->phrases['blogs']['lastEdited'],'</th>
					<th class="controls">',$data->phrases['core']['controls'],'</th>
				</tr>
			</thead><tbody>';
}

function theme_blogsListPostsTableRow($data,$item,$aRoot,$count) {
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
				<a href="'.$aRoot.'listComments/',$item['id'],'">',$data->phrases['blogs']['editComments'],'</a>
				<a href="'.$aRoot.'deletePosts/',$item['id'],'">',$data->phrases['core']['actionDelete'],'</a>
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
			<a href="',$aRoot,'listCategories/'.$data->output['parentBlog']['id'].'">',$data->phrases['blogs']['categories'],'</a>
				<a href="',$aRoot,'addPost/'.$data->output['parentBlog']['id'].'">
				',$data->phrases['blogs']['addNewPost'],'
				</a>
				<a href="'.$aRoot.'list">
				',$data->phrases['blogs']['returnToBlogs'],'
				</a>
		</div>';
}

?>