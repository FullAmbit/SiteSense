<?php

function page_getUniqueSettings($data) {
	$data->output['pageShortName']='register';
}

function sendActivationEMail($data,$db,$userId,$hash,$sendToEmail) {
	$statement=$db->prepare('getRegistrationEMail','register');
	$statement->execute();
	if ($mailBody=$statement->fetchColumn()) {
		$activationLink='http://'.$_SERVER['SERVER_NAME'].$data->linkRoot.'register/activate/'.$userId.'/'.$hash;
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
<title>Activating Your Account</title>
</head><body>
'.$mailBody.'
</body></html>';


	  $data->output['messages'][]='
	  	<p>
	  		Your Activation Link has been e-mailed to '.$sendToEmail.'. It should arrive within a few minutes. If it does not arrive within 48 hours please use our contact form to have one of our staff assist you. Activation links and their associated accounts are automatically deleted after two weeks.
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
				The activation E-Mail appears to have been deleted from this CMS. Please use our contact form to notify the administrator of this problem.
			</p>
		';

	}
	return false;
}

function page_buildContent($data,$db) {
	require_once('libraries/forms.php');
	require_once($data->themeDir.'formGenerator.template.php');
	$data->output['registerForm']=new formHandler('register',$data);
	$data->output['showForm']=true;
	$data->output['messages']=array();
	if (
		isset($_POST['fromForm']) &&
		($_POST['fromForm']==$data->output['registerForm']->fromForm)
	) {
		$data->output['registerForm']->populateFromPostData();
		if ($data->output['registerForm']->validateFromPost()) {
			if ($data->getUserIdByName($data->output['registerForm']->sendArray[':name'])) {
				$data->output['registerForm']->fields['name']['error']='true';
				$data->output['registerForm']->fields['name']['errorList'][]='Name already exists';
			} else {
				unset($data->output['registerForm']->sendArray[':password2']);
				unset($data->output['registerForm']->sendArray[':verifyEMail']);
				$data->output['registerForm']->sendArray[':registeredDate']=time();
				$data->output['registerForm']->sendArray[':registeredIP']=$_SERVER['REMOTE_ADDR'];
				$data->output['registerForm']->sendArray[':lastAccess']=time();
				$data->output['registerForm']->sendArray[':userLevel']=0;
				$data->output['registerForm']->sendArray[':publicEMail']='';
				$data->output['registerForm']->sendArray[':password']=hash(
					'sha256',
					$data->output['registerForm']->sendArray[':password']
				);
				$statement=$db->prepare('insertUser','register');
				$statement->execute($data->output['registerForm']->sendArray) or die('Saving user failed');
				$userId = $db->lastInsertId();
				$profileAlbum = $db->prepare('addAlbum', 'gallery');
				$profileAlbum->execute(array(':user' => $userId, ':name' => 'Profile Pictures', ':shortName' => 'profile-pictures', 'allowComments' => 0));
				$hash=md5(common_randomPassword(32,32));
				$statement=$db->prepare('insertActivationHash','register');
				$statement->execute(array(
					':userId' => $userId,
					':hash' => $hash,
					':expires' => time()+(14*24*360)
				));

				sendActivationEMail($data,$db,$userId,$hash,$data->output['registerForm']->sendArray[':contactEMail']);

				$data->output['showForm']=false;
			}
		}

	} else {
		switch ($data->action[1]) {

			case 'activate':
				$data->output['showForm']=false;
				$userId=$data->action[2];
				$hash=$data->action[3];
				/*
					We have to use a var for time so as to not have accounts
					'slip through the cracks' waiting for the queries to execute
				*/
				$expireTime=time();

				$statement=$db->prepare('getExpiredActivations','register');
				$statement->execute(array(':expireTime' => $expireTime));

				$delStatement=$db->prepare('deleteUserById','register');
				while ($user=$statement->fetch()) {
					$delStatement->execute(array(':userId' => $user['userId']));
				}

				$statement=$db->prepare('expireActivationHashes','register');
				$statement->execute(array(':expireTime' => $expireTime));

				$statement=$db->prepare('checkActivationHash','register');
				$statement->execute(array(
					':userId' => $userId,
					':hash' => $hash
				));
				if ($attemptExpires=$statement->fetchColumn()) {
					$statement=$db->prepare('activateUser','register');
					$statement->execute(array(
						'userId' => $userId
					));

					$statement=$db->prepare('deleteActivation','register');
					$statement->execute(array(
						':userId' => $userId,
						':hash' => $hash
					));

					$data->output['messages'][]='
						<p>
							Your account has been activated.
							<a href="'.$data->linkRoot.'login">Click here to Log in</a>
						</p>
					';

				} else {
					$data->output['messages'][]='
						<p>
							That user ID or Security Code do not exist in our database. Activation Codes are removed after two weeks. Please try and resubmit your activation.
						</p>
					';
				}
			break;
			case 'lostPassword':
			/* lost password handler here */
			default:
			/* no default as yet, just show form */
		}
	}
}

function page_content($data) {
	if ($data->output['showForm']) {
		theme_buildForm($data->output['registerForm']);
	} else {
		theme_contentBoxHeader('Account Registration &amp; Activation');
		foreach ($data->output['messages'] as $message) {
			echo '<p>',$message,'</p>';
		}
		theme_contentBoxFooter();
	}

}
?>