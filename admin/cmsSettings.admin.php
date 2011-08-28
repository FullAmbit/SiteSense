<?php

common_include('libraries/forms.php');

function admin_buildContent($data,$db) {

	$data->output['settingsForm']=new formHandler('cmsSettings',$data,true);
	$getModules = $db->query('getEnabledModules', 'modules');
	$modules = $getModules->fetchAll();
	foreach($modules as $module){
		$data->output['settingsForm']->fields['defaultModule']['options'][] = $module['shortName'];
	}
	if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->output['settingsForm']->fromForm) {
		if ($data->output['formOk']=$data->output['settingsForm']->validateFromPost()) {
			$data->output['secondSideBar']='
				<h2>Settings Saved</h2>
				<ul class="updateList">';
			$statement=$db->prepare('updateSettings','admin_cmsSettings');
			foreach ($data->output['settingsForm']->fields as $fieldKey => $fieldData) {

				if (!empty($fieldData['updated'])) {
					$data->output['secondSideBar'].='
						<li class="changed"><b>'.$fieldKey.'</b><span> updated</span></li>';
					$statement->execute(array(
						'value' => $fieldData[$fieldData['updated']],
						'name' => $fieldKey
					));
				} else $data->output['secondSideBar'].='
					<li><b>'.$fieldKey.'</b><span> unchanged</span></li>';
			}
			$data->output['secondSideBar'].='
				</ul>';
		} else {
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}

	/* some values need logic flow to set */
	$list=glob('themes/*');
	foreach ($list as $theme) {
		if (filetype($theme)=='dir') {
			$data->output['settingsForm']->fields['theme']['options'][]=substr(strrchr($theme,'/'),1);
		}
	}

	$data->output['pageTitle']='Global Settings';

}

function admin_content($data) {
	theme_buildForm($data->output['settingsForm']);
}
?>