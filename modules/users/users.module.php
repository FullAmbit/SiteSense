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
common_include('libraries/forms.php');
function populateTimeZones($data) {
    $currentTime=time();
    $times=array();
    $start=$currentTime-date('G',$currentTime)*3600;
    for($i=0;$i<24*60;$i+=15) {
        $times[date('g:i A',$start+$i*60)]=array();
    }
    $timezones=DateTimeZone::listIdentifiers();
    foreach($timezones AS $timezone) {
        $dt=new DateTime('@'.$currentTime);
        $dt->setTimeZone(new DateTimeZone($timezone));
        $time=$dt->format('g:i A');
        $times[$time][]=$timezone;
    }
    $timeZones=array_filter($times);
    foreach($timeZones as $time => $timeZoneList) {
        foreach($timeZoneList as $timeZone) {
            $data->output['timeZones'][]=array(
                'text'  => $time.' - '.$timeZone,
                'value' => $timeZone
            );
        }
    }
}
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
<title>',$data->phrases['users']['activateAccountPageTitle'],'</title>
</head><body>
'.$mailBody.'
</body></html>';
        $data->output['messages'][]='
	  	<p>
	  		'.$data->phrases['users']['activationLink1'].$sendToEmail.$data->phrases['users']['activationLink2'].'
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
function checkUserName($name,$db) {
	$statement=$db->prepare('checkUserName','users');
	$statement->execute(array(':name' => $name));
	return $statement->fetchColumn();
}
function users_getUniqueSettings($data){
	$data->output['pageShortName']='SiteSense';
}
function users_buildContent($data,$db) {
    populateTimeZones($data);
    switch($data->action[1]){
		case 'edit':
			// Check If Logged In
			if(!isset($data->user['id'])){
				common_redirect_local($data, 'users/login/');
			}
            $data->output['userForm'] = new formHandler('edit', $data);
            if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['userForm']->fromForm)){
                $data->output['userForm']->populateFromPostData();
                if ($data->output['userForm']->validateFromPost()) {
                    unset($data->output['userForm']->sendArray[':password2']);
                    if ($data->output['userForm']->sendArray[':password']=='') {
                        $statement=$db->prepare('updateUserByIdNoPw','users');
                        unset($data->output['userForm']->sendArray[':password']);
                        $data->output['userForm']->sendArray[':id']=$data->user['id'];
                    } else {
                        $data->output['userForm']->sendArray[':password']=hash('sha256',$data->output['userForm']->sendArray[':password']);
                        $statement=$db->prepare('updateUserById','users');
                        $data->output['userForm']->sendArray[':id']=$data->user['id'];
                    }
                    $statement->execute($data->output['userForm']->sendArray);
                    if (empty($data->output['secondSidebar'])) {
                        $data->output['savedOkMessage']='
						<h2>'.$data->phrases['users']['userDetailsSaved'].'</h2>
						<p>'.$data->phrases['users']['beRedirectedShortly'].'</p>
					' . _common_timedRedirect($data->linkRoot . 'users/');
                    }
                } else {
                    /*
                  invalid data, so we want to show the form again
                 */
                    $data->output['secondSidebar']='
					<h2>'.$data->phrases['users']['errorInData'].'</h2>
					<p>
						'.$data->phrases['users']['validationError'].'
					</p>';
                    if ($data->output['userForm']->sendArray[':password'] != $data->output['userForm']->sendArray[':password2']) {
                        $data->output['secondSidebar'].='
					<p>
						<strong>'.$data->phrases['users']['passwordMismatch'].'</strong>
					</p>';
                        $data->output['userForm']->fields['password']['error']=true;
                        $data->output['userForm']->fields['password2']['error']=true;
                    }
                }
            } else {
                $data->output['userForm']->caption=$data->phrases['users']['editingUserDetails'];
                $statement=$db->prepare('getById','users');
                $statement->execute(array(
                    ':id' => $data->user['id']
                ));
                if (false !== ($item = $statement->fetch())) {
                    foreach ($data->output['userForm']->fields as $key => $value) {
                        if (empty($value['params']['type'])){
                            $value['params']['type'] = '';
                            switch ($value['params']['type']) {
                                case 'checkbox':
                                    $data->output['userForm']->fields[$key]['checked']=(
                                    $item[$key] ? 'checked' : ''
                                    );
                                    break;
                                case 'password':
                                    /* NEVER SEND PASSWORD TO A FORM!!! */
                                    break;
                                default:
                                    $data->output['userForm']->fields[$key]['value']=$item[$key];
                            }
                        }
                    }
                }
            }
		break;
		case 'register':
            if(isset($data->user['id'])) {
                common_redirect_local($data,'default');
            }
            require_once('libraries/forms.php');
            $data->output['registerForm']=new formHandler('register',$data);
            $data->output['showForm']=true;
            $data->output['messages']=array();
            if(isset($_POST['fromForm'])&&($_POST['fromForm']==$data->output['registerForm']->fromForm)) {
                $data->output['registerForm']->populateFromPostData();
                if($data->output['registerForm']->validateFromPost()) {
                    if($data->getUserIdByName($data->output['registerForm']->sendArray[':name'])) {
                        $data->output['registerForm']->fields['name']['error']='true';
                        $data->output['registerForm']->fields['name']['errorList'][]=$data->phrases['users']['nameExists'];
                    } else {
                        unset($data->output['registerForm']->sendArray[':password2']);
                        unset($data->output['registerForm']->sendArray[':verifyEMail']);
                        $data->output['registerForm']->sendArray[':registeredDate']=$data->output['registerForm']->sendArray[':lastAccess']=common_formatDatabaseTime();
                        $data->output['registerForm']->sendArray[':registeredIP']=$_SERVER['REMOTE_ADDR'];
                        $data->output['registerForm']->sendArray[':publicEMail']='';
                        $data->output['registerForm']->sendArray[':emailVerified']=($data->settings['verifyEmail'] == 1) ? 0 : 1;
                        $data->output['registerForm']->sendArray[':password']=hash(
                            'sha256',
                            $data->output['registerForm']->sendArray[':password']
                        );
                        $statement=$db->prepare('insertUser','users');
                        $statement->execute($data->output['registerForm']->sendArray) or die('Saving user failed');
                        $userId = $db->lastInsertId();
                        $hash=md5(common_randomPassword(32,32));
                        // Insert into group
                        if($data->settings['defaultGroup']!==0) {
							$statement=$db->prepare('addUserToPermissionGroupNoExpires');
							$statement->execute(array(
								':userID'          => $userId,
								':groupName'       => $data->settings['defaultGroup']
							));
                        }
                        // Do We Require E-Mail Verification??
                        if($data->settings['verifyEmail'] == 1) {
                            $statement=$db->prepare('insertActivationHash','users');
                            $statement->execute(array(
                                ':userId' => $userId,
                                ':hash' => $hash,
                                ':expires' => date('Y-m-d H:i:s',(time()+(14*24*360)))
                            ));
                            sendActivationEMail($data,$db,$userId,$hash,$data->output['registerForm']->sendArray[':contactEMail']);
                        } else if($data->settings['requireActivation'] == 0) {
                            $data->output['messages'][]='
                                            <p>
                                                '.$data->phrases['users']['accountRegistered'].'
                                                <a href="'.$data->linkRoot.'login">'.$data->phrases['users']['clickLogin'].'</a>
                                            </p>';
                        } else if($data->settings['requireActivation'] == 1) {
                            $data->output['messages'][]='
                                            <p>
                                                '.$data->phrases['users']['awaitingApproval'].'
                                            </p>';
                        }
                        $data->output['showForm']=false;
                    }
                }
            } else {
                switch ($data->action[2]) {
                    case 'activate':
                        $data->output['showForm']=false;
                        $userId=$data->action[3];
                        $hash=$data->action[4];
                        /*
                            We have to use a var for time so as to not have accounts
                            'slip through the cracks' waiting for the queries to execute
                        */
                        $expireTime=time();
                        $statement=$db->prepare('getExpiredActivations','users');
                        $statement->execute(array(':expireTime' => $expireTime));
                        $delStatement=$db->prepare('deleteUserById','users');
                        while($user=$statement->fetch()) {
                            $delStatement->execute(array(':userId' => $user['userId']));
                        }
                        $statement=$db->prepare('expireActivationHashes','users');
                        $statement->execute(array(':expireTime' => $expireTime));
                        $statement=$db->prepare('checkActivationHash','users');
                        $statement->execute(array(
                            ':userId' => $userId,
                            ':hash' => $hash
                        ));
                        if($attemptExpires=$statement->fetchColumn()) {
                            // Set Email Verified To True
                            $statement = $db->prepare('updateEmailVerification','users');
                            $statement->execute(array(
                                ':userId' => $userId
                            ));
                            // If Email Verification Is Enough, Then Activate The User.
                            if($data->settings['requireActivation'] == 0) {
                                $statement=$db->prepare('activateUser','users');
                                $statement->execute(array(
                                    ':userId' => $userId
                                ));
                            }
                            $statement=$db->prepare('deleteActivation','users');
                            $statement->execute(array(
                                ':userId' => $userId,
                                ':hash' => $hash
                            ));
                            if($data->settings['requireActivation']==0) {
                                $data->output['messages'][]='
                                        <p>
                                            '.$data->phrases['users']['accountActivated'].'
                                            <a href="'.$data->linkRoot.'login">'.$data->phrases['users']['clickLogin'].'</a>
                                        </p>
                                    ';
                            } else {
                                $data->output['messages'][]='
                                        <p>
                                            '.$data->phrases['users']['emailVerified'].'
                                        </p>
                                    ';
                            }
                        } else {
                            $data->output['messages'][]='
                                    <p>
                                        '.$data->phrases['users']['userIdOrCodeDoesNotExist'].'
                                    </p>
                                ';
                        }
                    break; // case 'activate'
                }
            }
	    break; // case 'register'
	}
}
function users_content($data){
	$data->loadModuleTemplate('users');
	switch($data->action[1]){
		case 'edit':
			if(isset($data->output['savedOkMessage'])) {
				echo $data->output['savedOkMessage'];
			} else {
				theme_contentBoxHeader($data->phrases['users']['editingUser']);
				//theme_EditSettings($data);
				theme_buildForm($data->output['userForm']);
				theme_contentBoxFooter();
			}
		break;
        case 'login':
            theme_contentBoxHeader($data->phrases['users']['userLogin']);
            $data->loadModuleTemplate('users');
            theme_loginForm($data);
            theme_contentBoxFooter();
        break;
        case 'logout':
            common_redirect_local($data, '');
        break;
        case 'register':
        	if ($data->output['showForm']) {
        		$data->output['registerForm']->build();
        	} else {
	        	theme_contentBoxHeader($data->phrases['users']['accountRegistrationActivation']);
	        	foreach ($data->output['messages'] as $message) {
		        	echo '<p>',$message,'</p>';
		        }
		        theme_contentBoxFooter();
		    }
        break;
	}
}
?>