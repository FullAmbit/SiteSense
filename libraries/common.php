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
function killHacker($reason) { 
     echo ' 
          <h1>Aborting Execution</h1> 
          <p>Hacking attempt detected - ',$reason,'</p>'; 
     die; 
} 
function common_loadPlugin(&$data,$name)
{
	if(isset($data->plugins[$name])) {
		return true;
	}
	
	if(file_exists('plugins/'.$name.'/plugin.php'))	{
		common_include('plugins/'.$name.'/plugin.php');
		$objectName = 'plugin_'.$name;		
		$data->plugins[$name] = new $objectName;
		return true;
	} else {
		return false;
	}
}

function common_generateLink(&$data,$link,$text,$id = FALSE,$rel = FALSE,$class = NULL,$return = FALSE) {
	$data->output['links'][] = array(
		'link' => $link,
		'rel' => ($rel) ? $rel : $id,
		'id' => $id
	);
	
	if(!$return) {
		echo '<a href="',$link,'" rel="',$rel,'" id="',$id,'" class="',$class,'">',$text,'</a>';
	} else {
		return '<a href="'.$link.'" rel="'.$rel.'" id="'.$id.'" class="'.$class.'">'.$text.'</a>';
	}
	
}
function common_isValidEmail($address) {
	if (filter_var($address,FILTER_VALIDATE_EMAIL)==FALSE) {
		return false;
	}
	/* explode out local and domain */
	list($local,$domain)=explode('@',$address);
	$localLength=strlen($local);
	$domainLength=strlen($domain);
	return (
		/* check for proper lengths */
		($localLength>0 && $localLength<65) &&
		($domainLength>3 && $domainLength<256) &&
		(
			checkdnsrr($domain,'MX') ||
			checkdnsrr($domain,'A')
		)
	);
}
function common_redirect_local($data, $where) {
	common_redirect($data->linkHome . $where);
}
function common_redirect($where) {
	ob_end_clean();
	header('location: ' . $where);
	exit;
}
function common_camelBack($inString) {
	return lcfirst(str_replace(
		array(
			' ',"\n","\t","\r",'&nbsp;','_'
		),'',ucwords($inString)
	));
}
function common_randomPassword($min=8,$max=12) {
	$result='';
	if ($max<$min) $max=$min;
	$count=rand($min,$max);
	while ($count>0) {
		switch (rand(1,3)) {
			case 1:
				$result.=chr(rand(48,57));
			break;
			case 2:
				$result.=chr(rand(65,90));
			break;
			case 3:
				$result.=chr(rand(97,122));
			break;
		}
		$count--;
	}
	return $result;
}
function common_hasUrlPrefix($url) {
	$urlArray=Array(
		'http:',
		'https:',
		'ftp:'
	);
	foreach ($urlArray as $urlPrefix) {
		if (stripos($url,$urlPrefix)===0) return true;
	}
	return false;
}
function common_timedRedirect($URL, $seconds = 5) {
	echo _common_timedRedirect($URL);
}
function _common_timedRedirect($URL, $seconds = 5) {
	return '
		<p>Click <a href="'. $URL . '">here</a> if you are not redirected in ' . $seconds . ' seconds</p>
		<script type="text/javascript">
			window.setTimeout("window.location.href = \'' . $URL . '\';", ' . ($seconds * 1000) . ');
		</script>
	';
}
function common_parseDynamicValues(&$data, &$textToParse,$db = NULL) {
	$codeReplacements=array(
		'|linkRoot|' => $data->linkRoot,
		'|imageDir|' => $data->linkRoot.'images/',	
		'|smallStaticLinkRoot|' => $data->settings['cdnSmall'],
		'|largeStaticLinkRoot|' => $data->settings['cdnLarge'],
		'|flashLinkRoot|' => $data->settings['cdnFlash'],
		'|rssLink|' => (isset($data->output['rssLink'])) ? $data->output['rssLink'] : '',
		'|attribution|' => '<p id="attribution">Powered by <a href="http://www.sitesense.org">SiteSense</a>&trade; '.$data->settings['version'].', a <a href="http://www.fullambit.com">Full Ambit Media</a> product.</p>'
	);
	foreach ($codeReplacements as $key => $value) {
		$textToParse=str_replace($key,$value,$textToParse);
	}
	
	// Any Blocks?
	preg_match_all('/\|block:([_a-zA-Z0-9\s\-]+)\(?(.*?)\)?\|/',$textToParse,$matches,PREG_PATTERN_ORDER);
	//$textToParse = preg_replace('/\|loadBlock:([a-zA-Z0-9\s\-]+)\|/','',$textToParse);
	$blockList = $matches[1];
	ob_start();
	foreach($blockList as $key => $originalBlockName) {
		$blockInfo=explode('_',$originalBlockName);
		$target = 'modules/'.$blockInfo[0].'/blocks/'.$blockInfo[1].'.block.php';
		if(file_exists('modules/'.$blockInfo[0].'/blocks/'.$blockInfo[1].'.block.php')) {
			
			common_include('modules/'.$blockInfo[0].'/blocks/'.$blockInfo[1].'.block.php');
			
			$attributes = array(false);
			$attributesString = $matches[2][$key];
			$attributes = explode(',',$attributesString);
			
			$getUniqueSettings = $blockInfo[1].'_getUniqueSettings';
			$buildContent = $blockInfo[1].'_buildContent';
			$content = $blockInfo[1].'_content';
			
			if(function_exists($getUniqueSettings))	{
				$getUniqueSettings($data,$attributes);
			}
			
			if(function_exists($buildContent) && $db !== NULL) {
				$buildContent($data,$db,$attributes);
			}
			
			if(function_exists($content)) {
				$content($data,$attributes);
			}
		}
		
		$buffer = ob_get_contents();
		$textToParse = str_replace($matches[0][$key],$buffer,$textToParse);
	}	
	
	ob_end_clean();
	return $textToParse;
}

