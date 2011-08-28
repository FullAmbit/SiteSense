<?php

$this->action=$data->linkRoot.'admin/cmsSettings';
$this->formPrefix='cmsSettings_';
$this->caption='CMS Base Settings';
$this->submitTitle='Save Changes';
$this->fromForm='cmsSettings';
$this->fields=array(
	'siteTitle' => array(
		'label' => 'Site Title',
		'required' => true,
		'tag' => 'input',
		'value' => $data->settings['siteTitle'],
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Site Title</b> - Used to construct the TITLE tag and in most skins the content of the top level heading (H1).
			</p>
		'
	),
	'theme' => array(
		'label' => 'Default Theme',
		'tag' => 'select',
		'value' => $data->settings['theme'],
		'description' => '
			<p>
				<b>Default Theme</b> - Determines which of your installed themes users will default to.
			</p>
		'
	),
	'language' => array(
		'label' => 'Language',
		'tag' => 'select',
		'value' => $data->settings['language'],
		'options' => array(
			'en','es','de'
		),
		'description' => '
			<p>
				<b>Language</b> - Sets the HTML <code>lang</code> and <code>xml:lang</code> attributes, the <code>Content-Language</code> meta-tag, <i>and at some point the CMS language strings</i>.
			</p>
		'
	),
	'defaultModule' => array(
		'label' => 'Default Module',
		'tag' => 'select',
		'value' => $data->settings['defaultModule'],
		'options' => array(),
		'description' => '
			<p>
				<b>Default Module</b> - What will users see first when they visit your site?
			</p>
		'
	),
	'characterEncoding' => array(
		'label' => 'character Encoding',
		'tag' => 'select',
		'value' => $data->settings['characterEncoding'],
		'options' => array(
			'utf-8','iso-8859-1','windows-1252'
		),
		'description' => '
			<p>
				<b>Character Encoding</b> - Not only sets up the <code>Content-Encoding</code> meta-tag, but is also used by PHP\'s "header" function to set the mime-type in the http response header.
			</p>
		'
	),
	'compressionEnabled' => array(
		'label' => 'Compression Enabled',
		'tag' => 'input',
		'checked' => ($data->settings['compressionEnabled'] ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Compression Enabled</b> - Turns on and off server gzip/mod_deflate compression if available.
			</p>
		'
	),
	'compressionLevel' => array(
		'label' => 'Compression Level',
		'tag' => 'select',
		'value' => $data->settings['compressionLevel'],
		'options' => array(
			1,2,3,4,5,6,7,8,9
		),
		'description' => '
			<p>
				<b>Compression Level</b> - If compression is on and this is enabled, sets the amount of compression used. 1 for least compression and low CPU load, 9 for the most compression and high CPU use.
			</p>
		'
	),
	'userSessionTimeOut' => array(
		'label' => 'Default Session Timeout (seconds)',
		'required' => true,
		'tag' => 'input',
		'value' => $data->settings['userSessionTimeOut'],
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Default Session timeout</b> - This value is how long a user account is normally allowed to remain logged in without requesting any pages. Stated in seconds, so:
			</p>
			<ul>
				<li>300 = 5 minutes</li>
				<li>1800 = 30 minutes</li>
				<li>3600 = 1 hour</li>
			</ul>
		'
	),
	'useModRewrite' => array(
		'label' => 'Use modRewrite (.htaccess)',
		'tag' => 'input',
		'checked' => ($data->settings['useModRewrite'] ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Use modRewrite</b> - If enabled this will build all links to assume that the rewrite engine is in use and redirecting all calls to the system to index.php directly. If enabled your .htacces should include something like this:
			</p>
			<pre><code>RewriteEngine On
RewriteRule !\.(gif|jpg|png|css|js|swf|html|ico|zip|rar|pdf|xml|mp4|mpg|flv|mkv)$ index.php</code></pre>
			<p>
				You will want to make sure any and all data types you do not want the CMS to handle have their file extensions listed above
			</p>
		'
	),
	'hideContentGuests' => array(
		'label' => 'Hide Content From Guests',
		'tag' => 'select',
		'value' => $data->settings['hideContentGuests'],
		'options' => array(
			'no','login','register'
		),
		'description' => '
			<p>
				<b>Hide Content From Guests</b> - You can set the CMS to show either the login page or the registration page instead of the normal content to guests
			</p>
		'
	),
	'showPerPage' => array(
		'label' => 'Blog Items Per Page',
		'required' => true,
		'tag' => 'input',
		'value' => $data->settings['showPerPage'],
		'params' => array(
			'type' => 'text',
			'size' => 2
		),
		'description' => '
			<p>
				<b>Blog Items Per Page</b> - Determines how many blog posts are shown on a list page. This includes the number of news items to be shown on the home page.
			</p>
		'
	),
	'footerContent' => array(
		'label' => 'Footer Content',
		'tag' => 'textarea',
		'value' => $data->settings['footerContent'],
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 10
		),
		'description' => '
			<p>
				<b>Footer Content</b> - This is what is displayed inside the <code>div#footer</code> on every page. Typically you would put the site disclaimer there.
			</p>
		'
	)
);