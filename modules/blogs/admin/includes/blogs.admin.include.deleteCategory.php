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
    if(!checkPermission('categoryDelete','blogs',$data))	{
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	$data->output['delete']='';
	// Check To See If The Category Exists
	$check=$db->prepare('getCategoryById','admin_blogs');
	$check->execute(array(
        ':id' => $data->action[3]
    ));
	if(($data->output['categoryItem']=$check->fetch())===FALSE)	{
		$data->output['abort']=true;
		$data->output['abortMessage']='<h2>The ID does not exist in database</h2>';
		return;
	}
	//---If You're a Blogger, You Can Only Load Your OWN Blog--
	if(!checkPermission('accessOthers','blogs',$data)) {
		$check=$db->prepare('getBlogByIdAndOwner','admin_blogs');
		$check->execute(array(
			':id' => $data->output['categoryItem']['blogId'],
			':owner' => $data->user['id']
		));
	} else {
		$check=$db->prepare('getBlogById','admin_blogs');
		$check->execute(array(
			':id' => $data->output['categoryItem']['blogId']
		));
	}
	if(($data->output['blogItem']=$check->fetch())===FALSE)	{
		$data->output['rejectError']='Invalid Parameters';
		$data->output['rejectText']='The blog you specified could not be found.';
		return;
	}
	if(isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3]) {
		if(!empty($_POST['delete'])) {
			// Delete Category From DB
			$statement=$db->prepare('deleteCategory','admin_blogs');
			$statement->execute(array(
				':id' => $data->output['categoryItem']['id']
			));
			common_deleteFromLanguageTables($data,$db,'blog_categories','id',$data->output['categoryItem']['id']);

			// Set Category to ZERO For All Existing Posts Within Category
			common_updateAcrossLanguageTables($data,$db,'blog_posts',array('categoryId' => $data->output['categoryItem']['id']),array('categoryId' => $data->output['categoryItem']['id']),TRUE);
			/**$statement=$db->prepare('updatePostsWithinCategory','admin_blogs');
			$statement->execute(array(
				':categoryId' => $data->output['categoryItem']['id']
			));**/
			
			$data->output['delete']='deleted';
		} else {
			$data->output['delete']='cancelled';
		}
	}
}
function admin_blogsShow($data) {
	$aRoot=$data->linkRoot.'admin/blogs/';
	if(empty($data->output['rejectError'])) {
		switch($data->output['delete']) {
			case 'deleted':
				theme_blogsDeleteCatDeleted($data,$aRoot);
				break;
			case 'cancelled':
				theme_blogsDeleteCatCancelled($aRoot);
				break;
			default:
				theme_blogsDeleteCatDefault($data,$aRoot);
				break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>