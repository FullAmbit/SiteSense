<?php
function admin_buildContent($data,$db) {

	switch ($data->action[2]) {
		case 'switch':
			if (is_numeric($data->action[3])) {
				$statement=$db->prepare('getSideById','admin_mainMenu');
				$statement->execute(array(
					':id' => $data->action[3]
				));
				if ($item=$statement->fetch()) {
					$statement=$db->prepare('updateSideById','admin_mainMenu');
					$statement->execute(array(
						':side' => ( $item['side']=='left' ? 'right' : 'left' ),
						':id' => $item['id']
					));
				}
			}
		break;
		case 'moveUp':
		case 'moveDown':
			if (is_numeric($data->action[3])) {
				$statement=$db->prepare('getSortOrderById','admin_mainMenu');
				$statement->execute(array(
					':id' => $data->action[3]
				));
				if ($item=$statement->fetch()) {
					if ($data->action[2]=='moveUp') {
						$item['sortOrder']-=3;
					} else {
						$item['sortOrder']+=3;
					}
					$statement=$db->prepare('updateOrderById','admin_mainMenu');
					$statement->execute(array(
						':sortOrder' => $item['sortOrder'],
						':id' => $item['id']
					));
				}
			}
		break;
	}

	admin_mainMenuRebuild($data,$db);
	$statement=$db->query('getMenuItemsOrdered','admin_mainMenu');
	$data->output['menuList']=$statement->fetchAll();
}

function admin_content($data) {
	echo '
		<table class="pagesList">
			<caption>Main Menu Order</caption>
			<tr>
				<th class="text">Text</th>
				<th class="title">Title</th>
				<th class="module">Module</th>
				<th class="module">Side</th>
				<th class="buttonList">Controls</th>
			</tr>';
	$count=0;
	foreach ($data->output['menuList'] as $item) {
		echo '<tr class="',(
			$count%2==0 ? 'even' : 'odd'
		),'">
				<td class="text">',$item['text'],'</td>
				<td class="title">',$item['title'],'</td>
				<td class="module">',$item['module'],'</td>
				<td class="module">',$item['side'],'</td>
				<td class="buttonList">
					<a href="',$data->linkRoot,'admin/mainMenu/switch/',$item['id'],'"
						title="Switch side of menu this item is shown on"
					>Switch Side</a>
					<a href="',$data->linkRoot,'admin/mainMenu/moveUp/',$item['id'],'" title="Move Up">&uArr;</a>
					<a href="',$data->linkRoot,'admin/mainMenu/moveDown/',$item['id'],'" title="Move Down">&dArr;</a>
				</td>
			</tr>';
		$count++;
	}
	echo '
		</table>';

}
?>