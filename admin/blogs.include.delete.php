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
	if (empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
	} else {
		if (checkPermission('canDeleteBlogOwners','blogs',$data)) {
			/*	Permissions
			 *	Anything less than a moderator only have individual access to blogs,
			 *	Thus, check to see if the user owns this blog.
			*/
			if(checkPermission('canSeeOthersBlogs','blogs',$data))
			{
				$qHandle=$db->prepare('getBlogByIdAndOwner','admin_blogs');
				$qHandle->execute(array(
					':id' => $data->action[3],
					':owner' => $data->user['id']
				));
			} else {
				$qHandle=$db->prepare('getBlogById','admin_blogs');
				$qHandle->execute(array(
					':id' => $data->action[3]
				));
			}
			if(($data->output['thisBlog']=$qHandle->fetch()) == FALSE)
			{
				$data->output['rejectError']='invalid parameters';
				$data->output['rejectText']='The blog you specified was not found.';
				return;
			}
			
			if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3]) {
				if (!empty($_POST['delete'])) {
					$qHandle=$db->prepare('deleteBlogById','admin_blogs');
					$qHandle->execute(array(
						':id' => $data->action[3]
					));
					$data->output['deleteCount']=$qHandle->rowCount();
					if ($data->output['deleteCount']>0) {
						$qHandle=$db->prepare('deleteBlogPostByBlogId','admin_blogs');
						//$qHandle->execute(array(
							//':id' => $data->action[3]
						//));
						$data->output['deletePostCount']=$qHandle->rowCount();
						$data->output['delete']='deleted';
						
						if($data->cdn)
						{
							// Delete Blog Image Folder
							common_include('plugins/edgeCast.php');
							$edgeCast = new api_edgeCast('50E3','c43dc644-d7af-4b4e-a6c1-72f4ad655e52');
							$edgeCast->delete($data->settings['cdnBaseDir'].'themes/'.$data->settings['theme'].'/images/blogs/'.$data->output['thisBlog']['shortName'].'/',true);
						}
					} else {
						$data->output['rejectError']='Database Error';
						$data->output['rejectText']='You attempted to delete a record, are you sure that record existed?';
					}
				} else {
					/* from form plus not deleted must == cancelled. */
					$data->output['delete']='cancelled';
				}
			}
		} else {
			$data->output['rejectError']='Insufficient User Permissions';
			$data->output['rejectText']='You do not have sufficient access to perform this action.';
		}
	}
}
function admin_blogsShow($data) {
	$aRoot=$data->linkRoot.'admin/blogs/';
	if (empty($data->output['rejectError'])) {
		switch ($data->output['delete']) {
			case 'deleted':
				theme_blogsDeleteDeleted($data,$aRoot);
			break;
			case 'cancelled':
				theme_blogsDeleteCancelled($aRoot);
			break;
			default:
				 theme_blogsDeleteDefault($data,$aRoot);
			break;
		}
	} else {
			theme_rejectError($data);
	}
}
?>