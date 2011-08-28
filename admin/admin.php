<?php

require_once('admin/admin.common.php');

function page_buildContent($data,$db) {
	$db->loadModuleQueries('admin',true);
	//Preload default values into $data->output:
	$defaults = array(
		'pagesError' => false,
		'abort' => false,
		'abortMessage' => 'abort',
		'blogsStart' => false
	);
	$data->output = array_merge($defaults, $data->output);
  if ($data->user['userLevel']>=USERLEVEL_MODERATOR) {
    if (empty($data->action[1])) {
      common_include('admin/about.admin.php');
    } else {
      $target='admin/'.$data->action[1].'.admin.php';
      if (file_exists($target)) {
        common_include($target);
				$db->loadModuleQueries('admin_'.$data->action[1]);
      } else {
        common_include('admin/404.static.php');
      }
    }
    $files=glob('admin/*.config.php');
    foreach ($files as $fileName) {
      common_include($fileName);
      $targetName=substr(strrchr(str_replace('.config.php','',$fileName),'/'),1);
      $targetFunction=$targetName.'_config';
      if (function_exists($targetFunction)) {
        $targetFunction($data,$db);
      }
    }
    usort($data->admin['menu'],'admin_menuCmp');
    if (function_exists('admin_buildContent')) {
      admin_buildContent($data,$db);
    }
  }
}

function page_content($data) {
  if ($data->user['userLevel']<USERLEVEL_MODERATOR) {
    if ($data->user['userLevel']>USERLEVEL_GUEST) {
      echo '
      <h2>Access Denied</h2>
      <p>You do not have sufficient user rights to access the administration panel.</p>';
    } else {
      echo '
      <h2>Access Denied</h2>
      <p>You must be logged in to access the administration panel.</p>';
      theme_loginForm($data);
    }
  } else {
    if (function_exists('admin_content')) {
      admin_content($data);
    } else {
      echo '
      <h2>Fatal Error</h2>
      <p>The requested adming.php module is not installed.</p>';
    }
  }
}

?>