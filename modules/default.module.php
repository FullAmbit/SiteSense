<?php

function page_getUniqueSettings($data) {
	$data->output['pageShortName']='default';
}

function page_buildContent($data,$db) {
	require_once('modules/blogs.common.php');
	$db->loadModuleQueries('blogs');

	$statement=$db->query('getAllNews');
	$data->output['blogInfo']=$statement->fetch();
	$data->output['blogInfo']['startPage']=0;

	$data->output['newsList']=blog_getContent($data,$db,'news',0,6);

	$statement=$db->query('getHomePagePages');
	$data->output['homePagePages']=$statement->fetchAll();

	$statement=$db->query('getHomePageSideBarPages');
	$data->output['homePageSideBars']=$statement->fetchAll();
}

function page_content($data) {

	foreach ($data->output['newsList']['postList'] as $blogPost) {
		theme_contentBoxHeader(
			$blogPost['title'],
			$data->linkRoot.'news/'.$blogPost['shortName'],
			$blogPost['postTime']
		);
		echo $blogPost['content'];
		theme_contentBoxFooter();
	}

	if (count($data->output['homePagePages'])>0) {
		foreach ($data->output['homePagePages'] as $pageInfo) {
			theme_contentBoxHeader(
				$pageInfo['title'],
				'',
				0,
				common_camelBack($pageInfo['title'])
			);
			echo $pageInfo['content'];
			theme_contentBoxFooter();
		}
	}

}
?>