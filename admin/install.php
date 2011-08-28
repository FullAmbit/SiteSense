<?php

$settings=array(

	'setupPassword'=> 'startitup',

	'saveToDb' => array(
		'siteTitle' => 'SiteSense CMS',
		'defaultModule' => 'default',
		'theme' => 'default',
		'language' => 'en',
		'characterEncoding' => 'utf-8',
		'compressionEnabled' => true,
		'compressionLevel' => 9,
		'userSessionTimeOut' => 1800, /* in seconds */
		'useModRewrite' => true,
		'hideContentGuests' => 'no',
		'showPerPage' => 5,
		'footerContent' => '
			<p class="trailer">
				This website is best viewed with eyeballs.
			</p><p>
				SiteSense CMS &copy; 2011 <a href="http://www.cutcodedown.com">Paladin Systems North</a>,
				All Rights Reserved
			</p>
		'
	)
);

function makeTable($name,$structure,$data) {
	echo '
				<h2>Making/Checking "<span>',$name,'</span>" Table</h2>';

	$exists=$data->tableExists($name);

	if ($exists && ($_POST['cbDrop']=='drop')) {
		echo '
				<p>Dropping existing table</p>';

		$data->exec('dropTable','installer',$name);
		$exists=false;
	}

	if (!$exists) {

		try {

			echo '
				<h3>Attempting table Creation</h3>';

			if ($data->createTable($name,$structure,true)) {

				echo '
				<p class="success">
					PDO operation complete, no reported errors.
				</p>';

	 		} else {

		 		$data->installErrors++;
		 		echo '
		 			<p class="error">Failed to create ',$name,' table</p>
		 			<pre>',print_r($data->errorInfo()),'</pre><br />';
	 		}

		} catch(PDOException $e) {
	 		$data->installErrors++;
			echo '
				<p class="error">Failed to create '.$name.' table!</p>
				<pre>'.$e->getMessage().'</pre>';
		}
		return true;
	} else {
		echo  '
				<p class="exists">
					"',$name,' database" already exists
				</p>';

		/* should add restructuring code here! */

		return false;
	}
}


echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
	xmlns="http://www.w3.org/1999/xhtml"
	lang="en"
	xml:lang="en"
><head>

<meta
	http-equiv="Content-Type"
	content="text/html; charset=utf-8"
/>

<meta
	http-equiv="Content-Language"
	content="en"
/>

<link
	type="text/css"
	rel="stylesheet"
	href="themes/default/installer.css"
	media="screen,projection,tv"
/>

<title>
	PaladinCMS Installer
</title>

</head><body>

<h1>Paladin CMS Installer/Upgrader</h1>
';

