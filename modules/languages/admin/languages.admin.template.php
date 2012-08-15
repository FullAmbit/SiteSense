<?php

function theme_languagesList($data){
	echo 
	'
	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/languages/update/">',$data->phrases['languages']['updateButton'],'</a>
	</div>
	<table width="100%">
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
				<td>
					<a href="',$data->linkRoot,'admin/languages/listPhrases/',$languageItem['shortName'],'">',$data->phrases['languages']['phrases'],'</a>',
						(($languageItem['isDefault']=='0') ? '<a href="'.$data->linkRoot.'admin/languages/default/'.$languageItem['shortName'].'">'.$data->phrases['languages']['makeDefault'].'</a>' : '<b>'.$data->phrases['languages']['default'].'</b>'),'
				</td>
			</tr>';
	}
	echo
	'	</tbody>
	</table>';
}

function theme_languagesListPhrases($data){
	echo 
	'
	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/languages/addPhrase/',$data->output['languageItem']['shortName'],'">',$data->phrases['languages']['addAPhrase'],'</a>
	</div>
	<table width="100%">
		<caption>',$data->output['languageItem']['name'],' ',$data->phrases['languages']['phrases'],'</caption>
		<thead>
			<tr>
				<th>',$data->phrases['languages']['phrase'],'</th>
				<th>',$data->phrases['languages']['text'],'</th>
				<th>',$data->phrases['languages']['module'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['phraseList'] as $phraseItem){
		echo 
		'	<tr>
				<td>',$phraseItem['phrase'],'</td>
				<td>',$phraseItem['text'],'</td>
				<td>',($phraseItem['module'] == '') ? 'Global' : $phraseItem['module'],'</td>
				<td><a href="',$data->linkRoot,'admin/languages/editPhrase/',$data->output['languageItem']['shortName'],'/',$phraseItem['id'],'">',$data->phrases['core']['actionModify'],'</a></td>
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
	$data->output['phraseForm']->caption = $data->phrases['languages']['captionAddPhrase'].' '.$data->output['languageItem']['name'];
	$data->output['phraseForm']->build();
}

function theme_languagesEditPhrase($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	$data->output['phraseForm']->caption = $data->phrases['languages']['captionEditPhrase'].' '.$data->output['phraseItem']['phrase'].' - '.$data->phrases['languages']['language'].': '.$data->output['languageItem']['name'];
	$data->output['phraseForm']->build();
}

function theme_languagesAddPhraseSuccess($data){
	echo $data->phrases['languages']['addPhraseSuccess'],' - ',$data->output['phraseForm']->sendArray[':phrase'];
}

function theme_languagesEditPhraseSuccess($data){
	echo '<h2>',$data->phrases['languages']['editPhraseSuccess'],'</h2>';
}

function theme_languagesUpdate($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption>',$data->phrases['languages']['captionUpdate'],'</caption><br />
		',$data->phrases['languages']['language'],'
		<select name="updateLanguage">';
	foreach($data->output['languageList'] as $languageItem){
	
		echo
		'	<option value="',$languageItem['shortName'],'">',$languageItem['name'],'</option>';
	}
	echo '
		</select><br />
		',$data->phrases['languages']['phraseAction'],'
		<select name="action">
			<option value="0">',$data->phrases['languages']['updateActionClear'],'</option>
			<option value="1">',$data->phrases['languages']['updateActionNew'],'</option>
			<option value="2">',$data->phrases['languages']['updateActionAll'],'</option>
		</select><br />
		',$data->phrases['languages']['updateModuleLanguages'],'<input type="checkbox" name="updateModules" value="1" /><br />
		<input type="submit" name="install" value="',$data->phrases['languages']['install'],'" />
	</form>';
}

function theme_languagesUpdateSuccess($data){
	echo '<h2>',$data->phrases['languages']['updateLanguageSuccess'],' - ',$data->output['languageList'][$_POST['updateLanguage']]['name'],'</h2>';
}