<?php

common_include('libraries/forms.php');

function admin_sideBarsCheckShortName($db,$shortName) {
	$statement=$db->prepare('getIdByShortName','admin_sideBars');
	$statement->execute(array(
		':shortName' => $shortName
	));
	if ($first=$statement->fetch()) {
		return $first['id'];
	} else return false;
}

function admin_sideBarsBuild($data,$db) {
	$aRoot=$data->linkRoot.'admin/sideBars/';

	$data->output['sideBarForm']=new formHandler('sideBars',$data,true);

	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['sideBarForm']->fromForm)
	) {
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['sideBarForm']->caption='Editing Page '.$data->action[3];
		$data->output['sideBarForm']->populateFromPostData();
		$shortName=preg_replace('/\W/i','',str_replace(' ','_',$_POST[$data->output['sideBarForm']->formPrefix.'title']));
		$shortNameExists=admin_sideBarsCheckShortName($db,$shortName);
		if ($data->output['sideBarForm']->validateFromPost()) {
			if (is_numeric($data->action[3])) {
				$statement=$db->prepare('updateById','admin_sideBars');
				$data->output['sideBarForm']->sendArray[':id']=$data->action[3];
				if (
					$shortNameExists &&
					($shortNameExists!=$data->action[3])
				) {
					$shortName.='_'.$data->action[3];
				}
				$data->output['sideBarForm']->sendArray[':shortName']=$shortName;
				$statement->execute($data->output['sideBarForm']->sendArray);
			} else { /* came from form, must be new */
				$statement=$db->prepare('insertSideBar','admin_sideBars');
				$data->output['sideBarForm']->sendArray[':shortName']=$shortName;
				$statement->execute($data->output['sideBarForm']->sendArray);
				if ($shortNameExists) {
					$tempID=$db->lastInsertId();
					$shortName.='_'.$tempID;
					$statement=$db->prepare('updateShortNameById','admin_sideBars');
					$statement->execute(array(
						':shortName'=> $shortName,
						':id' => $tempID
					));
				}
			}

			admin_sideBarsResort($db);

			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$sendArray[':shortName'].'
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

		$data->output['sideBarForm']->caption='Editing Page '.$data->action[3];
		$statement=$db->prepare('getById','admin_sideBars');
		$statement->execute(array(
			'id' => $data->action[3]
		));
		if ($item=$statement->fetch()) {
			foreach ($data->output['sideBarForm']->fields as $key => $value) {
				if (
					(!empty($value['params']['type'])) &&
					($value['params']['type']=='checkbox')
				) {
					$data->output['sideBarForm']->fields[$key]['checked']=(
						$item[$key] ? 'checked' : ''
					);
				} else {
					$data->output['sideBarForm']->fields[$key]['value']=$item[$key];
				}
			}
		}
	} else if ($data->action[3]!='new') {
		/* if it's not new, not numbered, and didn't come from form... */
		$data->output['editError']='unknown function';
	}

}

function admin_sideBarsShow($data) {
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['sideBarForm']);
	}
}

?>