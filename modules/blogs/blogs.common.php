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
function blogs_common_buildContent($data,$db) {
    $statement=$db->prepare('countBlogPosts','blogs');
    $statement->bindValue(':blogId',$data->output['blogInfo']['id']);
    $statement->execute();
    if($result=$statement->fetch()) {
        $data->output['blogInfo']['numberOfPosts']=$result['count'];
    } else {
        $data->output['blogInfo']['numberOfPosts']=0;
    }
    $statement=$db->prepare('getBlogPostsDelimited','blogs');
    $statement->bindValue(':blogId',$data->output['blogInfo']['id']);
    /* LIMIT must be done using ::bindValue with typecasting!!! */
    $start=(intval($data->output['blogInfo']['startPage']) > 1) ? $data->output['blogInfo']['startPage']+1 : 0;
    $statement->bindValue(':start',(int)$start,PDO::PARAM_INT);
    $statement->bindValue(':count',(int)$data->output['blogInfo']['numberPerPage'],PDO::PARAM_INT);
    $statement->execute();
    $data->output['newsList']=$statement->fetchAll();
}
function blogs_common_getContent($data,$db,$targetBlog=1,$targetStart=0,$targetCount=6) {
	$retVal=array();
	if(!is_numeric($targetBlog)) {
		$statement=$db->prepare('getBlogByName','blogs');
		$statement->execute(array(
			':shortName' => $targetBlog
		));
		$retVal['parentBlog']=$statement->fetch();
		$targetBlog=$retVal['parentBlog']['id'];
	}
	$statement=$db->prepare('countBlogPosts','blogs');
	$statement->bindValue(':blogId',$targetBlog);
	$statement->execute();
	if($result=$statement->fetch()) {
		$retVal['numberOfPosts']=$result['count'];
	} else {
        $retVal['numberOfPosts']=0;
    }
	$statement=$db->prepare('getBlogPostsDelimited','blogs');
	$statement->bindValue(':blogId',$targetBlog);
	/* LIMIT must be done using ::bindValue with typecasting!!! */
	$statement->bindValue(':start',(int)$targetStart,PDO::PARAM_INT);
	$statement->bindValue(':count',(int)$targetCount,PDO::PARAM_INT);
	$statement->execute();
	$retVal['postList']=$statement->fetchAll();
	return $retVal;
}
function blogs_common_pageContent($data,$firstPagination=false,$secondPagination=false,$summarize=false,$headingLevel=2) {
	if($data->output['notFound']===true) {
		theme_notFound($data);
		return;
	}
	if($summarize) {
        theme_contentBoxHeader($data->output['pageTitle']);
	}
	if($firstPagination) {
		theme_pagination(
			$data->output['blogInfo']['numberOfPosts'],
			$data->output['blogInfo']['startPage'],
			$data->output['blogInfo']['numberPerPage'],
			$data->localRoot.'/'
		);
	}
	$count=count($data->output['newsList']);
	foreach($data->output['newsList'] as $newsItem) {
		// What's The Author's Name? Scan The User's Array. Better Than Running A Crapton of Queries.
		foreach($data->output['usersList'] as $userItem) {
			if($userItem['id']==$newsItem['user']) {
				$newsItem['authorName']=$userItem['firstName']." ".$userItem['lastName'];
				break;
			}
		}
		if($summarize) {
			theme_blogSummaryBox($data, $newsItem, $headingLevel);
		} else {
			theme_blogDisplayBox($data, $newsItem, $headingLevel);
		}
	}
	if($secondPagination) {
		theme_pagination(
			$data->output['blogInfo']['numberOfPosts'],
			$data->output['blogInfo']['startPage'],
			$data->output['blogInfo']['numberPerPage'],
			$data->localRoot.'/'
		);
	}
	if($summarize) {
    	theme_contentBoxFooter();
	}
}
?>