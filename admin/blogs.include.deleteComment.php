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
	//var_dump($data->action);
	$data->output['delete']='';
	// Make Sure We Have An ID
	if(!is_numeric($data->action[3]))
	{
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
		return;
	}
	// Check for User Permissions
	if (!in_array('canDeleteBlogComment',$data->user['permissions']['blogs']))
	{
		$data->output['rejectError']='Insufficient User Permissions';
		$data->output['rejectText']='You do not have sufficient access to perform this action.';
		return;
	}
	
	// Get The Comment Info So Far
	$statement = $db->prepare('getCommentById','admin_blogcomments');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['commentItem'] = $statement->fetch();
	
	/* Permission Check-----------------------
	 * If the user is anything less than a moderator,
	 * check to see if the user owns the blog this comment
	 * is under.
	 * ---------------------------------------
	**/
	if(in_array('canDeleteBlogComment',$data->user['permissons']['blogs']))
	{
		$statement = $db->prepare('getBlogByPost','admin_blogs');
		$statement->execute(array(
			':postId' => $data->output['commentItem']['post']
		));
		
		$blogItem = $statement->fetch();
		if($data->user['id'] !== $blogItem['owner'])
		{
			$data->output['savedOkMessage'] = '<h2>You do not have the permissions to modify this blog</h2>';
			return;
		}
	}
	
	// Delete Comment
	if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3])
	{
		if(!empty($_POST['delete']))
		{
			$statement = $db->prepare('deleteCommentById','blogcomments');
			$statement->execute(array(':id' => $data->action[3]));
			$data->output['delete']='deleted';
		} else {
			$data->output['delete']='cancelled';
		}
	}
}
function admin_blogsShow($data)
{
	$aRoot=$data->linkRoot.'admin/blogs/';
	if(empty($data->output['rejectError']))
	{
		switch($data->output['delete'])
		{
			case 'deleted':
					theme_blogsDeleteCommentDeleted($aRoot);
				break;
			case 'cancelled':
					theme_blogsDeleteCommentCancelled($aRoot);
				break;
			default:
					theme_blogsDeleteCommentDefault($data,$aRoot);
				break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>