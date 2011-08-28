<?php
function admin_unknown() {
	echo '
	  <h2>Unknown command</h2>
	  <p>
	    You have called a function that is either not yet implemented or is incorrect. Please check the URL and try again. If you feel this is in error, contact your system administrator.
	  </p>';
}

function admin_menuCmp($a,$b) {
  if (
    ($a['category']=='CMS Settings') &&
    ($b['category']!='CMS Settings')
  ) {
    return -1;
  }
  $result=strcmp($a['category'],$b['category']);
  if ($result==0) {
    $result=(
      $a['sortOrder']<$b['sortOrder'] ?
      1 :
      (
        $a['sortOrder']==$b['sortOrder'] ? 0 : -1
      )
    );
    $result=strcmp($a['sortOrder'],$b['sortOrder']);
    if ($result==0) {
      $result=strcmp($a['name'],$b['name']);
    }
  }
  return $result;
}

function admin_mainMenuInColumn($value,$testArray) {
	//reset($testArray); - removed by-reference so unnecessary  
	foreach ($testArray as $item) {
		if ($item['text']==$value) return true;
	}
	return false;
}

function admin_mainMenuRebuild($data,$db) {
	/* first pull up the current menu entries */
	$statement=$db->query('getMainMenu','admin');
	$list=$statement->fetchAll();
	/*
		then we need to add any that are in our
		constructed list that aren't in the database
	*/

	foreach(glob('modules/*.startup.php') as $path){
		$dirend = strrpos($path, '/') + 1;
		$nameend = strpos($path, '.');
		$name = substr($path, $dirend, $nameend - $dirend);
		$function = $name . '_startup';
		if(!function_exists($function)){
			common_include($path);
			if(function_exists($function)){
				$function($data, $db);
			}
		}
	}
	
	$statement = $db->prepare('insertMenuItem', 'admin');
	foreach ($data->menuSource as $item) {
		if (!admin_mainMenuInColumn($item['text'],$list)) {
			$statement->execute(array(
				':text' => $item['text'],
				':title' => $item['title'],
				':url' => $item['url'],
				':module' => $item['module']
			));
		}
	}

	/*
		then reverse it, delete any that are in the database but not
		our list. This might seem convoluted, but the only other approach
		would be to check if each one exists in turn which would be a lot
		more queries.
	*/

	$statement=$db->prepare('deleteMenuItemById','admin');
	reset($data->menuSource);
	foreach ($list as $item) {
		if (!admin_mainMenuInColumn($item['text'],$data->menuSource)) {
			$statement->execute(array(
				':id' => $item['id']
			));
		}
	}

	/* now we update their sortOrder values, which means pulling the list AGAIN */
	$statement=$db->query('getMenuItemsOrdered','admin');
	$count=1;
	$list=$statement->fetchAll();
	$statement=$db->prepare('updateMenuSortOrder','admin');
	foreach ($list as $item) {
		$count+=2;
		if ($item['sortOrder']!=$count) {
			$statement->execute(array(
				':sortOrder' => $count,
				':id' => $item['id']
			));
		}
	}
}
?>