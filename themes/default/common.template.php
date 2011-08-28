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
			',$data->settings['siteTitle'],'
			<span><!-- image replacement --></span>
		</a>
	</h1>

	<ul id="mainMenu">
		';

	$menuList=array_merge($data->menuList['left'],$data->menuList['right']);

	ksort($menuList); /* keys may not be in order! */

	$last=count($menuList)-1;
	$previousSide='left';
	foreach ($menuList as $key => $menuItem) {
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
			),$menuItem['url'],'">',$menuItem['text'],'<span></span></a>
		</li>';
	}

	echo '
	</ul>

	<div id="mainWrapper">
		<div id="contentWrapper"><div id="content">
';

} // theme_header


function theme_contentBoxHeader($heading,$headingURL='',$date=0) {
	echo '
					<div class="contentBox">
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
						</h2>
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

function theme_sideBar($data) {

	echo '

		<!-- #content, #contentWrapper --></div></div>

		<div id="sidebar">';

	if (count($data->sideBarList)>0) {
		foreach($data->sideBarList as $sideBar) {
			if ($sideBar['name']!=$data->output['pageShortName']) {
				if ($sideBar['fromFile']) {
					require_once('sideBars/'.$sideBar['name'].'.sideBar.php');
				} else {
					common_parseDynamicValues($data, $sideBar['titleURL']);
					common_parseDynamicValues($data, $sideBar['content']);
					theme_sideBarBoxHeader($sideBar['title'],$sideBar['titleURL']);
					echo $sideBar['content'];
					theme_sideBarBoxFooter();
				}
			}
		}
	}

	echo '
		<!-- .sideBar --></div>';

} // theme_sideBar


function theme_footer($data) {
	common_parseDynamicValues($data,$data->settings['footerContent']);
	echo '

	<!-- #mainWrapper --></div>

	<div id="footer">

',$data->settings['footerContent'],'

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
		$lastPage=floor(($count-1)/$showPerPage);
		$currentPage=floor($current/$showPerPage);

		if ($lastPage>0) {

			if ($current>0) echo '
	<li><a href="',$linkPrefix,'0">First</a></li>';

			if ($currentPage>0) {
				echo '
	<li><a href="',$linkPrefix,($currentPage-1)*$showPerPage,'">&lt;</a></li>';
			}
		}

		if ($lastPage>9) {
			$counter=($currentpage<6) ? 0 : $currentPage-5;
			$endPage=$counter+10;
			if ($endPage>$lastPage) $endPage=$lastPage;
		} else {
			$counter=0;
			$endPage=$lastPage;
		}

		while ($counter<=$endPage) {
			$noAnchor=($counter==$currentPage);
			echo '
	<li>',(
					$noAnchor ? '<span>' : '<a href="'.$linkPrefix.($counter*$showPerPage).'">'
				),++$counter,(
					$noAnchor ? '</span>' : '</a>'
				),'</li>';
		}

		if ($lastPage>0) {
			if ($currentPage<$lastPage) {
				echo '
	<li><a href="',$linkPrefix,($currentPage+1)*$showPerPage,'">&gt;</a></li>
	<li><a href="',$linkPrefix,$lastPage*$showPerPage,'">Last</a></li>';
			}
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