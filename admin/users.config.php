<?php

function users_config($data,$db) {
  if ($data->user['userLevel']>=USERLEVEL_ADMIN) {
    $data->admin['menu'][]=array(
      'category'  => 'User Management',
      'command'   => 'users/list',
      'name'      => 'User List',
      'sortOrder' => 3
    );
    $data->admin['menu'][]=array(
      'category'  => 'User Management',
      'command'   => 'users/list/staff',
      'name'      => 'Staff Members',
      'sortOrder' => 4
    );
    $data->admin['menu'][]=array(
      'category'  => 'User Management',
      'command'   => 'users/search',
      'name'      => 'Search Members',
      'sortOrder' => 5
    );
    $data->admin['menu'][]=array(
      'category'  => 'User Management',
      'command'   => 'users/edit/new',
      'name'      => 'Add New User',
      'sortOrder' => 6
    );
    $data->admin['menu'][]=array(
      'category'  => 'User Management',
      'command'   => 'users/activation',
      'name'      => 'Awaiting Activation',
      'sortOrder' => 7
    );
    $data->admin['menu'][]=array(
      'category'  => 'User Management',
      'command'   => 'users/policy',
      'name'      => 'Activation Policy',
      'sortOrder' => 8
    );
  }
}

?>