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
function common_loadPlugin($data,$name)
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

function common_generateLink($data,$link,$text,$id = FALSE,$rel = FALSE,$class = NULL,$return = FALSE) {
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
function common_parseDynamicValues($data,&$textToParse,$db = NULL) {
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
	
	// Parsing $data->action?
	preg_match_all('/\|action:([0-9]+)\|/',$textToParse,$matches,PREG_PATTERN_ORDER);
	$actionList = $matches[0];
	foreach($actionList as $key => $actionText){
		$textToParse=str_replace($actionText,$data->action[$matches[1][$key]],$textToParse);
	}
		
	// Any Blocks?
	preg_match_all('/\|block:([_a-zA-Z0-9\s\-]+)\(?(.*?)\)?\|/',$textToParse,$matches,PREG_PATTERN_ORDER);
	//$textToParse = preg_replace('/\|loadBlock:([a-zA-Z0-9\s\-]+)\|/','',$textToParse);
	$blockList = $matches[1];
    foreach($blockList as $key => $originalBlockName) {
    	ob_start();
		$blockInfo=explode('_',$originalBlockName);
		$target = 'modules/'.$blockInfo[0].'/blocks/'.$blockInfo[0].'.block.'.$blockInfo[1].'.php';
        if(file_exists($target)) {
			common_include($target);
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
		ob_end_clean();
	}
	return $textToParse;
}

function common_parseTime($UTCTime,$offset,$includeZone = TRUE,$format = "M d Y G:i:s"){
	$unixTime = (intval($UTCTime) > 100000) ? $UTCTime : strtotime($UTCTime);
	$newTime = $unixTime + $offset;
	if($includeZone){
		$zone = $offset/3600;
		$format .= (($zone) < 0) ? ' \G\M\T'.$zone : ' \G\M\T+'.$zone;
	}
	return date($format,$newTime);
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

    $data->permissions['urls']=array(
        'access'        => 'URL Remap Access',
        'add'           => 'Add URL Remaps',
        'delete'        => 'Delete URL Remaps',
        'edit'          => 'Edit URL Remaps',
        'list'          => 'List URL Remaps'
    );
}

/**function permissionQuery(&$db,&$user,$queryName,$queryData) {
	$statement=$db->prepare($queryName);
	$statement->execute($queryData);
	while ($result=$statement->fetch(PDO::FETCH_ASSOC)
		$user['permissions'][$result['name']]|=$result['value'];
	}
}

function getUserPermissions(&$db,&$user) {
	if (isset($user['permissions'])) return false; // already set
	
	$user['permissions']=array();
	
	$db->query('purgeExpiredGroups');
	
	$groupsStatement=$db->prepare('getGroupsByUserID');
	$groupsStatement->execute(array(
		':userID' => $user['id']
	));
	while ($group=$groupsStatement->fetch(PDO::FETCH_ASSOC)) {
		permissionQuery($db,$user,'getPermissionsByGroupName',array(
			':name' => $group['name']
		);
	}
	
	permissionQuery($db,$user,'getUserPermissionsByUserID',array(
		':userID' => $user['id']
	);
}**/


function getUserPermissions($db,&$user){
	if(isset($user['permissions'])) return false;
	
	// Finds out if user is SuperAdmin with universal access
	$superAdmins = array(1);
	$user['isSuperAdmin'] = FALSE;
	if(in_array($user['id'],$superAdmins)){
		$user['isSuperAdmin']=TRUE;
		return;
	}
	
	$user['permissions']=array();
	
	$db->query('purgeExpiredGroups');
	
	
	// Permissions Per Group
	$statement = $db->prepare('getGroupsByUserID');
	$statement->execute(array(
		':userID' => $user['id']
	));
	while($group = $statement->fetch(PDO::FETCH_ASSOC)){
		$statement=$db->prepare('getPermissionsByGroupName');
        $statement->execute(array(
            ':groupName' =>  $group['groupName']
        ));
        $permissionList=$statement->fetchAll(PDO::FETCH_ASSOC); // Contains all permissions in each group
        foreach($permissionList as $permissionItem) {
        	// Parse Perission Name
        	list($prefix,$suffix) = parsePermissionname($permissionItem['permissionName']);
        	$user['permissions'][$prefix][$suffix] = $permissionItem['value'];
        }
	}
	
	// Permissions Per User
    $statement=$db->prepare('getUserPermissionsByUserID');
    $statement->execute(array(
        ':userID' => $user['id']
    ));
    $permissionList=$statement->fetchAll(PDO::FETCH_ASSOC); // Contains all user permissions
    foreach($permissionList as $permissionItem){
		list($prefix,$suffix) = parsePermissionName($permissionItem['permissionName']);
		if(!isset($user['permissions'][$prefix][$suffix])){
			$user['permissions'][$prefix][$suffix] = $permissionItem['value'];
		}elseif($user['permissions'][$prefix][$suffix] == '-1'){
			// Forbid Takes Priority Over Everything
			continue;
		}elseif($user['permissions'][$prefix][$suffix] == '0'){
			// If Existing Permission Is Neutral..Override
			$user['permissions'][$prefix][$suffix] = $permissionItem['value'];
		}elseif($user['permissions'][$prefix][$suffix] == '1' && $permissionItem['value'] !== '0'){
			// If Existing Permission Is Allow...Only Override If The New One Is Not A Neutral
			$user['permissions'][$prefix][$suffix] = $permissionItem['value'];
		}		
	}
}

function parsePermissionName($permission){
    $separator = strpos($permission,'_');
    $prefix = substr($permission,0,$separator);
    $suffix = substr($permission,$separator+1);
    
    return array($prefix,$suffix);
}

function checkPermission($permission,$module,$data) {
    $hasPermission = false;
	// User is Admin, which is universal access, Return true
    if(isset($data->user['isSuperAdmin']) && $data->user['isSuperAdmin']===TRUE) {
        $hasPermission = true;
    } else {
        if(isset($data->user['permissions'][$module][$permission]) && $data->user['permissions'][$module][$permission] == '1') {
            $hasPermission = true;
        }
    }
    return $hasPermission;
}

function common_populateLanguageTables($data,$db,$tableName,$keyColumn,$keyValue,$includeCurrentLanguage = FALSE){
	foreach($data->languageList as $languageItem){
		if($includeCurrentLanguage==FALSE && ($languageItem['shortName']==$data->language)) continue;

		$statement=$db->prepare('populateLanguageTable','common',array(
			'!languageTable!' => $tableName.'_'.$languageItem['shortName'],
			'!sourceTable!' => $tableName.'_'.$data->language,
			'!keyColumn!' => $keyColumn
		));
		$statement->execute(array(
			':keyValue' => $keyValue
		));
		if(!$statement){
			return $statement;
		}
	}
}

function common_deleteFromLanguageTables($data,$db,$tableName,$keyColumn,$keyValue,$includeCurrentLanguage = FALSE){
	foreach($data->languageList as $languageItem){
		if($includeCurrentLanguage==FALSE && ($languageItem['shortName']==$data->language)) continue;
		$statement=$db->prepare('deleteFromLanguageTable','common',array(
			'!table!' => $tableName.'_'.$languageItem['shortName'],
			'!keyColumn!' => $keyColumn
		));
		$statement->execute(array(
			':keyValue' => $keyValue
		));
	}
}

function common_updateAcrossLanguageTables($data,$db,$tableName,$conditions,$values,$includeCurrentLanguage = FALSE){
	$qString = '';
	foreach($values as $columnName => $columnValue){
		$vars[':'.$columnName."Val"] = $columnValue;
		
		// Query String
		$qString .= $columnName." = :".$columnName."Val,";
	}
	$qString = trim($qString,",");
	
	// Build Condition Where Statement
	$conditionStatement = '';
	$max = count($conditions);
	$i = 0;
	foreach($conditions as $conditionColumn => $conditionValue){
		$vars[':'.$conditionColumn] = $conditionValue;
		
		// Query String
		$conditionStatement .= $conditionColumn." = :".$conditionColumn.((($i+1) >= $max) ? "" : " AND ");
		$i++;
	}
	
	foreach($data->languageList as $languageItem){
		if($includeCurrentLanguage==FALSE && ($languageItem['shortName']==$data->language)) continue;
		$statement = $db->prepare("updateLanguageTable",'common',array(
			'!table!' => $tableName.'_'.$languageItem['shortName'],
			'!qString!' => $qString,
			'!conditionStatement!' => $conditionStatement
		));
		$statement->execute($vars);
	}
}

function common_checkUniqueValueAcrossLanguages($data,$db,$tableName,$columnSelector,$conditions,$includeCurrentLanguage = TRUE){
	$qString = '';
	$max = count($conditions);
	$i = 0;
	foreach($conditions as $conditionColumn => $conditionValue){
		$vars[':'.$conditionColumn] = $conditionValue;
		
		// Query String
		$qString .= $conditionColumn." = :".$conditionColumn.((($i+1) >= $max) ? "" : " AND ");
		$i++;
	}
	foreach($data->languageList as $languageItem){
		if($includeCurrentLanguage==FALSE && ($languageItem['shortName']==$data->language)) continue;
		
		$statement = $db->prepare("selectFromLanguageTable","common",array(
			'!table!' => $tableName.'_'.$languageItem['shortName'],
			'!column!' => $columnSelector,
			'!qString!' => $qString
		));
		$statement->execute($vars);
		if($statement->fetch()!==FALSE){
			return TRUE;
		}
	}
	return FALSE;
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

function common_formatDatabaseTime($time=NULL,$format="Y-m-d H:i:s") {
	$time = ($time == NULL) ? time() : $time;
	return gmdate($format,$time);
}
function common_timeDiff($start,$end) {
		$diff=$end-$start;
		$hrs=0;
		$mins=0;
		$secs=0;
		if($diff%86400<=0) $days=$diff/86400;
		if($diff%86400>0) {
			$rest=($diff%86400);
			$days=($diff-$rest)/86400;
     	if($rest%3600>0) {
				$rest1=($rest%3600);
				$hrs=($rest-$rest1)/3600;
        if($rest1%60>0) {
					$rest2=($rest1%60);
          $mins=($rest1-$rest2)/60;
          $secs=$rest2;
        } else $mins=$rest1/60;
     	} else $hrs=$rest/3600;
		}
		if($days==1) $days=$days.' Day';
		elseif($days>1) $days=$days.' Days';
    else $days=false;
    if($hrs==1) $hrs=$hrs.' Hour';
		elseif($hrs>1) $hrs=$hrs.' Hours';
    else $hrs=false;
		if($mins==1) $mins=$mins.' Minute';
		elseif($mins>1) $mins=$mins.' Minutes';
    else $mins=false;
		if($secs>1) $secs=$secs.' Seconds';
    else $secs='1 Second!';
    if($days) return $days;
    elseif($hrs) return $hrs;
    elseif($mins) return $mins;
		else return $secs;
}

function common_loadPhrases($data,$db,$moduleShortName,$isAdmin = 0){	
	$statement = $db->prepare('getPhrasesByModule', 'common');
	// Core Phrases
	$statement->execute(array(
		':module' => '',
		':isAdmin' => $isAdmin
	));
	while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$data->phrases['core'][$row['phrase']] = $row['text'];
	}
	// Module-Specific Phrases
	$statement->execute(array(
		':module' => $moduleShortName,
		':isAdmin' => $isAdmin
	));
	while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$data->phrases[$moduleShortName][$row['phrase']] = $row['text'];
	}

}
?>