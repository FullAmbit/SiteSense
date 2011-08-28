<?php

function blog_buildContent($data,$db) {
	$statement=$db->prepare('countBlogPosts','blogs');
	$statement->bindValue(':blogId',$data->output['blogInfo']['id']);
	$statement->execute();

	if ($result=$statement->fetch()) {
		$data->output['blogInfo']['numberOfPosts']=$result['count'];
	} else $data->output['blogInfo']['numberOfPosts']=0;

	$statement=$db->prepare('getBlogPostsDelimited','blogs');
	$statement->bindValue(':blogId',$data->output['blogInfo']['id']);
	/* LIMIT must be done using ::bindValue with typecasting!!! */
	$statement->bindValue(':start',(int)$data->output['blogInfo']['startPage'],PDO::PARAM_INT);
	$statement->bindValue(':count',(int)$data->output['blogInfo']['numberPerPage'],PDO::PARAM_INT);
	$statement->execute();
	$data->output['newsList']=$statement->fetchAll();
}

function blog_getContent($data,$db,$targetBlog=1,$targetStart=0,$targetCount=6) {
	$retVal=array();
	if (!is_numeric($targetBlog)) {
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
	if ($result=$statement->fetch()) {
		$retVal['numberOfPosts']=$result['count'];
	} else $retVal['numberOfPosts']=0;
	$statement=$db->prepare('getBlogPostsDelimited','blogs');
	$statement->bindValue(':blogId',$targetBlog);
	/* LIMIT must be done using ::bindValue with typecasting!!! */
	$statement->bindValue(':start',(int)$targetStart,PDO::PARAM_INT);
	$statement->bindValue(':count',(int)$targetCount,PDO::PARAM_INT);
	$statement->execute();
	$retVal['postList']=$statement->fetchAll();
	return $retVal;
}

function blog_pageContent($data,$firstPagination=false,$secondPagination=false,$summarize=false,$headingLevel=2) {

	if($data->output['notFound'] === true){
		theme_notFound($data);
		return;
	}
	$localRoot = $data->linkRoot . ($data->output['blogInfo']['name']=='news' ? 'news' : 'blogs/'.$data->output['blogInfo']['shortName']);
	if ($firstPagination) {
		theme_pagination(
			$data->output['blogInfo']['numberOfPosts'],
			$data->output['blogInfo']['startPage'],
			$data->output['blogInfo']['numberPerPage'],
			$localRoot
		);
	}
	common_include($data->themeDir . 'formGenerator.template.php');
	foreach ($data->output['newsList'] as $newsItem) {
		if($summarize){
			theme_blogSummaryBox($data, $newsItem, $localRoot, $headingLevel);
		}else{
			theme_blogDisplayBox($data, $newsItem, $localRoot, $headingLevel);
		}
	}

	if ($secondPagination) {
		theme_pagination(
			$data->output['blogInfo']['numberOfPosts'],
			$data->output['blogInfo']['startPage'],
			$data->output['blogInfo']['numberPerPage'],
			$localRoot
		);
	}
}

?>