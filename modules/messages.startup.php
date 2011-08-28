<?php

function messages_startup($data,$db) {
	$link=array(
		'text'        => 'Messages',
		'title'       => 'Personal Messages',
		'url' 				=> 'messages',
		'module'			=> 'messages'
	);
	
	if(!isset($data->user['id'])){
		$data->output['showMessageLink'] = false;	
	}else{
		$data->output['showMessageLink'] = true;
		$statement = $db->prepare('getUnreadCountByUser', 'messages');
		$statement->execute(array('user' => $data->user['id']));
		if(false !== ($num = $statement->fetchColumn()) && $num > 0){
			$link['dynamictext'] = 'Messages (' . $num . ')';
			$data->output['unreadMessageCount'] = $num;
		}else{
			$data->output['unreadMessageCount'] = 0;
		}
	}
	
	$data->menuSource[] = $link;
}

?>