if (
	!isset($_POST['spw']) ||
	($_POST['spw']!==$settings['setupPassword'])
) {

	echo (
		(isset($_POST['spw']) && ($_POST['spw']!=$settings['setupPassword'])) ? '
<p class="error">Incorrect Setup Password</p>' :
			''
		),'

<form action="?install" method="post">
	<fieldset>
		<label for="spw">Please Enter Your Setup Password to Continue<br /></label>
		<input type="password" id="spw" name="spw" width="24" /><br /><br />
		<label for="cbDrop">
			<input type="checkBox" class="checkBox" id="cbDrop" name="cbDrop" value="drop" />
			Drop all tables first?<br />
		</label>
		<p class="warning">*** WARNING *** Dropping all tables will erase ALL entries in the CMS!</p>
	</fieldset>
</form>';

} else {

	$data->loadModuleQueries('installer',true);
	$structures=installer_structures();

	echo '<p>Connect to Database Successful</p>';

	if (makeTable('settings',$structures['settings'],$data)) {
		try {
			$statement=$data->prepare('addSetting','installer');
			echo '
				<div>';
			foreach ($settings['saveToDb'] as $key => $value) {
				$statement->execute(array(
					':name' => $key,
					':category' => 'cms',
					':value' => $value
				));
				$result=$statement->fetchAll();
				echo '
					Created ',$key,' Entry<br />';
			}
			echo '
				</div><br />';
		} catch (PDOException $e) {
			$installErrors++;
			echo '
				<h2>Database Connection Error</h2>
				<pre>'.$e->getMessage().'</pre>';
		}
	}

	makeTable('users',$structures['users'],$data);

	$count=$data->countRows('users');

	if ($count==0) {
		try {
			$newPassword=common_randomPassword();
			echo '
				<h3>Attempting to add admin user</h3>';

			$statement=$data->prepare('addUser','installer');
			$statement->execute(array(
				':name' => 'admin',
				':passphrase' => hash('sha256',$newPassword),
				':userLevel' => 255,
				':registeredDate' => time(),
				':registeredIP' => $_SERVER['REMOTE_ADDR']
			));
			echo '
				<p>Administrator account automatically generated!</p>';
		} catch(PDOException $e) {
			$installErrors++;
			echo '
				<h3 class="error">Failed to create administrator account!</h3>
				<pre>',$e->getMessage(),'</pre><br />';
		}
	} else echo '<p class="exists">"users database" already contains records</p>';

	makeTable('pages',$structures['pages'],$data);
	$count=$data->countRows('pages');
	if ($count==0) {
		try {
			echo '
				<h3>Attempting:</h3>';

			$data->exec('makeRegistrationAgreement','installer');
			echo '
				<div>
					Registration Agreement Page Generated!
				</div><br />
			';
		} catch(PDOException $e) {
			$installErrors++;
			echo '
				<h2>Failed to create registration agreement!</h2>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
		try {
			echo '
				<h3>Attempting:</h3>';

			$data->exec('makeRegistrationEMail','installer');
			echo '
				<div>
					Registration E-Mail Page Generated!
				</div><br />
			';
		} catch(PDOException $e) {
			$installErrors++;
			echo '
				<h2>Failed to create registration E-Mail!</h2>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
	} else echo '<p class="exists">"pages database" already contains records</p>';

	makeTable('sessions',$structures['sessions'],$data);
	makeTable('sidebars',$structures['sidebars'],$data);
	makeTable('mainMenu',$structures['mainMenu'],$data);
	makeTable('activations',$structures['activations'],$data);
	makeTable('userpms',$structures['userpms'],$data);
	makeTable('blogs',$structures['blogs'],$data);
	$count=$data->countRows('blogs');
	$installErrors=0;
	if ($count==0) {
		try {
			echo '
				<h3>Attempting:</h3>';

			$data->exec('makeNewsBlog','installer');
			echo '
				<div>
					Home Page News Blog Generated!
				</div><br />
			';
		} catch(PDOException $e) {
			$installErrors++;
			echo '
				<h2>Failed to create Home Page News Blog!</h2>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
	} else echo '<p class="exists">"blogs database" already contains records</p>';

	echo '';

	makeTable('blogPosts',$structures['blogPosts'],$data);
	$count=$data->countRows('blogPosts');
	if ($count==0) {
		try {

			echo '
				<h3>Attempting to add Welcome Post</h3>';

			$statement=$data->prepare('makeWelcomePost','installer');
			$statement->execute(array(':time' => time()));
			echo '
				<div>
					Home Page Welcome Post Generated!<br />
				</div><br />';

		} catch(PDOException $e) {
			$installErrors++;
			echo '
				<p class="error">Failed to create Home Page Welcome Post!</p>
				<pre>'.$e->getMessage().'</pre><br />
			';
		}
	} else echo '<p class="exists">"blogs database" already contains records</p>';
	makeTable('blogcomments',$structures['blogcomments'],$data);
	makeTable('galleryalbums',$structures['galleryalbums'],$data);
	makeTable('galleryimages',$structures['galleryimages'],$data);
	makeTable('gallerycomments',$structures['gallerycomments'],$data);
	makeTable('urlremap',$structures['urlremap'],$data);
	makeTable('customforms',$structures['customforms'],$data);
	makeTable('customformfields',$structures['customformfields'],$data);
	makeTable('customformrows',$structures['customformrows'],$data);
	makeTable('customformvalues',$structures['customformvalues'],$data);
	makeTable('modules',$structures['modules'],$data);
	$data->query('makeModules','installer');
	makeTable('modulesidebars',$structures['modulesidebars'],$data);
	if ($installErrors==0) {

		echo '
			<h2 id="done">Complete</h2>
			<p class="success">
				Installation/Verification Completed Successfully
			</p><p>
				It is recommended to log into the Admin panel and go to the "mainMenu" function to populate the menu functions. Until you do so, there will be no menu. Any sidebars you have installed will also not show until you enable them in the Admin sidebar control.
			</p>';

		if (isset($newPassword)) {
			echo '
			<p>
				A new administrator login was created. You must use the following information to log into the system:
			</p>
			<dl>
				<dt>Username:</dt><dd>admin</dd>
				<dt>Password:</dt><dd>',$newPassword,'</dd>
			</dl>
			<p>
				Changing the password is recommended.
			</p>';
		}

	} else {

		echo '
			<h2 id="done">Errors Present</h2>
			<p>
				We were unable to build the databases properly. Please review the above errros before attempting to use this installation.
			</p>';

	}
}

	echo '
</body></html>';

?>