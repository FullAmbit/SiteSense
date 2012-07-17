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
function admin_blogsBuild($data,$db) {
    if(!checkPermission('categoryList','blogs',$data)) {
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	// ---If You're a Blogger, You Can Only Load Your OWN Blog--
	if(!checkPermission('accessOthers','blogs',$data)) {
		$check=$db->prepare('getBlogByIdAndOwner','admin_blogs');
		$check->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$check=$db->prepare('getBlogById','admin_blogs');
		$check->execute(array(':id' => $data->action[3]));
	}
	// Check For Results
	if(($data->output['blogItem']=$check->fetch())===FALSE) {
		$data->output['abort']=true;
		$data->output['abortMessage']='<h2>The ID does not exist in database</h2>';
		return;
	}
	// Get All Categories //
	$statement=$db->prepare('getAllCategoriesByBlog','admin_blogs');
	$statement->execute(array(
		':blogId' => $data->action[3]
	));
	$data->output['categoryList']=$statement->fetchAll();
}
function admin_blogsShow($data) {
	$aRoot=$data->linkRoot.'admin/blogs/';
	theme_blogsListCatTableHead($data,$aRoot) ;
	$count=0;
	if(count($data->output['categoryList']) < 1) {
		theme_blogsListCatNoCats();
	}
	foreach($data->output['categoryList'] as $categoryItem) {
		theme_blogsListCatTableRow($categoryItem,$aRoot,$count);
		$count++;
	}
	theme_blogsListCatTableFoot();
}
?>