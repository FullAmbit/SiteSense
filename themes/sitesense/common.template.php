<?php

function theme_metaList($metaList) {
	foreach ($metaList as $meta) {
		echo "\n\n<meta";
		foreach ($meta as $key => $value) {
			echo "\n\t",$key,'="',$value,'"';
		}
		echo "\n/>";
	}
} // theme_metaList

function theme_header($data) {

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
	xmlns="http://www.w3.org/1999/xhtml"
	xml:lang="',$data->settings['language'],'"
	lang="',$data->settings['language'],'"
><head>';

	theme_metaList($data->metaList);

	echo '

<link rel="shortcut icon" href="',$data->linkRoot,$data->themeDir,'images/favicon.ico" />

<link
	type="text/css"
	rel="stylesheet"
	href="',$data->linkHome,$data->themeDir,'screen.css"
	media="screen,projection,tv"
/>


<script
	type="text/javascript"
	src="/core/ckeditor/ckeditor_basic.js"
></script>

<title>
	',(
		empty($data->output['pageTitle']) ?
		'' :
		$data->output['pageTitle'].' - '
	),$data->settings['siteTitle'],'
</title>

</head><body>

<div id="pageWrapper">

	<h1>
		<a href="',$data->linkRoot,'">
			<b>Site</b>Sense
			<small>making sense of the web</small>
			<span><!-- image replacement --></span>
		</a>
	</h1>
			<form action="',$data->linkRoot,'login"
				method="post"
				id="topLogin"
			>
				<fieldset>
					<div class="normalFields">
						<label for="topUsername">Username:</label><br />
						<input type="text"
							name="username"
							id="topUsername"
							value="admin"
						/><br />
						<input type="checkbox"
							name="keepLogged"
							id="topKeepLogged"
						/>
						<label for="topKeepLogged" class="forCheckbox">
							Remember Me
						</label>
					</div>
					<div class="normalFields">
						<label for="topPassword">Password:</label><br />
						<input type="password"
							name="password"
							id="topPassword"
						/><br />
						<a href="'.$data->linkRoot.'register/forgotPassword">Forget your password?</a>
					</div>
					<div class="submitsAndHiddens">
						<span class="submit">
							<input type="submit"
								value="Log In"
							/>
						</span>
						<input type="hidden"
							name="login"
							value="',$_SERVER['REMOTE_ADDR'],'"
						/>
						<input type="hidden"
							name="lastPage"
							value="',$data->currentPage,'"
						/>
					</div>
				</fieldset>
			</form>
<div id="mainMenu">
	<ul>
		';

	$menuList=array_merge($data->menuList['left'],$data->menuList['right']);

	ksort($menuList); /* keys may not be in order! */

	$last=count($menuList)-1;
	$previousSide='left';
	foreach ($menuList as $key => $menuItem) {
		if($menuItem['parent'] !== '0')
		{
			continue;
		}
		$children = array();
		$class=array($menuItem['side']);
		if ($key==0) $class[]='first';
		if (
			(
				($key==$last) &&
				($menuItem['side']=='left')
			) || $previousSide!==$menuItem['side']
		) {
			$class[]='last';
		}
		$previousSide=$menuItem['side'];
		if ($menuItem['url']==$data->output['pageShortName']) {
			$class[]='current';
		}
		echo '<li',(
			empty($class) ? '': ' class="'.implode(' ',$class).'"'
		),(
			empty($menuItem['title']) ? '' : ' title="'.$menuItem['title'].'"'
		),'>
			<a href="',(
				common_hasUrlPrefix($menuItem['url']) ? '' : $data->linkRoot
			),$menuItem['url'],'">',$menuItem['text'],'<span></span></a>';
			// Get All Children
			for($i=0;$i<count($menuList);$i++)
			{
				if($menuList[$i]['parent'] == $menuItem['id'])
				{
					$children[$i] = $menuList[$i];
				}
			}
			//var_dump($children);
			if(count($children) > 0)
			{
				echo '<div><ul>';
				// Loop Through Children
				foreach($children as $index => $child)
				{
					// Unset From Overall Menu List (It's Not A Parent)
					echo '<li>&raquo; <a href="',$data->linkRoot,$child['url'],'">',$child['text'],'</a></li>';
				}
				echo '</ul></div>';
			}
		echo '</li>';
	}

	echo '
	</ul>
</div>

<div id="homepageBanner">
  <div>
    <div>
      <h2>
        Inspiring developers<small>to build rock solid websites.</small>
        <b></b>
      </h2>
      <a href="#">
        <span>
          Learn More<b></b>
        </span>
      </a>
    </div>
    <div>
      <h2>
        Empowering everyone<small>to easily publish content online.</small>
        <b></b>
      </h2>
      <a href="#">
        <span>
          Learn More<b></b>
        </span>
      </a>
    </div>
  </div>
</div>

	<div id="mainWrapper">
		<div id="contentWrapper"><div id="content">
';

} // theme_header


function theme_contentBoxHeader($heading,$headingURL='',$date=0) {
	echo '
					<div class="contentBox">';
					if($heading!='Home') {
            echo '
						<h2',(empty($headingURL) ? '' : ' class="link"'),'>
							',(
								($date!=0) ?
								'<span>'.date('d F Y H:i T',$date).'<span> - </span></span>' :
								''
							),(
								empty($headingURL) ?
								$heading :
								'<a href="'.$headingURL.'">'.$heading.'</a>'
							),'
						</h2>';
					}
					echo '
						<div class="innerBox">

