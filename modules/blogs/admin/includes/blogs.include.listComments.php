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
    if(checkPermission('commentList','blogs',$data)) {
    	$statement = $db->prepare('getBlogByPost','admin_blogs');
    	$statement->execute(array(
    		':postId' => $data->action[3]
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

	// Retrieve List Of All Approved Blog Comments
  	if(is_numeric($data->action[3])) {
		$statement = $db->prepare('getApprovedCommentsByPost','admin_blogs');
	  	$statement->execute(array(':post' => $data->action[3]));
	  	$data->output['commentList']['approved'] = $statement->fetchAll();

		// Comments Awaiting Approval
		$statement = $db->prepare('getCommentsAwaitingApproval','admin_blogs');
		$statement->execute(array(':post' => $data->action[3]));
		$data->output['commentList']['queue'] = $statement->fetchAll();
		
		// Comments Disapproved
		$statement = $db->prepare('getDisapprovedCommentsByPost','admin_blogs');
		$statement->execute(array(':post' => $data->action[3]));
		$data->output['commentList']['disapproved'] = $statement->fetchAll();
	}
}
function admin_blogsShow($data) {
	if(count($data->output['commentList']) < 1) {
		theme_blogsListCommentsNoComments();
		return;
	}
	// Comments Awaiting Approval
	theme_blogsListCommentsPendingTableHead();
	$count = 0;
	if(count($data->output['commentList']['queue']) > 0) {
		foreach($data->output['commentList']['queue'] as $item)
		{
			theme_blogsListCommentsPendingTableRow($data,$item,$count);
			$count++;
		}
	} else {
		theme_blogsListCommentsNoPending();
	}
	theme_blogsListCommentsTableFoot();
	//---------------------------
	// Approved Comments
	theme_blogsListCommentsApprovedTableHead();
	$count = 0;
	if(count($data->output['commentList']['approved']) > 0)
	{
		foreach($data->output['commentList']['approved'] as $item)
		{
			theme_blogsListCommentsApprovedTableRow($data,$item,$count);
			$count++;
		}
	} else {
		theme_blogsListCommentsNoApproved();
	}
	theme_blogsListCommentsTableFoot();
	//--------------------
	// Disapproved Comments
	theme_blogsListCommentsDisapprovedTableHead();
	$count = 0;
	if(count($data->output['commentList']['disapproved']) > 0)
	{
		foreach($data->output['commentList']['disapproved'] as $item)
		{
			theme_blogsListCommentsDisapprovedTableRow($data,$item,$count);
			$count++;
		}
	} else {
		theme_blogsListCommentsNoDisapproved();
	}
	theme_blogsListCommentsTableFoot();
	//---------------------------
}
?>