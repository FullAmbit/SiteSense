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
common_include('libraries/forms.php');
function admin_blogsBuild($data,$db)
{
	if(is_numeric($data->action[3]))
	{
		// Retrieve Comment
		$statement = $db->prepare('getCommentById','blogcomments');
		$statement->execute(array(':blogId' => $data->action[3]));
		if(($data->output['commentItem'] = $statement->fetch()) === FALSE)
		{
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>The ID does not exist in database</h2>';
			return;
		}
		
		/* Permission Check-----------------------
		 * If the user is anything less than a moderator,
		 * check to see if the user owns the blog this
		 * is under.
		 * ---------------------------------------
		**/
		if(checkPermission('canEditBlogComments','blogs',$data))
		{
			$statement = $db->prepare('getBlogByPost','admin_blogs');
			$statement->execute(array(
				':postId' => $data->output['commentItem']['post']
			));
			
			$blogItem = $statement->fetch();
			if($data->user['id'] !== $blogItem['owner'])
			{
				$data->output['savedOkMessage'] = '<h2>You do not have the permissions to modify this comment</h2>';
				return;
			}
		}
		
		$data->output['commentItemForm'] = new formHandler('commentItem',$data,true);
		$data->output['commentItemForm']->caption = 'Editing Comment';
		if((!empty($_POST['fromForm'])) && ($_POST['fromForm'] == $data->output['commentItemForm']->fromForm))
		{
			// Populate data
			$data->output['commentItemForm']->populateFromPostData();
			// Validation
			if($data->output['commentItemForm']->validateFromPost())
			{
				// Load BBCode
				if($data->settings['useBBCode'])
				{
					common_loadPlugin($data,'bbcode');
					$data->output['commentItemForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['commentItemForm']->sendArray[':rawContent']);
				} else {
					$data->output['commentItemForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['commentItemForm']->sendArray[':rawContent']);
				}
				// SQL Save Statement
				$statement = $db->prepare('editCommentById','blogcomments');
				$data->output['commentItemForm']->sendArray[':id'] = $data->action[3];
				//var_dump($data->output['commentItemForm']->sendArray);
				$statement->execute($data->output['commentItemForm']->sendArray) or die('Saving Comment Item Failed');
				if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>Project Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/blogs/">
							Return to Blogs List
						</a>
					</div>';
				}
			} else {
				$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
			}
		}		
	}
}
function admin_blogsShow($data)
{
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['commentItemForm']);
	}
}
?>