';
} // theme_contentBoxHeader

function theme_blogContentBoxHeader($heading,$headingURL='',$date=0,$headingLevel=2) {
	echo '
					<div class="contentBox blogBox">
						<h',$headingLevel,(empty($headingURL) ? '' : ' class="link"'),'>',(
								($date!=0) ? '
							<span>'.date('d F Y H:i T',$date).'<span> - </span></span>' :
								''
							),(
								empty($headingURL) ?
								$heading : '
							<a href="'.$headingURL.'">
								'.$heading.($headingLevel==2 ? '' : '
								<small><span>-</span> Read More</small>'
								).'
							</a>'
							),'
						</h',$headingLevel,'>
						<div class="innerBox">

';
} // theme_blogContentBoxHeader

function theme_contentBoxFooter() {
	echo '

						<!-- .innerBox --></div>
					<!-- .contentBox --></div>
';
} // them_contentBoxFooter()


function theme_sideBarBoxHeader($heading,$headingURL='') {
	echo '
			<div class="sidebarBox">
				<h2 class="',(empty($headingURL) ? 'noLink' : 'link'),'">
					',(
						(empty($headingURL)) ?
						'<span>'.$heading.'</span>' :
						'<a href="'.$headingURL.'">'.$heading.'</a>'
					),'
				</h2>
				<div class="innerBox">

';
} // theme_sideBarBoxHeader


function theme_sideBarBoxFooter() {
	echo '

				<!-- .innerBox --></div>
			<!-- .sidebarBox --></div>
';
}

function theme_leftSideBar($data) {

	echo '

		<!-- #content, #contentWrapper --></div></div>';
	// Check If We Have Any Sidebars	
	if(empty($data->sideBarList['left']))
	{
		return;
	}

	echo '
		<div id="leftSidebar">';

	if (count($data->sideBarList['left'])>0) {
		
		foreach($data->sideBarList['left'] as $sideBar) {
			
			if ($sideBar['name']!=$data->output['pageShortName']) {
				if ($sideBar['fromFile']) {
					require_once('sideBars/'.$sideBar['name'].'.sideBar.php');
				} else {
					common_parseDynamicValues($data, $sideBar['titleURL']);
					common_parseDynamicValues($data, $sideBar['parsedContent']);
					theme_sideBarBoxHeader($sideBar['title'],$sideBar['titleURL']);
					echo htmlspecialchars_decode($sideBar['parsedContent']);
					theme_sideBarBoxFooter();
				}
			}
		}
	}

	echo '
		<!-- .leftSideBar --></div>';

} // theme_leftSideBar

function theme_rightSideBar($data) {

	// Check If We Have Any Sidebars	
	if(empty($data->sideBarList['right']))
	{
		return;
	}

	echo '
		<div id="rightSidebar">';

	if (count($data->sideBarList)>0) {
		foreach($data->sideBarList['right'] as $sideBar) {
			if ($sideBar['name']!=$data->output['pageShortName']) {
				if ($sideBar['fromFile']) {
					require_once('sideBars/'.$sideBar['name'].'.sideBar.php');
				} else {
					common_parseDynamicValues($data, $sideBar['titleURL']);
					common_parseDynamicValues($data, $sideBar['parsedContent']);
					theme_sideBarBoxHeader($sideBar['title'],$sideBar['titleURL']);
					echo htmlspecialchars_decode($sideBar['parsedContent']);
					theme_sideBarBoxFooter();
				}
			}
		}
	}

	echo '
		<!-- .rightSideBar --></div>';

}




function theme_footer($data) {
	common_parseDynamicValues($data,$data->settings['footerContent']);
	echo '

	<!-- #mainWrapper --></div>

	<div id="footer">

',htmlspecialchars_decode($data->settings['parsedFooterContent']),'

	<!-- #footer --></div>

<!-- #pageWrapper --></div>

</body></html>';

} // theme_footer

function theme_pagination($count,$current,$showPerPage,$linkPrefix) {
	if($showPerPage == 0){
		//zero-error prevention
		$showPerPage = 5;
	}
	echo '
<ul class="pagination">
	<li>Pages:</li>';
		$pages = intval(ceil($count / $showPerPage));
		$lastPage = $pages;
		$currentPage = ($current == 0) ? 1 : $current;
		
		if($pages > 1)
		{
			if($currentPage > 1)
			{
				echo '
					<li><a href="',$linkPrefix,'1">First</a></li>
					<li><a href="',$linkPrefix,$currentPage-1,'">&lt;</a></li>';
			}
		}
		$counter = 0;
		
		while ($counter < $lastPage)
		{
			$counter++;	
			$noAnchor = ($counter == $currentPage);
			
			echo '<li>';
			if($noAnchor)
			{
				echo '<span>',$counter,'</span>';
			} else {
				echo '<a href="',$linkPrefix,$counter,'">',$counter,'</a>';
			}
		}
		
		if ($lastPage > $currentPage && $lastPage > 1) {
			echo '
				<li><a href="',$linkPrefix,($currentPage+1),'">&gt;</a></li>
				<li><a href="',$linkPrefix,$lastPage,'">Last</a></li>';
		}

	echo '
</ul>';
}

function theme_accountSettings($data) {
	if (!empty($data->output['savedOkMessage'])) {
	echo '
		<p>Your profile has been successfully updated. <a href="',$data->linkRoot.$data->currentPage,'">Return to your account</a></p>';
	} else {
		theme_buildForm($data->output['userForm']);
	}
}


?>