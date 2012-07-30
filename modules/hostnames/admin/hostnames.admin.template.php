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

?>