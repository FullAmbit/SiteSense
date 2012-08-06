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
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
	} else {
		if(checkPermission('blogDelete','blogs',$data)) {
			if(!checkPermission('accessOthers','blogs',$data)) {
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
			if(($data->output['thisBlog']=$qHandle->fetch())==FALSE) {
				$data->output['rejectError']='invalid parameters';
				$data->output['rejectText']='The blog you specified was not found.';
				return;
			}
			if(isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3]) {
				if(!empty($_POST['delete'])) {
                    if($data->output['thisBlog']['topLevel']) {
                      $statement=$db->prepare('deleteReplacementByMatch','admin_dynamicURLs');
                      $statement->execute(array(
                        ':match' => '^'.$data->output['thisBlog']['shortName'].'(/.*)?$'

                      ));
                    }
					$qHandle=$db->prepare('deleteBlogById','admin_blogs');
					$qHandle->execute(array(
						':id' => $data->action[3]
					));
					// Delete Blog EVERYWHERE
					common_deleteFromLanguageTables($data,$db,'blogs','id',$data->action[3]);
					$data->output['deleteCount']=$qHandle->rowCount();
					if($data->output['deleteCount']>0) {
						// Delete Blog Posts EVERYWHERE
						common_deleteFromLanguageTables($data,$db,'blog_posts','blogId',$data->action[3]);
						$data->output['delete']='deleted';
						if($data->cdn) {
							// Delete Blog Image Folder
							common_include('plugins/edgeCast.php');
							$edgeCast=new api_edgeCast('50E3','c43dc644-d7af-4b4e-a6c1-72f4ad655e52');
							$edgeCast->delete($data->settings['cdnBaseDir'].'themes/'.$data->settings['theme'].'/images/blogs/'.$data->output['thisBlog']['shortName'].'/',true);
						}
					} else {
						$data->output['rejectError']='Database Error';
						$data->output['rejectText']='You attempted to delete a record, are you sure that record existed?';
					}
				} else {
					// From form plus not deleted must==cancelled.
					$data->output['delete']='cancelled';
				}
			}
		} else {
            $data->output['abort']=true;
            $data->output['abortMessage']='<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
            return;
        }
	}
}
function admin_blogsShow($data) {
	$aRoot=$data->linkRoot.'admin/blogs/';
	if(empty($data->output['rejectError'])) {
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