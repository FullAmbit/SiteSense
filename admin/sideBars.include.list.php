<?php

function admin_sideBarsBuild($data,$db) {
	if (is_numeric($data->action[4])) {
		if (
			($data->action[3]=='moveUp') ||
			($data->action[3]=='moveDown')
		) {
			$qHandle=$db->prepare('getSortOrderById','admin_sideBars');
			$qHandle->execute(array(
				':id' => $data->action[4]
			));
			if ($item=$qHandle->fetch()) {
				$moveAmount=($data->action[3]=='moveUp') ? -3 : 3;
				$qHandle=$db->prepare('updateSortOrderById','admin_sideBars');
				$qHandle->execute(array(
					':sortOrder' => $item['sortOrder']+$moveAmount,
					':id' => $item['id']
				));
				admin_sideBarsResort($db);
			}
		} else if (
			($data->action[3]=='enable') ||
			($data->action[3]=='disable')
		) {
			$qHandle=$db->prepare('updateEnabledById','admin_sideBars');
			$qHandle->execute(array(
				':enabled' => ($data->action[3]=='enable'),
				':id' => $data->action[4]
			));
		}
	}
	$qHandle=$db->prepare('getAllOrdered','admin_sideBars');
	$qHandle->execute();
	$data->output['sideBars']=$qHandle->fetchAll();
}

function admin_sideBarsShow($data) {
	$aRoot=$data->linkRoot.'admin/sideBars/';
	echo '
			<div class="panel buttonList">
				<a href="',$aRoot,'edit/new">
					Add New Sidebar
				</a>
			</div>';

	if (empty($data->output['sideBars'])) {
		echo '
			<p class="pageListNoPages">No Sidebars exist</p>';
	} else {
		echo '
			<table class="pagesList">
				<thead>
					<tr>
						<th class="shortName">Sidebar Title</th>
						<th class="controls">Controls</th>
					</tr>
				</thead><tbody>';
		$count=0;
		foreach ($data->output['sideBars'] as $item) {
			$titleStartTag='';
			$titleEndTag='';

			if ($item['enabled']) {
				$titleStartTag.='<b>';
				$titleEndTag='</b>'.$titleEndTag;
			}
			if ($item['fromFile']) {
				$titleStartTag.='/sideBars/';
				$titleEndTag='.sideBar.php'.$titleEndTag;
			} else {
				$titleStartTag.='<a href="'.$aRoot.'edit/'.$item['id'].'">';
				$titleEndTag='</a>'.$titleEndTag;
			}

			echo '
					<tr class="',($count%2==0 ? 'odd' : 'even'),'">
						<td class="shortName">
							',$titleStartTag,$item['name'],$titleEndTag,'
						</td>
						<td class="buttonList">
							',(
								$item['fromFile'] ?
								'' :
								'<a href="'.$aRoot.'delete/'.$item['id'].'">Delete</a> '
							),(
								$item['enabled'] ?
								'<a href="'.$aRoot.'list/disable/'.$item['id'].'" class="enabled">Enabled</a>' :
								'<a href="'.$aRoot.'list/enable/'.$item['id'].'" class="disabled">Disabled</a>'
							),'
							<a href="',$aRoot,'list/moveUp/',$item['id'],'" title="Move Up">&uArr;</a>
							<a href="',$aRoot,'list/moveDown/',$item['id'],'" title="Move Down">&dArr;</a>
						</td>
					</tr>';

			$count++;
		}
	echo '
				</tbody>
			</table>';
	}

		echo '
			<div class="panel buttonList">
				<a href="',$aRoot,'edit/new">
					Add New Sidebar
				</a>
			</div>';

}

?>