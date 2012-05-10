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
function theme_blogSummariesHeader($title) {
	echo '
		<div class="contentBox">
			<h2>',$title,'</h2>';
}
function theme_blogSummariesFooter() {
	echo '
		<!-- .contentBox --></div>';
}
function theme_blogSummaryBox($data, $blogPost, $localRoot, $headingLevel = 2){
	theme_blogContentBoxHeader(
		$blogPost['title'],
		$data->localRoot.'/'.$blogPost['shortName'],
		$blogPost['postTime'],
		$headingLevel
	);
	$blogPost['summary'] = htmlspecialchars_decode(common_parseDynamicValues($data,$blogPost['parsedSummary']));
	
	$noWrapper = theme_noWrapper($blogPost['summary']);
	if ($noWrapper) echo '<div class="forceDiv">';
	echo $blogPost['summary'];
	if ($noWrapper) echo '</div>';
	if ($blogPost['modifiedTime']>$blogPost['postTime']) {
		theme_lastModified($blogPost['modifiedTime']);
	}
	theme_blogPostControls($data,$blogPost);
	if(function_exists('theme_blogContentBoxFooter')){
		theme_blogContentBoxFooter();
	}else{
		theme_contentBoxFooter();
	}
}
function theme_blogDisplayBox($data, $blogPost, $localRoot, $headingLevel = 2){
	theme_blogContentBoxHeader(
		$blogPost['title'],
		$data->localRoot.'/'.$blogPost['shortName'],
		$blogPost['postTime'],
		$headingLevel
	);
	$blogPost['content'] = htmlspecialchars_decode(common_parseDynamicValues($data,$blogPost['parsedContent']));
	$noWrapper = theme_noWrapper($blogPost['content']);
	if ($noWrapper) echo '<div class="forceDiv">';
	echo $blogPost['content'];
	if ($noWrapper) echo '</div>';
	if ($blogPost['modifiedTime']>$blogPost['postTime']) {
		theme_lastModified($blogPost['modifiedTime']);
	}
	theme_blogPostControls($data,$blogPost);
	if(function_exists('theme_blogContentBoxFooter')){
		theme_blogContentBoxFooter();
	}else{
		theme_contentBoxFooter();
	}
	if(!empty($blogPost['comments'])){
		theme_displayComments($blogPost['comments']);
	}
	if(isset($data->output['commentForm']) && ($data->output['blogInfo']['commentsRequireLogin'] == 0 || isset($data->user['id']))){
		theme_buildForm($data->output['commentForm']);
	}else if(isset($data->output['commentSuccess'])){
		echo '<p class="commentSuccess">Thank you for your comment</p>';
	}
}
function theme_displayComments($Comments){
	echo '<ol class="blogPostComments">';
	foreach($Comments as $Comment){
		$Comment['parsedContent'] = '<p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', $Comment['parsedContent']) . '<p>';
		while(strpos($Comment['parsedContent'], '<p></p>') !== false){
			$Comment['parsedContent'] = str_replace('<p></p>', '', $Comment['parsedContent']);
		}
		echo '
			<li>
				<blockquote>
					',$Comment['parsedContent'],'
					<cite>
						Posted by ',$Comment['author'],' on ', date('F j, Y \a\t g:i a', strtotime($Comment['time'])),'
					</cite>
				</blockquote>
			</li>
		';
	}
	echo '<!-- .blogPostComments --></ol>';
}
function theme_noWrapper($content){
	$matchCount=preg_match('/^<(\w+)\W/i',$content,$matches);
	if ($matchCount===0){
		return true;
	}else{
		switch ($matches[1]) {
			case 'p':
			case 'div':
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				return false;
				break;
			default:
				return true;
				break;
		}
	}
}
function theme_blogListHeader($title) {
	echo '
		<div class="blogTitleList">
			<h2>',$title,'</h2>
			<ul>';
}
function theme_blogListFooter() {
	echo '
			</ul>
		<!-- .blogTitleList --></div>';
}
function theme_blogListItem($blogPost,$localRoot) {
	echo '
				<li>
					<span>',date('d F Y',$blogPost['postTime']),' <span>&bull;</span></span>
					<a href="',$localRoot,$blogPost['shortName'],'">
						',$blogPost['title'],'
					</a>
				</li>';
}
function theme_blogPostControls($data,$blogItem) {
	$controls=array();
	if (checkPermission('blogsViewOthers','blogs',$data)) {
		if (
			$blogItem['allowComments']==true
		) $controls[]='<a href="#">Reply</a>';
		if (
			checkPermission('canManageBlog','blogs',$data) &&
				($data->output['blog']['owner']==$data->user['id'])

		) {
			if ($blogItem['repliesWaiting']>0) $controls[]='<a href="#">Approve Replies</a>';
			if (
				checkPermission('postEdit','blogs',$data)
			) $controls[]='<a href="'.$data->linkRoot.'/admin/blogs/editPosts/'.$blogItem['blogId'].'/'.$blogItem['id'].'">Edit</a>';
		}
	}
	if (!empty($controls)) {
		echo '
							<ul class="postControls">';
		foreach ($controls as $lineItem) {
			echo '
								<li>',$lineItem,'</li>';
		}
		echo '
							</ul>';
	}
}
function theme_lastModified($modifiedTime) {
			echo '
							<div class="lastModified">
								<em>Last Modified '.date('d F Y H:i T',$modifiedTime).'</em>
							</div>';
}
function theme_notFound($data){
	theme_contentBoxHeader('HTTP/1.1 404 Not Found');
	echo '
				<p>
					You attempted to access ',implode('/', array_filter($data->action)),' which either does not exist or is not accessable on this server. Please check the URL and try again. If you feel this is in error, please contact the site administrator.
				</p>';
	theme_contentBoxFooter();
}
?>