<?php

function urlremap_config($data,$db) {
  if ($data->user['userLevel']>=USERLEVEL_ADMIN) {
    $data->admin['menu'][]=array(
      'category'  => 'CMS Settings',
      'command'   => 'urlremap',
      'name'      => 'URL Remaps',
      'sortOrder' => 1000
    );
  }
}

?>