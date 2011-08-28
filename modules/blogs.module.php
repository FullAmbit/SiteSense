<?php
common_include('libraries/forms.php');
function page_getUniqueSettings($data) {
	$data->output['pageShortName']='blogs';
}

function page_buildContent($data,$db) {
	require_once('modules/blogs.common.php');
	$data->output['summarize'] = false;
	$data->output['notFound'] = false;
	if (!is_numeric($data->action[1])){
		$statement=$db->prepare('getBlogByName','blogs');
		$statement->execute(array(
			':shortName' => $data->action[1]
		));
		$parentBlog=$statement->fetch();
		$data->output['blogInfo'] = $parentBlog;
		$data->action[1]=$parentBlog['id'];
	}else{
		$statement=$db->prepare('getBlogById','blogs');
		$statement->bindValue(':blogId',$data->action[1]);
		$statement->execute();
		$data->output['blogInfo']=$statement->fetch();
	}
	if($data->output['blogInfo'] === false){
		$data->output['notFound'] = true;
		return; 
	}
	if ($data->action[2] === false) $data->action[2]=0;
	if (is_numeric($data->action[2])) {
		$data->output['blogInfo']['startPage']=$data->action[2];
		$data->output['summarize'] = true;
		blog_buildContent($data,$db);
		foreach($data->output['newsList'] as &$item){
			$statement=$db->prepare('countCommentsByPost','blogcomments');
			$statement->execute(array('post' => $item['id']));
			$item['commentCount'] = $statement->fetchColumn();
		}
	} else {
		$statement=$db->prepare('getBlogPostsByIDandName','blogs');
		$statement->execute(array(
			':blogId' => $data->action[1],
			':shortName' => $data->action[2]
		));
		$data->output['newsList'] = $statement->fetchAll();
		if(empty($data->output['newsList'])){
			$data->output['notFound'] = true;
			return;
		}
		$data->output['commentForm'] = new formHandler('blogcomment',$data);
		$data->output['commentForm']->fields['post']['value'] = $data->output['newsList'][0]['id'];
		if (($data->output['blogInfo']['commentsRequireLogin'] == 0 || isset($data->user['id'])) && isset($_POST['fromForm']) && ($_POST['fromForm']==$data->output['commentForm']->fromForm)){
			$data->output['commentForm']->populateFromPostData();
			if ($data->output['commentForm']->validateFromPost()) {
				$statement=$db->prepare('makeComment','blogcomments');
				$statement->execute($data->output['commentForm']->sendArray);
				unset($data->output['commentForm']);
				$data->output['commentSuccess'] = true;
			}
		}
		foreach($data->output['newsList'] as &$item){
			$statement=$db->prepare('getCommentsByPost','blogcomments');
			$statement->execute(array('post' => $item['id']));
			$item['comments'] = $statement->fetchAll();
			$item['commentCount'] = count($item['comments']);
		}
	}
}

function page_content($data) {
	$pagination=is_numeric($data->action[2]);
	blog_pageContent($data,$pagination,$pagination, $data->output['summarize']);
}

?>