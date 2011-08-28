<?php

function about_config($data,$db) {
  $data->admin['menu'][]=array(
    'category'  => 'CMS Settings',
    'command'   => 'about',
    'name'      => 'About This CMS',
    'sortOrder' => 0
  );
}

?>