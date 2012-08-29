<?php
function theme_navPanel($data) {
	echo '	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/languages/newlanguage/">',$data->phrases['languages']['install'],'</a>
		<a href="',$data->linkRoot,'admin/languages/listphrases/">',$data->phrases['languages']['modifyPhrases'],'</a>
		<a href="',$data->linkRoot,'admin/languages/listphrases/overrides">',$data->phrases['languages']['modifyOverrides'],'</a>
	</div>';
}
function theme_languagesList($data){
	theme_navPanel($data);
	echo 
	'

	<table class="pagesList">
		<caption>',$data->phrases['languages']['manageLanguagesHeading'],'</caption>
		<thead>
			<tr>
				<th>',$data->phrases['languages']['language'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['languageList'] as $languageItem){
		echo 
		'	<tr>
				<td>',$languageItem['name'],'</td>
				<td class="buttonList">',
						(($languageItem['isDefault']=='0') ? '<a href="'.$data->linkRoot.'admin/languages/default/'.$languageItem['shortName'].'">'.$data->phrases['languages']['makeDefault'].'</a>' : ''),'&nbsp;',	(($languageItem['shortName']=='en_us') ? '<a href="'.$data->linkRoot.'admin/languages/installPhrases">Install Phrases</a>' : '<a href="'.$data->linkRoot.'admin/languages/updateTranslation/'.$languageItem['shortName'].'">Update Translation</a>'),'
				</td>
			</tr>';
	}
	echo
	'	</tbody>
	</table>';
}

function theme_languagesListPhrases($data){
	echo '
	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/languages/addPhrase/',$data->output['languageItem']['shortName'],'">',$data->phrases['languages']['addAPhrase'],'</a>
	</div>
	<table class="pagesList">
		<caption>',$data->output['languageItem']['name'],' ',$data->phrases['languages']['phrases'],' - ',($data->action[3]=='overrides') ? $data->phrases['languages']['modifyOverrides'] : $data->phrases['languages']['modifyPhrases'],'</caption>
		<thead>
			<tr>
				<th>',$data->phrases['languages']['phrase'],'</th>
				<th>',$data->phrases['languages']['text'],'</th>
				<th>',$data->phrases['languages']['module'],'</th>
				<th>',$data->phrases['languages']['level'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['phraseList'] as $phraseItem){
		echo 
		'	<tr>
				<td>',$phraseItem['phrase'],'</td>
				<td>',$phraseItem['text'],'</td>
				<td>',($phraseItem['module'] == '') ? $data->phrases['languages']['global'] : $phraseItem['module'],'</td>
				<td>',$data->phrases['languages']['isAdmin_' . $phraseItem['isAdmin']],'</td>
				<td><a href="',$data->linkRoot,'admin/languages/editPhrase/',$phraseItem['id'],'">',$data->phrases['core']['actionModify'],'</a>
				</td>
			</tr>';
	}
	echo
	'	</tbody>
	</table>';
}

function theme_languagesNotFound($data){
	echo '<h2>',$data->phrases['languages']['languageNotFound'],'</h2>';
}

function theme_languagesPhraseNotFound($data){
	echo '<h2>',$data->phrases['language']['phraseNotFound'],' - ',$data->phrases['languages']['language'],': ',$data->output['languageItem']['name'],'</h2>';
}

function theme_languagesAddPhrase($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	$data->output['phraseForm']->caption = $data->phrases['languages']['captionAddPhrase'];
	$data->output['phraseForm']->build();
}

function theme_languagesEditPhrase($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	$data->output['phraseForm']->caption = $data->phrases['languages']['captionEditPhrase'].' '.$data->output['phraseItem']['en_us']['phrase'];
	$data->output['phraseForm']->build();
}

function theme_languagesAddPhraseSuccess($data){
	echo $data->phrases['languages']['addPhraseSuccess'],' - ',$data->output['phraseForm']->sendArray[':phrase'];
}

function theme_languagesEditPhraseSuccess($data){
	echo '<h2>',$data->phrases['languages']['editPhraseSuccess'],'</h2>';
}

function theme_languagesInstallPhrases($data){
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption>',$data->phrases['languages']['captionUpdate'],'</caption><br />
		',$data->phrases['languages']['phraseAction'],'
		<select name="action">
			<option value="0">',$data->phrases['languages']['updateActionClear'],'</option>
			<option value="1" selected="selected">',$data->phrases['languages']['updateActionNew'],'</option>
			<option value="2">',$data->phrases['languages']['updateActionAll'],'</option>
		</select><br />
		',$data->phrases['languages']['updateModuleLanguages'],'<input checked="checked" type="checkbox" name="updateModules" value="1" /><br />
		<input type="submit" name="install" value="',$data->phrases['languages']['install'],'" />
	</form>';
}

