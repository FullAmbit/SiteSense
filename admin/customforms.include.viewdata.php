<?php
function admin_customformsBuild($data,$db){
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No Form ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFormById', 'customform');
	$statement->execute(array(':id' => $data->action[3]));
	$form = $statement->fetch();
	if($form === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Form Doesn\'t Exist</h2>';
		return;
	}
	$data->output['form'] = $form;
	$statement = $db->prepare('getFieldsByForm', 'customform');
	$statement->execute(array(':form' => $form['id']));
	$data->output['fields'] = $statement->fetchAll();
	$statement = $db->prepare('getRowsByForm', 'customform');
	$statement->execute(array(':form' => $form['id']));
	$data->output['rows'] = $statement->fetchAll();
	$statement = $db->prepare('getValuesByForm', 'customform');
	$statement->execute(array(':form' => $form['id']));
	$results = $statement->fetchAll();
	$values = array();
	foreach($results as $value){
		if(!isset($values[$value['row']])){
			$values[$value['row']] = array();
		}
		$values[$value['row']][$value['field']] = $value['value']; 
	}
	$data->output['values'] = $values;
	
}
function admin_customformsShow($data){
	echo '
		<table>
			<tr>
		';
	foreach($data->output['fields'] as $field){
		echo '
				<th>', $field['name'], '</th>
		';
	}
	foreach($data->output['rows'] as $row){
		echo '
			<tr>
		';
		foreach($data->output['fields'] as $field){
			if(isset($data->output['values'][$row['id']][$field['id']])){
				$value = $data->output['values'][$row['id']][$field['id']];
			}else{
				$value = "-unset-";
			}
			echo '
				<td>', $value, '</td>
			';
		}
	}
	echo '
		</table>
	';
}