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
function killHacker($reason){ 
     echo ' 
          <h1>Aborting Execution</h1> 
          <p>Hacking attempt detected - ',$reason,'</p>'; 
     die; 
} 
function common_loadPlugin($data,$name){
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
	header('Location: ' . $where);
	exit;
}
function common_randomPassword($min=8,$max=12) {
	$result='';
	if ($max<$min) $max=$min;
	$count=mt_rand($min,$max);
	while ($count>0) {
		switch (mt_rand(1,3)) {
			case 1:
				$result.=chr(mt_rand(48,57));
			break;
			case 2:
				$result.=chr(mt_rand(65,90));
			break;
			case 3:
				$result.=chr(mt_rand(97,122));
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
		'|attribution|' => '<p id="attribution">Powered by <a href="http://www.sitesense.org">SiteSense</a>&trade; '.$data->version.', a <a href="http://www.fullambit.com">Full Ambit Media</a> product.</p>'
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
function common_generateShortName($string,$functionSafe=FALSE){
	$string = preg_replace('/\-+/','-',
				preg_replace('/[^a-z0-9\-\s]/','',
				str_replace(' ','-',strtolower($string))));
	if ($functionSafe) { // functions can't have dashes in their names
		$string = str_replace('-','_',$string);
	}
	return $string;
}
function common_include($includeName) {
	require_once($includeName);
}
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
	return explode('_',$permission,2);
}
function checkPermission($permission,$module,$data) {
    return ((isset($data->user['isSuperAdmin'])&&$data->user['isSuperAdmin']===TRUE) || // User is Admin, which is universal access, Return true
		(isset($data->user['permissions'][$module][$permission])&&$data->user['permissions'][$module][$permission]=='1')); // User has explicit permissions for this module
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
		$qString .= $columnName." = :".$columnName."Val,";
	}
	$qString = trim($qString,",");
	// Build Condition Where Statement
	$conditionStatement = '';
	$max = count($conditions);
	$i = 0;
	foreach($conditions as $conditionColumn => $conditionValue){
		$vars[':'.$conditionColumn] = $conditionValue;
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
function common_timeDiff($now,$then,$format=DATE_RSS) {
	$diff=$now-$then;
	if($diff<60){
		return $diff.' seconds';
	}elseif($diff<60*60){
		return round($diff/60).' minutes';
	}elseif($diff<60*60*24){
		return round($diff/(60*60)).' hours';
	}elseif($diff<60*60*24*31){
		return round($diff/(60*60*24)). ' days';
	}elseif($diff<60*60*24*31*12){
		return round($diff/(60*60*24*31)).' months';
	}else{
		return date($format,$then);
	}
}
function common_loadPhrases($data,$db,$moduleShortName,$isAdmin = 0){	
	$statement = $db->prepare('getPhrasesByModule', 'common');
	$statement->execute(array(
		':module' => '',
		':isAdmin' => $isAdmin
	));
	while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$data->phrases['core'][$row['phrase']] = $row['text'];
	}
	if(!$moduleShortName) return;
	$statement->execute(array(
		':module' => $moduleShortName,
		':isAdmin' => $isAdmin
	));
	while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$data->phrases[$moduleShortName][$row['phrase']] = $row['text'];
	}
}
function common_sendMail($data,$db,$to,$subject,$content,$from=NULL,$headers='') {
	if ($from===NULL) {
		$from=$data->settings['siteTitle'].' <no-reply@'.$_SERVER['HTTP_HOST'].'>';
	}
	if (is_array($to)) {
		$to_new='';
		foreach ($to as $name=>$recipient) {
			if (!is_numeric($name)) {
				$to_new.=trim($name).' <'.trim($recipient).'>,';
			} else {
				$to_new.=trim($recipient).',';
			}
		}
		$to=trim($to_new,',');
	}
	$headers.='From: ' . $from . "\r\n";
	$headers.='Reply-to: ' . $from . "\r\n";
	$headers.='X-Mailer: SiteSense on ' . $data->settings['siteTitle'] . "\r\n";
	return mail(
		$to,
		$subject,
		wordwrap($content,70,"\n",FALSE),
		$headers
	);
}