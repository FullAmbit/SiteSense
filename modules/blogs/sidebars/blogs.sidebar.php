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
$rssLink = isset($data->output['blogInfo']['rssOverride']{1}) ? $data->output['blogInfo']['rssOverride'] : $data->localRoot.'/rss';
echo '
			<div class="sidebarBox blogSidebar">
				<div class="innerBox">

					<div class="buttonWrapper">
						<a href="',$rssLink,'" class="greenButton"><span>
							<b></b>Subscribe
							<i><!-- hover state precache --></i>
						</span></a>
					<!-- .buttonWrapper --></div>';
// Loop Through Categories
if($data->output['blogCategoryList']) {
	echo '
					<h2>Blog Categories</h2>
					<ul>';
	foreach($data->output['blogCategoryList'] as $categoryItem)
	{
			echo '
						<li>
							<a href="',$data->localRoot,'/categories/',$categoryItem['shortName'],'">',$categoryItem['name'],'</a>
						</li>';	
	}
	echo '
					</ul>';
	}
theme_sideBarBoxFooter();
?>