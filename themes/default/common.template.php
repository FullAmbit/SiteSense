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

		if(!empty($menuItem['url'])) {
			$matched=0;
			$count=0;
			foreach(explode('/',$menuItem['url']) as $key => $value) {
				$count++;
				if($value==$data->action[$key]) {
					$matched++;
				} else {
					break;
				}
			}
			if($count==$matched) {
				$class[]='current';
			}
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
				echo '<ul>';
				// Loop Through Children
				foreach($children as $index => $child)
				{
					// Unset From Overall Menu List (It's Not A Parent)
					echo '<li><a href="',$data->linkRoot,$child['url'],'">',$child['text'],'</a></li>';
				}
				echo '</ul>';
			}
		echo '</li>';
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


function theme_sidebarBoxHeader($heading,$headingURL='') {
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
} // theme_sidebarBoxHeader


function theme_sidebarBoxFooter() {
	echo '

				<!-- .innerBox --></div>
			<!-- .sidebarBox --></div>
';
}

function theme_leftSidebar($data) {

	echo '

		<!-- #content, #contentWrapper --></div></div>';
	// Check If We Have Any Sidebars	
	if(empty($data->sidebarList['left']))
	{
		return;
	}

	echo '
		<div id="leftSidebar">';

	if (count($data->sidebarList['left'])>0) {
		foreach($data->sidebarList['left'] as $sidebar) {
			if ($sidebar['fromFile']) {
				require_once('modules/sidebars/'.$sidebar['name'].'.sidebar.php');
			} else {
				common_parseDynamicValues($data, $sidebar['titleURL']);
				common_parseDynamicValues($data, $sidebar['parsedContent']);
				theme_sidebarBoxHeader($sidebar['title'],$sidebar['titleURL']);
				echo htmlspecialchars_decode($sidebar['parsedContent']);
				theme_sidebarBoxFooter();
			}
		}
	}

	echo '
		<!-- .leftSidebar --></div>';

} // theme_leftSidebar

