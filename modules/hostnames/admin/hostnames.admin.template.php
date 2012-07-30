<?php

function theme_hostnamesList($data){
	echo 
	'
	<div class="navPanel buttonList">
		<a href="',$data->linkRoot,'admin/hostnames/add">Add A Hostname</a>
	</div>
	<table width="100%">
		<caption>Hostnames</caption>
		<thead>
			<tr>
				<th>Hostname</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>';
	foreach($data->output['hostnameList'] as $hostnameItem){
		echo 
		'	<tr>
				<td>',$hostnameItem['hostname'],'</td>
				<td>
				<a href="',$data->linkRoot,'admin/hostnames/edit/',$hostnameItem['hostname'],'">Edit</a>
				<a href="',$data->linkRoot,'admin/hostnames/delete/',$hostnameItem['hostname'],'">Delete</a>
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
	echo 'The hostname: ',$data->output['hostnameForm']->sendArray[':hostname'],' was added to the database.';
}

function theme_hostnamesEdit($data){
	if(isset($data->output['responseMessage'])) echo $data->output['responseMessage'];
	$data->output['hostnameForm']->caption = 'Edit Hostname';
	$data->output['hostnameForm']->build();
}

function theme_hostnamesEditSuccess($data){
	echo 'The changes you made were successfully saved.';
}

function theme_hostnamesNotFound($data){
	echo "The hostname you specific could not be found.";
}

function theme_hostnamesDelete($data){
	if(isset($data->output['responseMessage'])) echo $data->output['responseMessage'];
	echo '
	<form name="delHostname" action="" method="post">
		Are you sure you want to delete this hostname?<br /><b>Warning:</b> This will also delete all associated settings and URL remaps.
		<input type="submit" name="yes" value="Yes" />
		<input type="submit" name="no" value="No" />
	</form>';
}

function theme_hostnamesDeleteSuccess($data){
	echo "The hostname and all associated URL remaps have been deleted.";
}
?>