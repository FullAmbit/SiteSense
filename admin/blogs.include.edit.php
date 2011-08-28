<?php

common_include('libraries/forms.php');

function admin_blogsCheckShortName($db,$shortName) {
	$statement=$db->prepare('getBlogIdByName','admin_blogs');
	$statement->execute(array(
		':shortName' => $shortName
	));
	if ($first=$statement->fetch()) {
		return $first['id'];
	} else return false;
}

function admin_blogsBuild($data,$db) {
global $languageText;

	$aRoot=$data->linkRoot.'admin/blogs/';

	$data->output['blogForm']=new formHandler('blogsEdit',$data,true);

	$statement=$db->query('getBloggersByUserLevel','admin_blogs');

	while ($item=$statement->fetch()) {
		$data->output['blogForm']->fields['owner']['options'][]=array(
			'value' => $item['id'],
			'text' => $item['name'].' - '.$languageText['userLevels'][$item['userLevel']]
		);
	}

	foreach ($languageText['userLevels'] as $value => $text) {
		if ($value>=USERLEVEL_MODERATOR) {
			$data->output['blogForm']->fields['minPermission']['options'][]=array(
				'value' => $value,
				'text' => $text
			);
		}
	}

	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['blogForm']->fromForm)
	) {
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['blogForm']->caption='Editing Blog '.$data->action[3];
		$data->output['blogForm']->populateFromPostData();
		$shortName=preg_replace('/\W/i','',str_replace(' ','_',$_POST[$data->output['blogForm']->formPrefix.'name']));
		$shortNameExists=admin_blogsCheckShortName($db,$shortName);
		if ($data->output['blogForm']->validateFromPost()) {
			if (is_numeric($data->action[3])) {
				$statement=$db->prepare('updateBlogById','admin_blogs');
				$data->output['blogForm']->sendArray[':id']=$data->action[3];
				if (
					$shortNameExists &&
					($shortNameExists!=$data->action[3])
				) {
					$shortName.='_'.$data->action[3];
				}
				$data->output['blogForm']->sendArray[':shortName']=$shortName;
				$statement->execute($data->output['blogForm']->sendArray);
			} else { /* came from form, must be new */
				$statement=$db->prepare('insertBlog','admin_blogs');
				$data->output['blogForm']->sendArray[':shortName']=$shortName;
				$statement->execute($data->output['blogForm']->sendArray);
				if ($shortNameExists) {
					$tempID=$db->lastInsertId();
					$shortName.='_'.$tempID;
					$statement=$db->prepare('updateShortNameById','admin_blogs');
					$statement->execute(array(
						':shortName'=> $shortName,
						':id' => $tempID
					));
				}
			}
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'edit/new">
						Add New Page
					</a>
					<a href="'.$aRoot.'list/">
						Return to Page List
					</a>
				</div>';
		} else {
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}

	} else if (is_numeric($data->action[3])) {

		/* editing an existing from the database */

		$data->output['blogForm']->caption='Editing Blog '.$data->action[3];
		$statement=$db->prepare('getBlogById','admin_blogs');
		$statement->execute(array(
			'id' => $data->action[3]
		));
		if ($item=$statement->fetch()) {
			foreach ($data->output['blogForm']->fields as $key => $value) {
				if (
					(!empty($value['params']['type'])) &&
					($value['params']['type']=='checkbox')
				) {
					$data->output['blogForm']->fields[$key]['checked']=(
						$item[$key] ? 'checked' : ''
					);
				} else {
					$data->output['blogForm']->fields[$key]['value']=$item[$key];
				}
			}
		}
	} else if ($data->action[3]!='new') {
		/* if it's not new, not numbered, and didn't come from form... */
		$data->output['editError']='unknown function';
	}

}

function admin_blogsShow($data) {
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['blogForm']);
	}
}

?>