function theme_rightSidebar($data) {

	// Check If We Have Any Sidebars	
	if(empty($data->sidebarList['right'])) {
		return;
	}

	echo '<div id="rightSidebar">';

	if (count($data->sidebarList)>0) {
		foreach($data->sidebarList['right'] as $sidebar) {
			if ($sidebar['fromFile']) {
				require_once('modules/sidebars/'.$sidebar['name'].'.sidebar.php');
			} else {
				common_parseDynamicValues($data, $sidebar['titleURL']);
				common_parseDynamicValues($data, $sidebar['parsedContent']);
				theme_sidebarBoxHeader($sidebar['title'],$sidebar['titleURL']);
				echo htmlspecialchars_decode($sidebar['parsedContent']);
				theme_sidebarBoxFooter();
			}
		}
	}

	echo '
		<!-- .rightSidebar --></div>';

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

function theme_buildForm($formData,$buffer = FALSE) {

	if($buffer)	{
		ob_start();
	}
	//var_dump($formData);
	echo '
		<form
			method="post"
			action="',rtrim($formData->action,'/').'/','"
			id="',$formData->formPrefix,'form"
			enctype="multipart/form-data"
			class="commonForm"
		>';
	if ($formData->error) {
		echo '
			<div class="errorBox">',$formData->errorText,'</div>';
	}
	echo '
			<div class="fieldsetWrapper"><fieldset>',(
	isset($formData->caption) ? '
				<legend><span>'.$formData->caption.'</span></legend>' :
		''
	);
	foreach ($formData->fields as $formDataKey => $formField) {
		if ($formField['params']['type']!='hidden') {
			$class=array();
			if ($formField['tag']=='input') {
				if (!empty($formField['params']['type'])) {
					$class[]='type_'.$formField['params']['type'];
				}
			} else $class[]='type_'.$formField['tag'];
			if (!empty($formField['divClasses'])) {
				$class=array_merge($class,$formField['divClasses']);
			}
			$class=implode(' ',$class);
			if (empty($formField['classes'])) {
				$fieldClass=array();
			} else {
				$fieldClass=$formField['classes'];
			}
			if ($formField['required']) {
				$fieldClass[]='required';
			}
			if ($formField['error']) {
				$fieldClass[]='error';
			}
			if (!empty($formField['description'])) {
				$fieldClass[]='nsDesc';
			}
			$fieldClass=implode(' ',$fieldClass);
			echo '
				<div',(
			$class ? ' class="'.$class.'"' : ''
			),'>
					<label for="',$formData->formPrefix.$formDataKey,'">',$formField['label'],' ',(
			$formField['error'] ? '<b>X</b>' : (
			$formField['required'] ? (
			empty($_POST['fromForm']) ?
				'<i>&raquo;</i>' :
				'<span>&radic;</span>'
			) : ''
			)
			),'</label>
					<div>
						<',$formField['tag'],'
							id="',$formData->formPrefix,$formDataKey,'"',(
			($formField['tag']=='span') ? '' : '
							name="'.$formData->formPrefix.$formDataKey.'"'
			),(
			$fieldClass ? '
							class="'.$fieldClass.'"' : ''
			),(
			empty($formField['checked']) ?	'' : '
							checked="checked"'
			);
			if (!empty($formField['params'])) {
				foreach ($formField['params'] as $attribute => $value) {
					echo '
						',$attribute,'="',$value,'"';
				}
			}
			switch ($formField['tag']) {
				case 'textarea':
					echo '>',htmlspecialchars_decode($formField['value']),'</textarea>';
					if (isset($formField['useEditor'])) {
						echo '
<script type="text/javascript"><!--
	CKEDITOR.replace(\'',$formData->formPrefix,$formDataKey,'\', {
		customConfig:CMSBasePath+"ckeditor/paladin/config.js"
	});
--></script>';
					}
					break;
				case 'select':
					echo '
						>';
					$optgroup = FALSE;
					if(!empty($formField['options'])) {
						foreach ($formField['options'] as $key => $option) {
							$selected='';
							if(empty($formField['value'])) {
								// Selected
								if(isset($formField['selected'])) {
									if(is_array($formField['selected'])) {
										foreach($formField['selected'] as $value) {
											if(is_array($option)) {
												if($value==$option['value']) {
													$selected=' selected="selected"';
												}
											} else {
												if($value==$option) {
													$selected=' selected="selected"';
												}
											}
										}
									} else {
										if(is_array($option)) {
											if($formField['selected']==$option['value']) {
												$selected=' selected="selected"';
											}
										} else {
											if($formField['selected']==$option) {
												$selected=' selected="selected"';
											}
										}
									}
								}
							} else {
								// Return bad entry
								if($formField['value']==$option['value']) {
									$selected=' selected="selected"';
								}
								if(is_array($formField['value'])) {
									foreach($formField['value'] as $value) {
										if(is_array($option)) {
											if($value==$option['value']) {
												$selected=' selected="selected"';
											}
										} else {
											if($value==$option) {
												$selected=' selected="selected"';
											}
										}
									}
								} else {
									if(is_array($option)) {
										if($formField['value']==$option['value']) {
											$selected=' selected="selected"';
										}
									} else {
										if($formField['value']==$option) {
											$selected=' selected="selected"';
										}
									}
								}
							}
							if (is_array($option)) {
								if(isset($option['optgroup']) && $option['optgroup'] !== $optgroup)
								{
									if($optgroup !== FALSE)
									{
										echo '
										</optgroup>';
									}
									$optgroup = $option['optgroup'];
									echo '
								<optgroup label = "',$option['optgroup'],'">';
								}
								echo '
								<option',$selected,' value="',$option['value'],'">',$option['text'],'</option>';
								if(!isset($formField['options'][$key+1]) && $optgroup)
								{
									echo '</optgroup>';
								}
							} else {
								echo '
								<option',$selected,'>',$option,'</option>';
							}
						}
					}
					echo '
						</select>';
					break;
				case 'span':
					echo '>',htmlspecialchars($formField['value']),'</span>';
					break;
				default:
					if (!empty($formField['value'])) {
						echo '
							value="',htmlspecialchars($formField['value']),'"';
					}
					echo '
						/>';
			}
			echo '
					</div>';
			if (count($formField['errorList'])>0) {
				echo '
					<ul class="errorMessages">';
				foreach ($formField['errorList'] as $message) {
					echo '
						<li>',$message,'</li>';
				}
				echo '
					</ul>';
			}
			echo '
				</div>';
		}
	}
	echo '
			</fieldset></div>
			<div class="submitsAndHiddens">
				<input type="submit" class="submit" value="',$formData->submitTitle,'" />
				<input type="hidden" name="fromForm" id="fromForm" value="',$formData->fromForm,'" />';
	foreach ($formData->fields as $formDataKey => $formField) {
		if ($formField['params']['type']=='hidden') {
			echo '
			<input type="hidden"
					name="',$formData->formPrefix.$formDataKey,'"
					id="',$formData->formPrefix.$formDataKey,'"
				value="',$formField['value'],'"
			/>';
		}
	}
	echo '
				<i>&raquo;</i> Indicates a required field',(
	$formData->error ? ', <b>X</b> indicates a field with errors' : ''
	),(
	(strlen($formData->extraMarkup)==0) ? '' : '<div class="extraMarkup">
        '.$formData->extraMarkup.'
      <!-- .extraMarkup --></div>'
	),'
			<!-- .submitsAndHiddens --></div>
		</form>';

	if($buffer)	{
		$contents = ob_get_clean();
		return $contents;
	}
}
?>