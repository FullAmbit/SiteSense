<?php

function admin_pagesBuild($data,$db) {
	if (
		(
			($data->action[3]=='moveUp') ||
			($data->action[3]=='moveDown')
		) && is_numeric($data->action[4])
	) {
		$qHandle=$db->prepare('getPageOrderById','admin_pages');
		$qHandle->execute(array(
			':id' => $data->action[4]
		));
		if ($item=$qHandle->fetch()) {
			$moveAmount=($item['parent']<1) ? 5 : 3;
			if ($data->action[3]=='moveUp') $moveAmount*=-1;
			$qHandle=$db->prepare('updatePageSortOrderById','admin_pages');
			$qHandle->execute(array(
				':sortOrder' => $item['sortOrder']+$moveAmount,
				':id' => $item['id']
			));
			admin_pagesResort($db);
		}
	}
	$data->output['pagesList'] = admin_List($db);
}
function admin_List($db, $Parent = 0, $Level = 0){ // Using a function is necessary here for recursion
	$list = array();
	$statement = $db->prepare('getPageListByParent', 'admin_pages');
	$statement->execute(array(':parent' => $Parent));
	while($item = $statement->fetch()){
		$item['level'] = $Level;
		$list[] = $item;
		$list = array_merge($list, admin_List($db, $item['id'], $Level + 1));
	}
	return $list;
}
function admin_pagesShowLine($data, $item) {
	echo '
			<tr class="',($data->output['count']%2==0 ? 'odd' : 'even'),'">
				<td class="shortName">
					',$data->output['pagesNamePrefix'],'<a href="',$data->linkRoot,'admin/pages/edit/',$item['id'],'">',$item['shortName'],'</a>
				</td>
				<td class="buttonList">
					<a href="',$data->linkRoot,'admin/pages/list/moveUp/',$item['id'],'" title="Move Up">&uArr;</a>
					<a href="',$data->linkRoot,'admin/pages/list/moveDown/',$item['id'],'" title="Move Down">&dArr;</a>
					<a href="',$data->linkRoot,'admin/pages/edit/new/childOf/',$item['id'],'">Add Child</a>
					<a href="',$data->linkRoot,'admin/pages/delete/',$item['id'],'">Delete</a>
				</td>
			</tr>';
	$data->output['count']++;
}

function admin_pagesShow($data) {
	echo '
			<div class="panel buttonList">
				<a href="',$data->linkRoot,'admin/pages/edit/new">
					Add New Page
				</a>
			</div>';

	if (empty($data->output['pagesList'])) {
		echo '
			<p class="pageListNoPages">No pages exist</p>';
	} else {
		echo '
			<table class="pagesList">
				<thead>
					<tr>
						<th class="shortName">Name</th>
						<th class="controls">Controls</th>
					</tr>
				</thead><tbody>';
		echo '<tr class="section"><th colspan="2">None</th></tr>';
		$data->output['count']=0;
		$lastParent=-999;
		$data->output['pagesNamePrefix']='';
		foreach ($data->output['pagesList'] as $item) {
			$data->output['pagesNamePrefix']=str_repeat('&nbsp;', $item['level'] * 4);
			admin_pagesShowLine($data,$item);
			$data->output['pagesNamePrefix']='';
		}
	echo '
				</tbody>
			</table>';
	}

	echo '
			<div class="panel buttonList">
				<a href="',$data->linkRoot,'admin/pages/edit/new">
					Add New Page
				</a>
			</div>';
}

?>