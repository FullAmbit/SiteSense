<?php

function theme_languagesList($data){
	echo 
	'<table width="100%">
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
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['phraseList'] as $phraseItem){
		echo 
		'	<tr>
				<td>',$languageItem['phrase'],'</td>
				<td>',$languageItem['text'],'</td>
				<td><a href="',$data->linkRoot,'admin/languages/editPhrase/',$phraseItem['phrase'],'">Modify</a></td>
			</tr>';
	}
	echo
	'	</tbody>
	</table>';
}

function theme_languagesNotFound($data){
	echo '<h2>The language you specified could not be found</h2>';
}

function theme_languagesAddPhrase($data){
	if(isset($data->output['responseMessage'])) echo '<h2>',$data->output['resposneMessage'],'</h2>';
	$data->output['phraseForm']->caption = 'Add A Phrase For '.$data->output['languageItem']['name'];
	$data->output['phraseForm']->build();
}