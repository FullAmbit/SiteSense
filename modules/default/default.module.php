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

function page_buildContent($data,$db) {
	if(!isset($data->user['id'])) {
		common_redirect('users/register/',$data);
	}
	require_once('modules/blogs/blogs.common.php');
	$db->loadModuleQueries('blogs');
	$statement=$db->query('getAllNews');
	$data->output['blogInfo']=$statement->fetch();
	$data->output['blogInfo']['startPage']=0;
	$data->output['newsList']=blog_getContent($data,$db,'news',0,6);
}
function page_content($data) {
	foreach ($data->output['newsList']['postList'] as $blogPost) {
		theme_contentBoxHeader(
			$blogPost['title'],
			$data->linkRoot.'news/'.$blogPost['shortName'],
			$blogPost['postTime']
		);
		echo htmlspecialchars_decode($blogPost['parsedContent']);
		theme_contentBoxFooter();
	}
}
?>