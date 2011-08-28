<?php

common_include('libraries/forms.php');

function admin_pagesCheckShortNameAndParent($db,$shortName,$parent) {
	$statement=$db->prepare('getPageIdByShortNameAndParent','admin_pages');
	$statement->execute(array(
		':shortName' => $shortName,
		':parent' => $parent
	));
	if ($first=$statement->fetch()) {
		return $first['id'];
	} else return false;
}
function admin_pageOptions($db, $Parent = -1, $Level = 0){ // Using a function is necessary here for recursion
	$options = array();
	if($Parent == -1){
		$options[] = array('value' => 0, 'text' => 'Site Root');
		$options = array_merge($options, admin_pageOptions($db, 0, 1));
	}else{
		$statement = $db->prepare('getPageListByParent', 'admin_pages');
		$statement->execute(array(':parent' => $Parent));
		while($item = $statement->fetch()){
			$options[] = array(
				'value' => $item['id'],
				'text' => str_repeat('-', $Level * 4) . ' ' . $item['shortName']
			);
			$options = array_merge($options, admin_pageOptions($db, $item['id'], $Level + 1));
		}
	}
	return $options;
}
function admin_pagesBuild($data,$db) {

	$data->output['pageForm']=new formHandler('pagesEdit',$data,true);

	$data->output['pageForm']->fields['parent']['options'] = admin_pageOptions($db);
	
	if (
		($data->action[3]=='new') &&
		($data->action[4]=='childOf') &&
		is_numeric($data->action[5])
	) {
		$data->output['pageForm']->fields['parent']['value']=$data->action[5];
	}

	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['pageForm']->fromForm)
	) {
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['pageForm']->caption='Editing Page '.$data->action[3];
		$data->output['pageForm']->populateFromPostData();
		$shortName=preg_replace('/\W-/i','',str_replace(' ','-',strtolower($_POST[$data->output['pageForm']->formPrefix.'title'])));
		$shortNameExists=admin_pagesCheckShortNameAndParent($db,$shortName,$data->output['pageForm']->fields['parent']['value']);
		$data->output['pageForm']->sendArray[':shortName']=$shortName;
		if ($data->output['pageForm']->validateFromPost()) {
			if (is_numeric($data->action[3])) {
				$statement=$db->prepare('updatePageById','admin_pages');
				$data->output['pageForm']->sendArray[':id']=$data->action[3];
				if (
					$shortNameExists &&
					($shortNameExists!=$data->action[3])
				) {
					$shortName.='_'.$data->action[3];
				}
				$data->output['pageForm']->sendArray[':shortName']=$shortName;
				$statement->execute($data->output['pageForm']->sendArray);
			} else { /* came from form, must be new */
				$statement=$db->prepare('insertPage','admin_pages');
				$statement->execute($data->output['pageForm']->sendArray);
				if ($shortNameExists) {
					$tempID=$db->lastInsertId();
					$shortName.='_'.$tempID;
					$statement=$db->prepare('updateShortNameById','admin_pages');
					$statement->execute(array(
						':shortName'=> $shortName,
						':id' => $tempID
					));
				}
			}
			
			admin_pagesResort($db);
			admin_mainMenuRebuild($data,$db);

			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$data->linkRoot.'admin/pages/edit/new">
						Add New Page
					</a>
					<a href="'.$data->linkRoot.'admin/pages/list/">
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

		$data->output['pageForm']->caption='Editing Page '.$data->action[3];
		$statement=$db->prepare('getPageById','admin_pages');
		$statement->execute(array(
			'id' => $data->action[3]
		));
		if ($item=$statement->fetch()) {
			foreach ($data->output['pageForm']->fields as $key => $value) {
				if (
					(!empty($value['params']['type'])) &&
					($value['params']['type']=='checkbox')
				) {
					$data->output['pageForm']->fields[$key]['checked']=(
						$item[$key] ? 'checked' : ''
					);
				} else {
					$data->output['pageForm']->fields[$key]['value']=$item[$key];
				}
			}
		}
	} else if ($data->action[3]!='new') {
		/* if it's not new, not numbered, and didn't come from form... */
		$data['editError']='unknown function';
	}

}

function admin_pagesShow($data) {
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['pageForm']);
	}
}

?>