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
	$data->output['delete']='';
	if(empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['abort']=true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
	} else {
		if(checkPermission('postDelete','blogs',$data)) {

			if(!checkPermission('accessOthers','blogs',$data))
			{
				$qHandle=$db->prepare('getBlogByIdAndOwner','admin_blogs');
				$qHandle->execute(array(
					':id' => $data->action[3],
					':owner' => $data->user['id']
				));
			} else {
				$qHandle=$db->prepare('getBlogByPost','admin_blogs');
				$qHandle->execute(array(
					':postId' => $data->action[3]
				));
			}
			if(($data->output['thisBlog']=$qHandle->fetch())==FALSE)
			{
				$data->output['abort']=true;
				$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
				return;
			}
				
			if(isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3]) {
				if(!empty($_POST['delete'])) {
					$statement=$db->prepare('deleteBlogPostById','admin_blogs');
					$statement->execute(array(
						':id' => $data->action[3]
					));
					// now delete across other language tables
					common_deleteFromLanguageTables($data,$db,'blog_posts','id',$data->action[3]);
					
					$data->output['deleteCount']=$statement->rowCount();
					if($data->output['deleteCount']>0) {
						$data->output['delete']='deleted';
					} else {
						$data->output['rejectError']=$data->phrases['core']['databaseErrorHeading'];
						$data->output['rejectText']=$data->phrases['core']['databaseErrorMessage'];
					}
				} else {
					/* from form plus not deleted must==cancelled. */
					$data->output['delete']='cancelled';
				}
			}
		} else {
            $data->output['abort']=true;
            $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		}
	}
}
function admin_blogsShow($data) {
	$aRoot=$data->linkRoot.'admin/blogs/';
	if(empty($data->output['rejectError'])) {
		switch ($data->output['delete']) {
			case 'deleted':
				theme_blogsDeletePostsDeleted($data,$aRoot) ;
			break;
			case 'cancelled':
				theme_blogsDeletePostsCancelled($data,$aRoot);
			break;
			default:
				theme_blogsDeletePostsDefault($data,$aRoot);
			break;
		}
	} else {
		theme_rejectError();
	}
}
?>