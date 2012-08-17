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

function users_validateDynamicFormField($data,$db,$field,$fieldValue){	
	$fieldRef =& $data->output['customForm']->fields[$field['id']];
	$formError =& $data->output['customForm']->error;
				
	$camelCaseName = common_camelBack($field['name']);
	switch($camelCaseName){
	}
}

function users_validateusername($data,$db,$field,$fieldValue){
	$fieldRef =& $data->output['customForm']->fields[$field['id']];
	$formError =& $data->output['customForm']->error;
	
	// Check If UserName Exists...
	if($data->getUserIdByName($fieldValue)) {
		$formError = true;
    	$fieldRef['error']=true;
    	$fieldRef['errorList'][]=$data->phrases['users']['nameAlreadyExists'];
    }
}

// Save The UserName To The Database
function users_saveusername($data,$db,$field,$fieldName,$fieldValue){
	// Initial Save...Create User Row
	$statement = $db->prepare('createUserRow','users');
	$r = $statement->execute(array(
		':name' => $fieldValue
	));
	// Get User Id Now.
	$statement=$db->prepare('checkUserName','users');
	$statement->execute(array(
		':name' => $fieldValue
	));
	list($data->user['id']) = $statement->fetch();
	$data->user['name'] = $fieldValue;
	return $r;
}

// Save Password To Database
function users_savepassword($data,$db,$field,$fieldName,$fieldValue){
	$fieldValue=hash('sha256',$fieldValue);
	$statement = $db->prepare('updateUserField','users',array('!column1!' => 'password'));
	$r=$statement->execute(array(
		':name' => $data->user['name'],
		':fieldValue' => $fieldValue
	));
}

// Add Seperate Field Data (Catch-All Function)
function users_saveDynamicFormField($data,$db,$field,$fieldName,$fieldValue){	
	$userColumns = array(
		'username',
		'password',
		'firstName',
		'lastName',
		'contactEMail',
		'publicEMail',
		'timeZone'
	);
	if(in_array($fieldName,$userColumns)){
		// In Users Table
		$statement = $db->prepare('updateUserField','users',array('!column1!' => $fieldName));
		$r=$statement->execute(array(
			':name' => $data->user['name'],
			':fieldValue' => $fieldValue
		));
	}else{
		// Not Part of Users Table
		$statement = $db->prepare('addDynamicUserField','users');
		$statement->execute(array(
			':userId' => $data->user['id'],
			':name' => $fieldName,
			':value' => $fieldValue
		));
	}
}

function users_afterForm($data,$db){
	// Get Inserted User Item
	$statement=$db->prepare('getByName','users');
	$statement->execute(array(
		':name' => $data->user['name']
	));
	$userItem = $statement->fetch(PDO::FETCH_ASSOC);
	// Do We Require E-Mail Verification??
	if($data->settings['verifyEmail'] == 1) {
        $hash=md5(common_randomPassword(32,32));
		$statement=$db->prepare('insertActivationHash','users');
		$statement->execute(array(
		    ':userId' => $userItem['id'],
		    ':hash' => $hash,
		    ':expires' => date('Y-m-d H:i:s',(time()+(14*24*360)))
		));
		sendActivationEMail($data,$db,$userItem['id'],$hash,$userItem['contactEMail']);
	}
	
	// Insert into group
	if($data->settings['defaultGroup']!==0) {
		$statement=$db->prepare('addUserToPermissionGroupNoExpires');
		$statement->execute(array(
			':userID'          => $userItem['id'],
			':groupName'       => $data->settings['defaultGroup']
		));
	}
	
	// Update Registered IP, Registered Date And Last Access
	$statement=$db->prepare('updateIPDateAndAccess','users');
	$r = $statement->execute(array(
		':userID' => $userItem['id'],
		':registeredIP' => $_SERVER['REMOTE_ADDR'],
		':registeredDate' => common_formatDatabaseTime(),
		':lastAccess' => common_formatDatabaseTime()
	));
}

// Send Activation EMail
function sendActivationEMail($data,$db,$userId,$hash,$sendToEmail) {
    $statement=$db->prepare('getRegistrationEMail','users');
    $statement->execute();
    if ($mailBody=$statement->fetchColumn()) {
        $mailBody = htmlspecialchars_decode($mailBody);
        $activationLink='http://'.$_SERVER['SERVER_NAME'].$data->linkRoot.'users/register/activate/'.$userId.'/'.$hash;
        $mailBody=str_replace(
            array(
                '$siteName',
                '$registerLink'
            ),
            array(
                $data->settings['siteTitle'],
                '<a href="'.$activationLink.'">'.$activationLink.'</a>'
            ),
            $mailBody
        );
        $subject=$data->settings['siteTitle'].' Activation Link';
        $header='From: Account Activation - '.$data->settings['siteTitle'].'<'.$data->settings['register']['sender'].">\r\n".
            'Reply-To: '.$data->settings['register']['sender']."\r\n".
            'X-Mailer: PHP/'.phpversion()."\r\n".
            'Content-Type: text/html';
        $content='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html><head>
<title>'.$data->phrases['users']['activateAccountPageTitle'].'</title>
</head><body>
'.$mailBody.'
</body></html>';
        $data->output['messages'][]='
	  	<p>
	  		'.$data->phrases['users']['activationLink1'].$sendToEmail.$data->phrases['users']['activationLink1'].'
	  	</p>
	  ';
        if (mail(
            $sendToEmail,
            $subject,
            $content,
            $header
        )) {
            return true;
        } else die('A Fatal error occurred in the mail subsystem');
    } else {
        $data->output['messages'][]='
			<p>
				'.$data->phrases['users']['activationEmailDeleted'].'
			</p>
		';
    }
    return false;
}
?>