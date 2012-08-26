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
function pages_getUniqueSettings($data,$db) {

}

function pages_buildContent($data,$db) {
	if($data->banned) {
		$statement = $db->prepare('getPagesByShortName','pages');
		$statement->execute(array(
			':shortName' => 'banned'
		));
		
		$data->output['pageContent'] = $statement->fetch();
		return;
	}
	$statement = $db->prepare('getPageByShortNameAndParent', 'pages');
	$current = array('id' => 0); //pseudo-page, root node.
	$stages = array_filter(array_slice($data->action, 1));
	$found = (count($stages) > 0);
	foreach($stages as $stage){
		if($stage !== false){
			$statement->execute(array('parent' => $current['id'], 'shortName' => $stage));
			if(($result = $statement->fetch()) !== false){
				$current = $result;
			}else{
				$found = false;
				break;
			}
		}
	}
	$data->output['found'] = $found;
	$data->output['404']=false;
	if($found){
		$statement = $db->prepare('getPagesByParent', 'pages');
		$statement->execute(array('parent' => $current['id']));
		$data->output['pageContent'] = $current;
		$data->output['pageShortName']= $current['title'];
		$data->output['pageContent']['children']=$statement->fetchAll();
		$data->output['pageTitle']=$data->output['pageContent']['title'];
        $data->output['pageContent']['parsedContent'] = htmlspecialchars_decode($data->output['pageContent']['parsedContent']);
        common_parseDynamicValues($data,$data->output['pageContent']['parsedContent'],$db);
	} else if ($data->httpHeaders[0] === 'Content-Type: text/html; charset='.$data->settings['characterEncoding']) {
		$data->httpHeaders[]='HTTP/1.1 404 Not Found';
		$data->output['404']=true;
		$data->output['pageContent']['title']='HTTP/1.1 404 Not Found';
	}
	$statement = $db->prepare('getEnabledSidebarsByPage','pages');
	$statement->execute(array(':pageId' => $current['id']));
	$sidebars = $statement->fetchAll();
	$data->sidebarList = array();
	$data->usedSidebars = array();
	foreach ($sidebars as $sidebar) {
		if (!in_array($sidebar['id'],$usedSidebars)) {
			common_parseDynamicValues($this, $sidebar['titleUrl'], $db);
			common_parseDynamicValues($this, $sidebar['parsedContent'], $db);
			$data->usedSidebars[] = $sidebar['id'];
			$data->sidebarList[strtolower($sidebar['side'])][]=$sidebar;
		}
	}
}
function pages_content($data) {
	if ($data->output['404']) {
		theme_contentBoxHeader('HTTP/1.1 404 Not Found');
		echo '
			<p>
				You attempted to access "', implode('/', array_filter($data->action)), '" which does not exist on this server. Please check the URL and try again. If you feel this is in error, please contact the site administrator.
			</p>';
		theme_contentBoxFooter();
		if(checkPermission('canEnableModules','core',$data) && $data->module !== false && $data->module['enabled'] == 0){
			theme_contentBoxHeader('Admin Options');
			echo '
				<p>
					This module exists, but is currently disabled (modules require enabling before use).<br />
					To enable this module, do so <a href="', $data->linkRoot, 'admin/modules/edit/', $data->module['id'], '">on this page in the admin panel</a>
				</p>';
			theme_contentBoxFooter();
		}
	} else {
		theme_contentBoxHeader($data->output['pageContent']['title']);
		
		echo $data->output['pageContent']['parsedContent'];
		theme_contentBoxFooter();
		/*if (!empty($data->output['pageContent']['children'])) {
			foreach ($data->output['pageContent']['children'] as $item) {
				common_parseDynamicValues($data,$item['content']);
				theme_contentBoxHeader($item['title']);
				echo $item['content'];
				theme_contentBoxFooter();
			}
		}*/
	}
}
?>