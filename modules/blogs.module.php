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
common_include('libraries/forms.php');
//common_include('plugins/campaignMonitor/campaignMonitor.api.php');
function page_getUniqueSettings($data) {
	$data->output['pageShortName']='blogs';
	$data->output['pageTitle']='Blog';
}
function page_buildContent($data,$db) {
	require_once('modules/blogs.common.php');
	$data->output['summarize'] = false;
	$data->output['notFound'] = false;
	// Now Get Users //
	$statement = $db->prepare('getAllUsers','users');
	$statement->execute();
	$data->output['usersList'] = $statement->fetchAll();
	// Get the ID Of The Blog Based On The ShortName
	if (!is_numeric($data->action[1])){
		$statement=$db->prepare('getBlogByName','blogs');
		$statement->execute(array(
			':shortName' => $data->action[1]
		));
		$parentBlog=$statement->fetch();
		$data->output['blogInfo'] = $parentBlog;
		$data->action[1]=$parentBlog['id'];
	}
	else
	{
		$statement=$db->prepare('getBlogById','blogs');
		$statement->bindValue(':blogId',$data->action[1]);
		$statement->execute();
		$data->output['blogInfo']=$statement->fetch();
	}
	// Blog Not Found
	if($data->output['blogInfo'] === false)
	{
		$data->output['notFound'] = true;
		return; 
	}
	$data->output['pageShortName']=$data->output['blogInfo']['shortName'];
	$data->output['pageTitle']=ucwords($data->output['blogInfo']['name']);
	// Get All Blog Categories
	$statement = $db->prepare('getAllCategoriesByBlogId','blogs');
	$statement->execute(array(
		':blogId' => $data->output['blogInfo']['id']
	));
	$data->output['blogCategoryList'] = $statement->fetchAll();
	
	// Build a localRoot so that links can account for top-level blogs
	$result=$db->query('getTopLevelBlogs','blogs');
	$data->topLevelBlogs = array();
	while ($row=$result->fetch()) 
	{
		$data->topLevelBlogs[]=$row['name'];
	}
	
	$data->localRoot = $data->linkRoot.(in_array($data->output['blogInfo']['name'],$data->topLevelBlogs) 
	? $data->output['blogInfo']['name'] 
	: 'blogs/'.$data->output['blogInfo']['shortName']);
	
	$data->output['rssLink'] = isset($data->output['blogInfo']['rssOverride']{1}) ? $data->output['blogInfo']['rssOverride'] : $data->localRoot.'/rss';
	
	// If RSS Feed Skip All This
	if($data->action[2] == 'rss')
	{
		// Content Type
		//$data->httpHeaders = NULL;
		$data->pageSettings['httpHeaders'][0] = 'Content-Type: application/xml';
		// Get Blog Info
		$statement = $db->prepare('getBlogById','blogs');
		$statement->execute(array(
			':blogId' => $data->action[1]
		));
		$data->output['blogItem'] = $statement->fetch();
		
		// Get All Posts In Blog
		$statement = $db->prepare('getBlogPostsByParentBlog','blogs');
		$statement->execute(array(
			':blogId' => $data->action[1]
		));
		$data->output['postsList'] = $statement->fetchAll();
		
		// Get All Categories in Blog
		$statement = $db->prepare('getAllCategoriesByBlogId','blogs');
		$statement->execute(array(':blogId' => $data->action[1]));
		$catList = $statement->fetchAll();
		foreach($catList as $catItem)
		{
			//var_dump($catItem);
			$data->output['rssCategoryList'][$catItem['id']] = $catItem;
		}
		
		
		// Get Name Of Blog Owner
		if($data->output['blogItem']['owner'] !== '0')
		{
			$statement = $db->prepare('getById','users');
			$statement->execute(array(
				':id' => $blogItem['owner']
			));
			$data->output['blogOwnerItem'] = $statement->fetch();
		}
		// Get Total Authors
		$statement = $db->prepare('getUniqueAuthorCountByBlog','blogs');
		$statement->execute(array(
			':blogId' => $data->action[1]
		));
		$data->output['blogItem']['authorCount'] = $statement->fetch();
		if($data->output['blogItem']['authorCount'] > 1)
		{
			// Get A List Of All Users And Resort Using The ID As The Key
			$statement = $db->prepare("getAllUsers",'users');
			$statement->execute();
			$result = $statement->fetchAll();
			foreach($result as $index => $userItem)
			{
				$data->output['blogItem']['userList'][$userItem['id']] = $userItem;
			}
		}
	} else {
	// If No Page Set, Then Start At 0
	if ($data->action[2] === false) $data->action[2]=0;
	// Show Posts In A Specific Blog (Action[2] would be the page number
	if (is_numeric($data->action[2])) {
		$data->output['blogInfo']['startPage']=$data->action[2];
		$data->output['summarize'] = true;
		blog_buildContent($data,$db);
		foreach($data->output['newsList'] as &$item){
			$statement=$db->prepare('countCommentsByPost','blogcomments');
			$statement->execute(array('post' => $item['id']));
			$item['commentCount'] = $statement->fetchColumn();
		}
	}
	else if($data->action[2] == 'tags')
	{
		// Get  A List Of Posts With A Specific Tag
		$statement = $db->prepare('getBlogPostsByTag','blogs');
		$statement->execute(array(
			':blogId' => $data->action[1],
			':tags' => '%'.$data->action[3]	.'%'
		));
		$data->output['newsList'] = $statement->fetchAll();
		// If No Posts, Return An Error
		if(empty($data->output['newsList'])){
			$data->output['notFound'] = true;
			return;
		}
		$data->output['summarize'] = true;
		foreach($data->output['newsList'] as &$item){
			$statement=$db->prepare('countCommentsByPost','blogcomments');
			$statement->execute(array('post' => $item['id']));
			$item['commentCount'] = $statement->fetchColumn();
		}
	}
	else if($data->action[2] == 'categories')
	{
		// Get The ID Of The Category Based Off The ShortName
		$statement = $db->prepare('getCategoryIdByShortName','blogs');
		$statement->execute(array(
			':shortName' => $data->action[3]
		));
		$data->output['categoryItem'] = $statement->fetch();
		// Get  A List Of Posts With A Specific Category
		$statement = $db->prepare('getBlogPostsByCategory','blogs');
		$statement->execute(array(
			':blogId' => $data->output['blogInfo']['id'],
			':categoryId' => $data->output['categoryItem']['id']
		));
		$data->output['newsList'] = $statement->fetchAll();		
		// If No Posts, Return An Error
		if(empty($data->output['newsList'])){
			$data->output['notFound'] = true;
			return;
		}
		$data->output['summarize'] = true;
		foreach($data->output['newsList'] as &$item){
			$statement=$db->prepare('countCommentsByPost','blogcomments');
			$statement->execute(array('post' => $item['id']));
			$item['commentCount'] = $statement->fetchColumn();
		}
	}
	else 
	{
		// Viewing A Specific Post Within A Blog
		$statement=$db->prepare('getBlogPostsByIDandName','blogs');
		$statement->execute(array(
			':blogId' => $data->action[1],
			':shortName' => $data->action[2]
		));
		$data->output['newsList'] = $statement->fetchAll();
		$data->output['pageTitle']=$data->output['newsList'][0]['title'].' - Blog';
		// If No Posts, Return An Error
		if(empty($data->output['newsList'])){
			$data->output['notFound'] = true;
			return;
		}
		if(($data->output['newsList'][0]['allowComments'] == '1'))
		{
			$data->output['commentForm'] = new formHandler('blogcomment',$data);
			$data->output['commentForm']->fields['post']['value'] = $data->output['newsList'][0]['id'];
			
			if (($data->output['blogInfo']['commentsRequireLogin'] == 0 || isset($data->user['id'])) && isset($_POST['fromForm']) && ($_POST['fromForm']==$data->output['commentForm']->fromForm)){
				$data->output['commentForm']->populateFromPostData();
				if ($data->output['commentForm']->validateFromPost())
				{
					$statement=$db->prepare('makeComment','blogcomments');
					//$data->output['commentForm']->sendArray[':comment'] = htmlspecialchars($data->output['commentForm']->sendArray[':comment']);
					
					// BBCode Parsing //
					if($data->settings['useBBCode'] == '1')
					{
						if(!isset($data->plugins['bbcode']))
						{
							common_loadPlugin($data,'bbcode');
						}
						$data->output['commentForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['commentForm']->sendArray[':rawContent']);
					} else {
						$data->output['commentForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['commentForm']->sendArray[':rawContent']);
					}
						
					// Remove subscriptions; not stored in our database
					unset($data->output['commentForm']->sendArray[':subscription']);
					$statement->execute($data->output['commentForm']->sendArray);
					unset($data->output['commentForm']);
					$data->output['commentSuccess'] = true;
				}
			}
		}
	}
	// Call The Theme Functions And Generate The Post
	foreach($data->output['newsList'] as &$item)
	{
		$statement=$db->prepare('getApprovedCommentsByPost','blogcomments');
		$statement->execute(array('post' => $item['id']));
		$item['comments'] = $statement->fetchAll();
		$item['commentCount'] = count($item['comments']);
		// Get A Count Of All Comments Awaiting Approval
		$statement = $db->prepare('getCommentsAwaitingApproval','blogcomments');
		$statement->execute(array('post' => $item['id']));
		$result = $statement->fetch();
		$item['commentsWaiting'] = intval($result[0]);
	}
	}
}
function page_content($data) 
{
	// If RSS Feed Skip All This
	if($data->pageSettings['httpHeaders'][0] == 'Content-Type: application/xml')
	{
		theme_blogRSSFeed($data);
	} else {
		$pagination=is_numeric($data->action[2]);
		blog_pageContent($data,false,$pagination, $data->output['summarize']);
	}
}

function loadPermissions($data) {
    $data->perisssions['blogs']=array(
      'admin' => 'User can see blog',
      'canSeeBlogOwners' => 'User can see blog owners',
      'canAddPost' => 'User can add blog post',
      'canSeeOthersBlogs' => 'User can view others blogs',
      'canAddCategory' => 'User can add a category to a blog',
      'canAddBlogOwner' => 'User can add others as owenr of a blog',
      'canApproveComments' => 'User can approve blogs comments',
      'canDeleteBlogOwners' => 'User can remove blog owners',
      'canDeleteBlogCategory' => 'User can remove categories from blogs',
      'canDeleteBlogComment' => 'User can remove blog comments',





    );
}
?>