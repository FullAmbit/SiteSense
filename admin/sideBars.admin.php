<?php

function admin_sideBarsResort($db) {
	$statement=$db->prepare('getSorted','admin_sideBars');
	$statement->execute();
	$list=$statement->fetchAll();
	$statement=$db->prepare('updateSortOrderById','admin_sideBars');
	$count=1;
	foreach ($list as $item) {
		if ($item['sortOrder']!=$count) {
			$statement->execute(array(
				':sortOrder' => $count,
				':id' => $item['id']
			));
		}
		$count+=2;
	}
}

function admin_buildContent($data,$db) {

	/* first add any that are in the directory but not in the database */
	$files=glob('sideBars/*.sideBar.php');
	$statement=$db->prepare('getSideBarNameByName','admin_sideBars');
	$wHandle=$db->prepare('insertSideBarSort','admin_sideBars');
	foreach ($files as $fileName) {
		$targetName=substr(strrchr(str_replace('.sideBar.php','',$fileName),'/'),1);
		$statement->execute(array(
			':name' => $targetName
		));
		if (!$statement->fetch()) {
			$wHandle->execute(array(
				':name' => $targetName
			));
		}
	}
	/* now even tougher, remove any that are NOT listed */
	$statement=$db->query('getFromFiles','admin_sideBars');
	$wHandle=$db->prepare('deleteById','admin_sideBars');
	$data->output['sideBars']=array();
	while ($item=$statement->fetch()) {
		$testName='sideBars/'.$item['name'].'.sideBar.php';
		if (!in_array($testName,$files)) {
			$wHandle->execute(array(
				':id' => $item['id']
			));
		}
	}

	$statement=$db->query('getAllOrdered','admin_sideBars');
	$data->output['sideBars']=$statement->fetchAll();

	if (empty($data->action[2])) {
		$data->action[2]='list';
	}
	$target='admin/sideBars.include.'.$data->action[2].'.php';
	if (file_exists($target)) {
		common_include($target);
		$data->output['function']=$data->action[2];
	}
	if (function_exists('admin_sideBarsBuild')) admin_sideBarsBuild($data,$db);
	$data->output['pageTitle']='SideBars';
}

function admin_content($data) {
	if ($data->output['abort']) {
		echo $data->output['abortMessage'];
	} else {
		if (!empty($data->output['function'])) {
			admin_sideBarsShow($data);
		} else admin_unknown();
	}
}
?>