<?php

function admin_usersBuild($data,$db) {
	$data->output['delete']='';
	if (empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
	} else {
		if ($data->user['userLevel']>=USERLEVEL_ADMIN) {
			if ($_POST['formForm']==$data->action[3]) {
				if (!empty($_POST['delete'])) {
					$qHandle=$db->prepare('deleteUserById','admin_users');
					$qHandle->execute(array(
						':id' => $data->action[3]
					));
					$data->output['deleteCount']=$qHandle->rowCount();
					if ($data->output['deleteCount']>0) {
						$data->output['delete']='deleted';
					} else {
						$data->output['rejectError']='Database Error';
						$data->output['rejectText']='You attempted to delete a user, are you sure that user exists?';
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

function admin_usersShow($data) {
	if (empty($data->output['rejectError'])) {
		switch ($data->output['delete']) {
			case 'deleted':
				echo '
				<h2>User #',$data->action[3],' Deleted</h2>
				<p>
					This action deleted a total of ',$data->output['deleteCount'],' users!
				</p>
				<div class="buttonList">
					<a href="',$data->linkRoot,'admin/users/list">Return to List</a>
				</div>
				';
			break;
			case 'cancelled':
				echo '
				<h2>Deletion Cancelled</h2>
				<p>
					You should be auto redirected to the page list in three seconds.
					<a href="',$data->linkRoot,'admin/users/list">Click Here if you don not wish to wait.</a>
				</p>';
			break;
			default:
				echo '
				<form action="',$data->linkRoot,'admin/users/delete/',$data->action[3],'" method="post" class="verifyForm">
					<fieldset>
						<legend><span>Are you sure you want to delete user #',$data->action[3],'?</span></legend>
						<p class="warning">*** WARNING *** This action cannot be undone</p>
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