<?php
function theme_customizeHeader($title) {
	echo '
		<div class="contentBox">
			<h2>',$title,'</h2>
			<div class="innerBox">';
}

function theme_customizeFooter() {
	echo '
			   <!-- .innerBox --></div>
		<!-- .contentBox --></div>';
}

function theme_customizeContentMain($linkRoot) {
	echo '<ul>
				<li><a href="',$linkRoot,'/themes">Themes</a></li>
				<li><a href="',$linkRoot,'/modules">Modules</a></li>
				<li><a href="',$linkRoot,'/plugins">Plugins</a></li>
			</ul>';
}

function theme_customizeContentTags($data) {
	echo '<h3>Tags</h3>';
}

function theme_customizeContentThemes($data) {
	echo '<h3>Themes</h3>';
}

function theme_customizeListThemeRow($theme,$linkRoot) {
	echo '- <a href="',$linkRoot,'/themes/',$theme['shortName'],'">',$theme['name'],'</a><br/>';
}

function theme_customizeShowTheme($theme) {
	echo '
	<strong>Name:</strong> ',$theme['name'],'<br/>
	<strong>Author:</strong> ',$theme['author'],'<br/>
	<strong>Owner:</strong> ',$theme['owner'],'<br/>
	<strong>Rating:</strong> ',$theme['rating'],'<br/>
	<strong>Approved:</strong> ',($theme['approved'])?'Yes':'No','<br/>
	<strong>Date Added:</strong> ',date("F j, Y, g:i a",$theme['dateAdded']),'<br/>
	<strong>Last Updated:</strong> ',date("F j, Y, g:i a",$theme['lastUpdated']),'<br/>
	<strong>Description:</strong><p>',$theme['description'],'</p>
	';
}

function theme_customizeContentModules($data) {
	echo '<h3>Modules</h3>';
}

function theme_customizeListModuleRow($module,$linkRoot) {
	echo '- <a href="',$linkRoot,'/modules/',$module['shortName'],'">',$module['name'],'</a><br/>';
}

function theme_customizeShowModule($module) {
	echo '
	<strong>Name:</strong> ',$module['name'],'<br/>
	<strong>Author:</strong> ',$module['author'],'<br/>
	<strong>Owner:</strong> ',$module['owner'],'<br/>
	<strong>Rating:</strong> ',$module['rating'],'<br/>
	<strong>Approved:</strong> ',($module['approved'])?'Yes':'No','<br/>
	<strong>Date Added:</strong> ',date("F j, Y, g:i a",$module['dateAdded']),'<br/>
	<strong>Last Updated:</strong> ',date("F j, Y, g:i a",$module['lastUpdated']),'<br/>
	<strong>Description:</strong><p>',$module['description'],'</p>
	';
}

function theme_customizeContentPlugins($data) {
	echo '<h3>Plugins</h3>';
}

function theme_customizeListPluginRow($plugin,$linkRoot) {
	echo '- <a href="',$linkRoot,'/modules/',$plugin['shortName'],'">',$plugin['name'],'</a><br/>';
}

function theme_customizeShowPlugin($plugin) {
	echo '
	<strong>Name:</strong> ',$plugin['name'],'<br/>
	<strong>Author:</strong> ',$plugin['author'],'<br/>
	<strong>Owner:</strong> ',$plugin['owner'],'<br/>
	<strong>Rating:</strong> ',$plugin['rating'],'<br/>
	<strong>Approved:</strong> ',($plugin['approved'])?'Yes':'No','<br/>
	<strong>Date Added:</strong> ',date("F j, Y, g:i a",$plugin['dateAdded']),'<br/>
	<strong>Last Updated:</strong> ',date("F j, Y, g:i a",$plugin['lastUpdated']),'<br/>
	<strong>Description:</strong><p>',$plugin['description'],'</p>
	';
}


?>