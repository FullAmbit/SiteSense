<?php

function theme_languagesList($data){
	echo 
	'
	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/languages/update/">Update From File System</a>
	</div>
	<table width="100%">
		<caption>Installed Languages</caption>
		<thead>
			<tr>
				<th>Language</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['languageList'] as $languageItem){
		echo 
		'	<tr>
				<td>',$languageItem['name'],'</td>
				<td><a href="',$data->linkRoot,'admin/languages/listPhrases/',$languageItem['shortName'],'">Phrases</a></td>
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
		<a href="',$data->linkRoot,'admin/languages/addPhrase/',$data->output['languageItem']['shortName'],'">Add A Phrase</a>
	</div>
	<table width="100%">
		<caption>',$data->output['languageItem']['name'],' Phrases</caption>
		<thead>
			<tr>
				<th>Phrase</th>
				<th>Text</th>
				<th>Module</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['phraseList'] as $phraseItem){
		echo 
		'	<tr>
				<td>',$phraseItem['phrase'],'</td>
				<td>',$phraseItem['text'],'</td>
				<td>',($phraseItem['module'] == '') ? 'Global' : $phraseItem['module'],'</td>
				<td><a href="',$data->linkRoot,'admin/languages/editPhrase/',$data->output['languageItem']['shortName'],'/',$phraseItem['id'],'">Modify</a></td>
			</tr>';
	}
	echo
	'	</tbody>
	</table>';
}

function theme_languagesNotFound($data){
	echo '<h2>The language you specified could not be found</h2>';
}

function theme_languagesPhraseNotFound($data){
	echo '<h2>The phrase you specified could not be found for the language ',$data->output['languageItem']['name'],'</h2>';
}

function theme_languagesAddPhrase($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	$data->output['phraseForm']->caption = 'Add A Phrase For '.$data->output['languageItem']['name'];
	$data->output['phraseForm']->build();
}

function theme_languagesEditPhrase($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	$data->output['phraseForm']->caption = 'Editing The Phrase '.$data->output['phraseItem']['phrase'].' For '.$data->output['languageItem']['name'];
	$data->output['phraseForm']->build();
}

function theme_languagesAddPhraseSuccess($data){
	echo '<h2>The phrase "<i>',$data->output['phraseForm']->sendArray[':phrase'],'</i>" was successfully added.</h2>';
}

function theme_languagesEditPhraseSuccess($data){
	echo '<h2>The phrase was successfully updated</h2>';
}

function theme_languagesUpdate($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['responseMessage'],'</h2>';
	echo
	'
	<form name="updateLanguage" action="" method="post">
		<caption>Update From File System</caption><br />
		Language:
		<select name="language">';
	foreach($data->output['languageList'] as $languageShortName => $languageItem){
		echo
		'	<option value="',$languageShortName,'">',$languageItem['name'],'</option>';
	}
	echo '
		</select>
		Action:
		<select name="action">
			<option value="0">Clear Table And Start Fresh</option>
			<option value="1">Only Install Phrases That Don\'t Currently Exist</option>
			<option value="2">Update Existing Phrases And Install New Ones</option>
		</select>
		<input type="submit" name="install" value="Install" />
	</form>';
}

function theme_languagesUpdateSuccess($data){
	echo '<h2>The language ',$data->output['languageList'][$_POST['language']]['name'],' was successfully updated</h2>';
}