function theme_languagesEnglishNotFound($data){
	echo '<h2>The core English phrases file was not found.</h2>';
}

function theme_languagesInstallPhrasesEnglishError($data){
	echo '<h2>There was an error while saving phrases for the English language</h2>';
}

function theme_languagesUpdateSuccess($data){
	echo '<h2>',$data->phrases['languages']['updateLanguageSuccess'],' - ',$data->output['languageList'][$_POST['updateLanguage']]['name'],'</h2>';
}
	
function theme_languagesUpdateTranslation($data){
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption><h2>Update Phrase Translations For The ',$data->output['languageItem']['name'],' Language</h2></caption><br />
		Update Module Phrases?<input checked="checked" type="checkbox" name="updateModules" value="1" /><br />
		<input type="submit" name="install" value="Update Phrases" />
	</form>';
}

function theme_languagesUpdateTranslationSuccess($data){
	$list = array('userErrors','adminErrors');
	$error = FALSE;
	foreach($list as $index => $varName){
		if(isset($data->output[$varName]) && !empty($data->output[$varName])){
			$error = TRUE;
			break;
		}
	}
	if($error == FALSE){
		echo '<h2>The translation went smoothly and all phrases were updated.</h2>';
	}else{
		echo '<h2>Existing phrases were updated, however some phrases were found without an English counterpart. Please add the following phrases to English first.</h2>
		<br />';
		
		if(isset($data->output['userErrors']) && !empty($data->output['userErrors'])){
			echo '<h1>User Phrases</h1>';
			foreach($data->output['userErrors'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
		
		if(isset($data->output['adminErrors']) && !empty($data->output['adminErrors'])){
			echo '<h1>Admin Phrases</h1>';
			foreach($data->output['adminErrors'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
	}
}

function theme_languagesNewLanguage($data){
	if(empty($data->output['languageList'])){
		echo '<h2>There are are no new languages for you to install.</h2>';
		return;
	}
	
	echo '<form name="newLanguage" method="post" action="',$data->linkRoot,'admin/languages/newlanguage/">Select A Language: <select name="newLanguage">';
	foreach($data->output['languageList'] as $languageShortName => $languageItem){
		echo '<option value="',$languageItem['shortName'],'">',$languageItem['name'],'</option>';
	}
	echo '</select><input type="submit" name="install" value="Add Language" /></form>';
}

function theme_languagesNewLanguageSuccess($data){
	$list = array('userErrors','adminErrors');
	$existingError = FALSE;
	foreach($list as $index => $varName){
		if(isset($data->output[$varName]) && !empty($data->output[$varName])){
			$existingError = TRUE;
			break;
		}
	}
	if($existingError){
		echo '<h2>Existing phrases were updated, however some phrases were found without an English counterpart. Please add the following phrases to English first.</h2>
		<br />';
		
		if(isset($data->output['userErrors']) && !empty($data->output['userErrors'])){
			echo '<h1>User Phrases</h1>';
			foreach($data->output['userErrors'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
		
		if(isset($data->output['adminErrors']) && !empty($data->output['adminErrors'])){
			echo '<h1>Admin Phrases</h1>';
			foreach($data->output['adminErrors'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
	}
	// Check If We Have Errors With New Phrases Being Added
	$list = array('userNew','adminNew');
	$newError = FALSE;
	foreach($list as $index => $varName){
		if(isset($data->output[$varName]) && !empty($data->output[$varName])){
			$newError = TRUE;
			break;
		}
	}
	
	if($newError){
		echo '<h2>The following phrases were not found in the language file and taken from English.</h2>
		<br />';
		
		if(isset($data->output['userNew']) && !empty($data->output['userNew'])){
			echo '<h1>User Phrases</h1>';
			foreach($data->output['userNew'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
		
		if(isset($data->output['adminNew']) && !empty($data->output['adminNew'])){
			echo '<h1>Admin Phrases</h1>';
			foreach($data->output['adminNew'] as $module => $phraseList){
				echo '
					<u><b>Module: '.$module.'</b></u>
					<ul>';
				foreach($phraseList as $index => $phrase){
					echo '<li style="margin-left:20px;">'.$phrase.'</li>';
				}
				echo '</ul>';
			}
		}
	}
}