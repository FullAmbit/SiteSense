<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
function admin_pagesBuild($data,$db) {
	//permission check for pages access
	if(!checkPermission('access','pages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '
			<h2>Insufficient User Permissions</h2>
			You do not have the permissions to access this area.';	
		return;
	}

	if ((($data->action[3]=='moveUp') || ($data->action[3]=='moveDown')) && is_numeric($data->action[4]))
	{
		$qHandle=$db->prepare('getPageById','admin_pages');
		$qHandle->execute(array(
			':id' => $data->action[4]
		));
		if($item = $qHandle->fetch())
		{
			$statement = $db->prepare('countPagesByParent','admin_pages');
			$statement->execute(array(':parent' => $item['parent']));
			list($rowCount) = $statement->fetch();
			
			if($data->action[3] == 'moveUp' && intval($item['sortOrder']) > 1) {
				$query1 = 'shiftOrderUpRelative';
				$query2 = 'shiftOrderUpByID';
			} else if($data->action[3] == 'moveDown' && intval($item['sortOrder']) < $rowCount) {
				$query1 = 'shiftOrderDownRelative';
				$query2 = 'shiftOrderDownByID';
			}
			if(isset($query1))
			{
				$statement = $db->prepare($query1,'admin_pages');
				$statement->execute(array(
					':sortOrder' => $item['sortOrder'],
					':parent' => $item['parent']
				));
				$statement = $db->prepare($query2,'admin_pages');
				$statement->execute(array(
					':id' => $item['id']
				));
			}
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
function admin_pagesShow($data) {
	theme_pagesListHead($data);
	if (empty($data->output['pagesList'])) {
		theme_pagesListNoPages();
	} else {
		theme_pagesListTableHead();
		$lastParent=-999;
		foreach ($data->output['pagesList'] as $item) {
			theme_pagesListTableRow($data,$item);
		}
		theme_pagesListTableFoot();
	}
	theme_pagesListFoot($data);
}
?>
