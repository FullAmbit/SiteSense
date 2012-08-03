<?php

common_include('libraries/forms.php');

function hostnames_admin_edit_build($data,$db){
	// Check If HostName Exists
	$statement = $db->prepare('getHostname','admin_hostnames');
	$statement->execute(array(
		':hostname' => $data->action[3]
	));
	if(($data->output['hostnameItem'] = $statement->fetch(PDO::FETCH_ASSOC)) == FALSE){
		$data->output['themeOverride'] = 'NotFound';
		return;
	}
	
	$data->output['hostnameForm'] = new formHandler('hostnameItem',$data,true);
	$data->output['hostnameForm']->caption = 'Add A Hostname';
	$list=glob('themes/*');
	foreach ($list as $theme) {
		if (filetype($theme)=='dir') {
			$data->output['hostnameForm']->fields['defaultTheme']['options'][]=substr(strrchr($theme,'/'),1);
		}
	}
	$getModules = $db->query('getEnabledModules','admin_modules');
	$modules = $getModules->fetchAll();
	// All Enabled Modules
	foreach($modules as $module){
		if($module['shortName'] == 'pages' || $module['shortName'] == 'ajax' || $module['shortName'] == 'users') continue;
		$option = array(
			'text' => $module['shortName'],
			'value' => $module['shortName'],
			'optgroup' => 'Modules'
		);
		$data->output['hostnameForm']->fields['homepage']['options'][] = $option;
	}
	// Get All Top Level Pages //
	$statement = $db->prepare('getTopLevelPages','admin_pages');
	$statement->execute();
	$pageList = $statement->fetchAll();
	if(count($pageList) > 0)
	{
		foreach($pageList as $pageItem)
		{
			$option = array(
				'text' => $pageItem['shortName'],
				'value' => $pageItem['shortName'],
				'optgroup' => 'Pages'
			);
			$data->output['hostnameForm']->fields['homepage']['options'][] = $option;
		}
	}
	
	// All Languages //
	$statement = $db->prepare('getAllLanguages','admin_languages');
	$statement->execute();
	$languageList = $statement->fetchAll(PDO::FETCH_ASSOC);
	foreach($languageList as $languageItem){
		$data->output['hostnameForm']->fields['defaultLanguage']['options'][] = array(
			'text' => $languageItem['name'],
			'value' => $languageItem['shortName']
		);
	}
	
	if(isset($_POST['fromForm']) && $_POST['fromForm'] == $data->output['hostnameForm']->fromForm){
		if($data->output['hostnameForm']->validateFromPost()){
			$data->output['hostnameForm']->populateFromPostData();
			// Make Sure The HostName Doesn't Already Exist. But Only Check If Different
			if($data->output['hostnameForm']->sendArray[':hostname'] !== $data->output['hostnameItem']['hostname']){
				$statement = $db->prepare('getHostname','admin_hostnames');
				$statement->execute(array(
					':hostname' => $data->output['hostnameForm']->sendArray[':hostname']
				));
				if($statement->fetch()){
					// Host Name Found..Abort....
					$data->output['hostnameForm']->error = true;
					$data->output['hostnameForm']->fields['hostname']['error'] = true;
					$data->output['hostnameForm']->fields['hostname']['errorList'][] = 'That hostname already exists in the database.';
					return;
				}

			}
			$data->output['hostnameForm']->sendArray[':originalHostName'] = $data->output['hostnameItem']['hostname'];
			$statement = $db->prepare('updateHostname','admin_hostnames');
			$result = $statement->execute($data->output['hostnameForm']->sendArray);
			if($result){
				$data->output['themeOverride'] = 'EditSuccess';
			}else{
				$data->output['responseMessage'] = 'There was an error in saving to the database.';
			}
		}
	}
}

function hostnames_admin_edit_content($data){
	if(isset($data->output['themeOverride'])){
		$func = 'theme_hostnames'.$data->output['themeOverride'];
		$func($data);
		return;
	}
	theme_hostnamesEdit($data);
}
?>