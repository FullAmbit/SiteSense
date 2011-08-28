<?php

function admin_blogsBuild($data,$db) {
	$data->output['delete']='';
	if (empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
	} else {
		if ($data->user['userLevel']>=USERLEVEL_WRITER) {
			$qHandle=$db->prepare('getBlogById','admin_blogs');
			$qHandle->execute(array(
				':id' => $data->action[3]
			));
			$data->output['thisBlog']=$qHandle->fetch();
			if (isset($_POST['formForm']) && $_POST['formForm']==$data->action[3]) {
				if (!empty($_POST['delete'])) {
					$qHandle=$db->prepare('deleteBlogById','admin_blogs');
					$qHandle->execute(array(
						':id' => $data->action[3]
					));
					$data->output['deleteCount']=$qHandle->rowCount();
					if ($data->output['deleteCount']>0) {
						$qHandle=$db->prepare('deleteBlogPostByBlogId','admin_blogs');
						$qHandle->execute(array(
							':id' => $data->action[3]
						));
						$data->output['deletePostCount']=$qHandle->rowCount();
						$data->output['delete']='deleted';
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
				echo '
				<h2>Entry #',$data->action[3],' - ',$data->output['thisBlog']['name'],' Deleted</h2>
				<p>
					This action deleted a total of ',$data->output['deleteCount'],' blogs containing ',$data->output['deletePostCount'],' Posts!
				</p>
				<div class="buttonList">
					<a href="',$aRoot,'list">Return to List</a>
				</div>
				';
			break;
			case 'cancelled':
				echo '
				<h2>Deletion Cancelled</h2>
				<p>
					You should be auto redirected to the blog list in three seconds.
					<a href="',$aRoot,'list">Click Here if you don not wish to wait.</a>
				</p>';
			break;
			default:
				echo '
				<form action="',$aRoot,'delete/',$data->action[3],'" method="post" class="verifyForm">
					<fieldset>
						<legend><span>Are you sure you want to delete blog #',$data->action[3],' "',$data->output['thisBlog']['name'],'"?</span></legend>
						<input type="submit" name="delete" value="Yes, Delete it" />
						<input type="submit" name="cancel" value="Cancel" />
						<input type="hidden" name="formForm" value="',$data->action[3],'" />
					</fieldset>
				</form>';
			break;
		}
	} else {
		echo '
			<h2>',$data->output['rejectError'],'</h2>',$data->output['rejectText'];
	}
}

?>