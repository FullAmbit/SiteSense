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
function admin_blogsBuild($data,$db)
{
	// Make Sure We Have An ID
	if(!is_numeric($data->action[3]))
	{
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
		return;
	}
	// Get The Comment Info So Far
	$statement = $db->prepare('getCommentById','blogs');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['commentItem'] = $statement->fetch();
	
	if(checkPermisison('commentDisapprove','blogs',$data))	{
		$statement = $db->prepare('getBlogByPost','blogs');
		$statement->execute(array(
			':postId' => $data->output['commentItem']['post']
		));
		
		$blogItem = $statement->fetch();
		if($data->user['id'] !== $blogItem['owner']) {
            if(!checkPermission('accessOthers','blogs',$data)) {
                $data->output['abort'] = true;
                $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
                return;
            }
		}
	} else {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	
	// Disapprove Comment
	$statement = $db->prepare('disapproveComment','blogs');
	$statement->execute(array(':id' => $data->action[3]));
	
	if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>Comment Disapproved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/blogs/listComments/'.$data->output['commentItem']['post'].'">
							Return to Comments List
						</a>
					</div>';
			}
}
function admin_blogsShow($data)
{
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	}
}
?>