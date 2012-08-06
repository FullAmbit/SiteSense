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
function admin_buildCSS($data) {
}
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
function admin_mainMenuInColumn($module,$testArray) {
	foreach ($testArray as $item) {
		if ($item['module']==$module) return true;
	}
	return false;
}
function admin_mainMenuRebuild($data,$db) {
	/* first pull up the current menu entries */
	$statement=$db->query('getMainMenu','admin');
	$list=$statement->fetchAll();
	$pages = array();
	foreach($list as $item){
		if($item['module'] == 'pages'){
			$pages[$item['url']] = $item;
		}
	}
	/*
		then we need to add any that are in our
		constructed list that aren't in the database
	*/
	foreach(glob('modules/*/*.startup.php') as $path){
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
		if (!admin_mainMenuInColumn($item['module'],$list)) {
			$statement->execute(array(
				':text' => $item['text'],
				':title' => $item['title'],
				':url' => $item['url'],
				':module' => $item['module'],
				':enabled' => 0
			));
		}
	}
	$sortstatement = $db->prepare('insertMenuItemWithSortAndSide', 'admin');
	$deletePages = $db->query('deletePageMenuItems', 'admin');
	$pageQuery = $db->query('getMenuPages', 'pages');
	while($item = $pageQuery->fetch()){
		if(isset($pages[$item['shortName']])){
			$sortstatement->execute(array(
				'text' => $item['title'],
				'title' => $item['title'],
				'url' => $item['shortName'],
				'module' => 'pages',
				'enabled' => $pages[$item['shortName']]['enabled'],
				'sortOrder' => $pages[$item['shortName']]['sortOrder'],
				'side' => $pages[$item['shortName']]['side']
			));
		}else{
			$statement->execute(array(
				'text' => $item['title'],
				'title' => $item['title'],
				'url' => $item['shortName'],
				'module' => 'pages',
				'enabled' => 1
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
		if (!admin_mainMenuInColumn($item['module'],$data->menuSource)) {
			$statement->execute(array(
				':id' => $item['id']
			));
		}
	}
	/* now we update their sortOrder values, which means pulling the list AGAIN */
	$statement=$db->query('getMenuItemsOrdered','admin');
	$list=$statement->fetchAll();
	$rowCount = count($list);
	$statement=$db->prepare('updateMenuSortOrder','admin');
	foreach ($list as $item) {
		$count += 1;
		if ($item['sortOrder']!=$count) {
			$statement->execute(array(
				':sortOrder' => $count,
				':id' => $item['id']
			));
		}
	}
}

function admin_sortOrder_new($data,$db,$table,$sortOrderName='sortOrder',$parentName=NULL,$parent=NULL,$language = FALSE) {
	// Are We Updating A Language Table?
	if($language){
		$baseTableName = $table;
		$table=$table.'_'.$data->language;
	}
	
	if(isset($parentName) && isset($parent)) {
		// Get Highest Sort Order (Parent)
		$statement = $db->prepare('getHighestSortOrderParent','admin',array(
			'!table!' => $table,
			'!column1!' => $sortOrderName,
			'!column2!' => $parentName
		));
		$statement->execute(array(
			':parent'        => $parent
		));
	} else {
		// Get Highest Sort Order (No Parent)
        $statement = $db->query('getHighestSortOrder','admin',array(
        	'!table!' => $table,
			'!column1!' => $sortOrderName
        ));
	}
    $result=$statement->fetch();
	$sortOrder=$result['sortOrder']+1;
	return $sortOrder;
}

function admin_sortOrder_move($data,$db,$table,$direction='down',$id,$sortOrderName='sortOrder',$parentName=NULL,$language = FALSE) {
	// Are We Updating A Language Table?
	if($language){
		$baseTableName = $table;
		$table=$table.'_'.$data->language;
	}
	// Get Current Sort Order by ID
    if(isset($parentName)) {
        $statement=$db->prepare('getSortOrderByID','admin',array(
        	'!table!' => $table,
        	'!column1!' => $sortOrderName,
        	'!column2!' => $parentName
        ));

        $statement->execute(array(
        	':id' => $id
        ));
        
        $result=$statement->fetch();
        $parent=$result['parent'];
    } else {
        $statement=$db->prepare('getSortOrderByIDNoParent','admin',array(
        	'!table!' => $table,
        	'!column1!' => $sortOrderName
        ));
        $statement->execute(array(
                ':id' => $id
        ));
        $result=$statement->fetch();
    }
    $sortOrder=$result['sortOrder'];
    $error=false;
	if($direction=='up' || $direction=='moveUp') {
		// Find the next smallest sort order within the parent
        if(isset($parentName)) {
            $statement=$db->prepare('getNextSmallestSortOrder','admin',array(
            	'!table!' => $table,
            	'!column1!' => $sortOrderName,
            	'!column2!' => $parentName
            ));
            $statement->execute(array(
                ':parent'    => $parent,
                ':sortOrder' => $sortOrder
            ));
        } else {
            $statement=$db->prepare('getNextSmallestSortOrderNoParent','admin',array(
	        	'!table!' => $table,
	        	'!column1!' => $sortOrderName
        	));
            $statement->execute(array(
                ':sortOrder' => $sortOrder
            ));
        }
        $result=$statement->fetch();
        if ($result===FALSE) {
			// Already the smallest
			$error=true;
		}
		$swapSortOrder=$result['sortOrder'];
	} elseif($direction=='down' || $direction=='moveDown') {
		// Find the next largest sort order within the parent
        if(isset($parentName)) {
            $statement=$db->prepare('getNextHighestSortOrder','admin',array(
            	'!table!' => $table,
            	'!column1!' => $sortOrderName,
            	'!column2!' => $parentName
            ));
            $statement->execute(array(
                ':parent'    => $parent,
                ':sortOrder' => $sortOrder
            ));
        } else {
            $statement=$db->prepare('getNextHighestSortOrderNoParent','admin',array(
	        	'!table!' => $table,
	        	'!column1!' => $sortOrderName
        	));
            $statement->execute(array(
                 ':sortOrder' => $sortOrder
            ));
        }
        $result=$statement->fetch();
        if ($result===FALSE) {
			// Already the largest
			$error=true;
		}
		$swapSortOrder=$result['sortOrder'];
	}
	
	if(!$error) {
		// Updating sortOrder effects two items as it is a swap
        if(isset($parentName)) {
            $statement=$db->prepare('updateSortOrderByParent','admin',array(
            	'!table!' => $table,
            	'!column1!' => $sortOrderName,
            	'!column2!' => $parentName
            ));
            $statement->execute(array(
                ':sortOrder_new' => $sortOrder,
                ':sortOrder'     => $swapSortOrder,
                ':parent'        => $parent
            ));
            //---Update Across Other Languages
            if($language){
            	$conditions = array(
            		$sortOrderName => $swapSortOrder,
	            	$parentName => $parent
            	);
            	$values = array(
	            	$sortOrderName =>  $sortOrder
            	);
	            common_updateAcrossLanguageTables($data,$db,$baseTableName,$conditions,$values);
	        }
        } else {
            $statement=$db->prepare('updateSortOrderNoParent','admin',array(
	        	'!table!' => $table,
	        	'!column1!' => $sortOrderName
        	));
            $statement->execute(array(
                    ':sortOrder_new' => $sortOrder,
                    ':sortOrder'     => $swapSortOrder
            ));
            
            //---Update Across Other Languages
            if($language){
            	$conditions = array(
            		$sortOrderName => $swapSortOrder,
            	);
            	$values = array(
	            	$sortOrderName =>  $sortOrder
            	);
	            common_updateAcrossLanguageTables($data,$db,$baseTableName,$conditions,$values);
	        }
        }
		$statement=$db->prepare('updateSortOrderByID','admin',array(
        	'!table!' => $table,
        	'!column1!' => $sortOrderName
    	));
		$statement->execute(array(
			':sortOrder' => $swapSortOrder,
			':id'        => $id
		));
		
		//---Update Across Other Languages
        if($language){
        	$conditions = array(
        		'id' => $id
        	);
        	$values = array(
            	$sortOrderName =>  $swapSortOrder
        	);
            common_updateAcrossLanguageTables($data,$db,$baseTableName,$conditions,$values);
        }
	}
}
?>