function common_generateShortName($string)
{
	$string = preg_replace("/[^a-z0-9\-\s]/",'',str_replace(' ','-',strtolower($string)));
	$string = str_replace("--","-",$string);
	$string = str_replace("--","-",$string	);
	
	return $string;
}

function common_include($includeName) {
	require_once($includeName);
}

function loadPermissions($data) {
    $data->permissions['core']=array(
        'access'        => 'Control panel access'
    );

    $data->permissions['dashboard']=array(
        'access'        => 'Dashboard access'
    );

    $data->permissions['mainMenu']=array(
        'access'        => 'Main menu access',
        'add'           => 'Add main menu items',
        'delete'        => 'Delete main menu items',
        'disable'       => 'Disable main menu items',
        'edit'          => 'Edit main menu items',
        'enable'        => 'Enable main menu items',
        'list'          => 'List main menu items'
    );

    $data->permissions['modules']=array(
        'access'        => 'Modules access',
        'disable'       => 'Disable modules',
        'edit'          => 'Edit modules',
        'enable'        => 'Enable modules',
        'list'          => 'List modules'
    );

    $data->permissions['plugins']=array(
        'access'        => 'Plugins access',
        'edit'          => 'Edit plugins',
        'disable'       => 'Disable plugins',
        'enable'        => 'Enable plugins',
        'list'          => 'List plugins'
    );

    $data->permissions['settings']=array(
        'access'        => 'Settings access'
    );

    $data->permissions['sidebars']=array(
        'access'        => 'Sidebar access',
        'add'           => 'Add sidebars',
        'delete'        => 'Delete sidebars',
        'edit'          => 'Edit sidebars',
        'list'          => 'List sidebars'
    );

    $data->permissions['dynamicURLs']=array(
        'access'        => 'URL Remap Access',
        'add'           => 'Add URL Remaps',
        'delete'        => 'Delete URL Remaps',
        'edit'          => 'Edit URL Remaps',
        'list'          => 'List URL Remaps'
    );
}

function getUserPermissions(&$db,&$user) {
    $user['permissions']=array();
    // Group Permissions
    // Purge expired Groups
    $db->query('purgeExpiredGroups');
    $statement=$db->prepare('getGroupsByUserID');
    $statement->execute(array(
        ':userID' => $user['id']
    ));
    $groups=$statement->fetchAll(PDO::FETCH_ASSOC); // Contains all groups a user is a member of
    foreach($groups as $group) {
        $statement=$db->prepare('getPermissionsByGroupName');
        $statement->execute(array(
            ':groupName' =>  $group['groupName']
        ));
        $permissions=$statement->fetchAll(PDO::FETCH_ASSOC); // Contains all permissions in each group
        foreach($permissions as $permission) {
           $user['permissions'][] = $permission['permissionName'];
        }
    }
    // User permissions
	// Finds out if user is Admin with universal access
    $statement=$db->prepare('isUserAdmin');
    $statement->execute(array(
        ':userID' => $user['id'],
    ));
    $userAdmin=$statement->fetchAll(PDO::FETCH_ASSOC); // Contains isAdmin results
	if(isset($userAdmin[0]))
		$user['isAdmin']=1;

    $statement=$db->prepare('getUserPermissionsByUserID');
    $statement->execute(array(
        ':userID' => $user['id']
    ));
    $permissions=$statement->fetchAll(PDO::FETCH_ASSOC); // Contains all user permissions
    foreach($permissions as $permission) {
        // Check to see if the user has already been granted the permission
        if(!in_array($permission['permissionName'],$user['permissions'])){ // User doesn't have permission
            // Allow/Forbid?
            if($permission['allow']==1) { // Allow; add permission
                $user['permissions'][] = $permission['permissionName'];
            }
        } else { // User has permission
            // Allow/Forbit?
            if($permission['allow']==0) { //Forbit; delete permission
                $key = array_search($permission['permissionName'],$user['permissions']);
                unset($user['permissions'][$key]);
            }
        }
    }
    if(isset($user['permissions'])) {
        // Organize array by module (Ex. $user['permissions']['blogs'])
        foreach($user['permissions'] as $key => $permission) {
            unset($user['permissions'][$key]);
            $separator = strpos($permission,'_');
            $prefix = substr($permission,0,$separator);
            $suffix = substr($permission,$separator+1);
            $user['permissions'][$prefix][] = $suffix;
        }

        // Clean up
        asort($user['permissions']);
    }
}

function checkPermission($permission,$module,$data) {
    $hasPermission = false;
	// User is Admin, which is universal access, Return true
    if(isset($data->user['isAdmin']) && $data->user['isAdmin']==1) {
        $hasPermission = true;
    } else {
        if(isset($data->user['permissions'][$module]) && in_array($permission,$data->user['permissions'][$module])) {
            $hasPermission = true;
        }
    }
    return $hasPermission;
}
function printFieldValue($fieldValue) {
	return isset($fieldValue) ? $fieldValue : '';
}
function hyphenToCamel($str,$ucfirst=false) {
    $parts=explode('-',$str);
    $parts=$parts ? array_map('ucfirst',$parts):array($str);
    $parts[0]=$ucfirst ? ucfirst($parts[0]):lcfirst($parts[0]);
    return implode('',$parts);
}
?>