<?php

function theme_hostnamesList($data){
	echo 
	'
	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/hostnames/add">Add A Hostname</a>
	</div>
	<table width="100%">
		<caption>',$data->phrases['hostnames']['manageHostnamesHeading'],'</caption>
		<thead>
			<tr>
				<th>',$data->phrases['hostnames']['hostname'],'</th>
				<th>',$data->phrases['core']['controls'],'</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['hostnameList'] as $hostnameItem){
		echo 
		'	<tr>
				<td>',$hostnameItem['hostname'],'</td>
				<td>
				<a href="',$data->linkRoot,'admin/hostnames/edit/',$hostnameItem['hostname'],'">',$data->phrases['core']['actionEdit'],'</a>
				<a href="',$data->linkRoot,'admin/hostnames/delete/',$hostnameItem['hostname'],'">',$data->phrases['core']['actionDelete'],'</a>
				</td>
			</tr>';
	}
	echo
		'</tbody>
	</table>';
}

function theme_hostnamesAdd($data){
	if(isset($data->output['responseMessage'])) echo $data->output['responseMessage'];
	$data->output['hostnameForm']->build();
}

function theme_hostnamesAddSuccess($data){
	echo $data->phrases['hostnames']['addHostnameSuccessMessage'],' - ',$data->output['hostnameForm']->sendArray[':hostname'];
}

function theme_hostnamesEdit($data){
	if(isset($data->output['responseMessage'])) echo $data->output['responseMessage'];
	$data->output['hostnameForm']->caption = $data->phrases['hostnames']['captionEditHostname'];
	$data->output['hostnameForm']->build();
}

function theme_hostnamesEditSuccess($data){
	echo $data->phrases['hostnames']['editHostnameSuccessMessage'];
}

function theme_hostnamesNotFound($data){
	echo $data->phrases['hostnames']['hostnameNotFound'];
}

function theme_hostnamesDelete($data){
	if(isset($data->output['responseMessage'])) echo $data->output['responseMessage'];
	echo '
	<form name="delHostname" action="" method="post">
		',$data->phrases['hostnames']['deleteHostnameConfirmHeading'],'<br /><b>',$data->phrases['hostnames']['warning'],'</b>&nbsp;',$data->phrases['hostnames']['deleteHostnameConfirmMessage'],'<br />
		<input type="submit" name="yes" value="',$data->phrases['core']['actionConfirmDelete'],'" />
		<input type="submit" name="no" value="',$data->phrases['core']['actionCancelDelete'],'" />
	</form>';
}

function theme_hostnamesDeleteSuccess($data){
	echo $data->phrases['hostnames']['deleteHostnameSuccessMessage'];
}
?>