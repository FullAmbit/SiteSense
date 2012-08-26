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
function admin_sidebarsBuild($data, $db) {
	if (!checkPermission('list', 'sidebars', $data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>'.$data->phrases['sidebars']['insufficientUserPermissions'].'</h2>'.$data->phrases['sidebars']['insufficientUserPermissions2'];
		return;
	}
	if (is_numeric($data->action[4])) {
		if (($data->action[3]=='moveUp') ||($data->action[3]=='moveDown')){
			admin_sortOrder_move($data,$db,'sidebars',$data->action[3],$data->action[4],'sortOrder',NULL,TRUE);
		} elseif ($data->action[3] == 'switch') {
			if (is_numeric($data->action[4])) {
				$statement=$db->prepare('getById', 'admin_sidebars');
				$statement->execute(array(
						':id' => $data->action[4]
				));
				if ($item=$statement->fetch()) {
					$side = $item['side']=='left' ? 'right' : 'left';
					$statement=$db->prepare('updateSideById', 'admin_sidebars');
					$statement->execute(array(
							':side' => $side,
							':id' => $item['id']
					));
					//--Push Changes To Other Languages
					common_updateAcrossLanguageTables($data,$db,'sidebars',array('id'=>$item['id']),array('side' => $side));
				}
			}
		}
	}
	$statement=$db->prepare('getAllOrdered', 'admin_sidebars');
	$statement->execute();
	$data->output['sidebars']=$statement->fetchAll();
}
function admin_sidebarsShow($data) {
	$aRoot=$data->linkRoot.'admin/sidebars/';
	theme_sidebarsListAddNewButton($data,$aRoot);
	if (empty($data->output['sidebars'])) {
		theme_sidebarsListNoSidebars($data);
	} else {
		theme_sidebarsListTableHead($data);
		$count=0;
		foreach ($data->output['sidebars'] as $item) {
			$titleStartTag='';
			$titleEndTag='';
			if ($item['enabled']) {
				$titleStartTag.='<b>';
				$titleEndTag='</b>'.$titleEndTag;
			}
			if ($item['fromFile']) {
				$titleStartTag.='/sidebars/';
				$titleEndTag='.sidebar.php'.$titleEndTag;
			} else {
				$titleStartTag.='<a href="'.$aRoot.'edit/'.$item['id'].'">';
				$titleEndTag='</a>'.$titleEndTag;
			}
			$data->output['item'] = $item;
			$data->output['aRoot'] = $aRoot;
			$data->output['titleStartTag'] = $titleStartTag;
			$data->output['titleEndTag'] = $titleEndTag;
			$data->output['count'] = $count;
			theme_sidebarsListTableRow($data);
			$count++;
		}
		theme_sidebarsListTableFoot();
	}
	theme_sidebarsListAddNewButton($data,$aRoot);
}
?>
