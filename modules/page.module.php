<?php
function page_getUniqueSettings($data,$db) {
	$statement = $db->prepare('getPageByShortNameAndParent', 'page');
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
	if($found){
		$statement = $db->prepare('getPagesByParent', 'page');
		$statement->execute(array('parent' => $current['id']));
		$data->output['pageContent'] = $current;
		$data->output['pageShortName']= $current['title'];
		$data->output['pageContent']['children']=$statement->fetchAll();
	} else {
		$data->httpHeaders[]='HTTP/1.1 404 Not Found';
		$data->output['pageShortName']='404';
		$data->output['pageContent']['title']='HTTP/1.1 404 Not Found';
	}
}


function page_content($data) {
	if ($data->output['pageShortName']=='404') {
		theme_contentBoxHeader('HTTP/1.1 404 Not Found');
		echo '
			<p>
				You attempted to access "', implode('/', array_filter($data->action)), '" which does not exist on this server. Please check the URL and try again. If you feel this is in error, please contact the site administrator.
			</p>';
		theme_contentBoxFooter();
		if($data->user['userLevel'] >= USERLEVEL_ADMIN && $data->module !== false && $data->module['enabled'] == 0){
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
		echo $data->output['pageContent']['content'];
		theme_contentBoxFooter();
		if (!empty($data->output['pageContent']['children'])) {
			foreach ($data->output['pageContent']['children'] as $item) {
				common_parseDynamicValues($data,$item['content']);
				theme_contentBoxHeader($item['title']);
				echo $item['content'];
				theme_contentBoxFooter();
			}
		}
	}
}

?>