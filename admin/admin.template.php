<?php

function theme_header($data) {

	$data->admin['linkRoot']=$data->linkHome.'admin/';

	echo'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
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

<link
	type="text/css"
	rel="stylesheet"
	href="',$data->admin['linkRoot'],'admin.screen.css"
	media="screen,projection,tv"
/>

<script
	type="text/javascript"
	src="',$data->linkRoot,'ckeditor/ckeditor.js"
></script>

<script type="text/javascript"><!--
	var CMSBasePath="',$data->linkRoot,'";
--></script>

<title>
	',(
		empty($data->output['pageTitle']) ? '' : $data->output['pageTitle'].' '
	),'Control Panel - ',$data->settings['siteTitle'],'
</title>

</head><body>

<div id="pageWrapper">

	<h1>Control Panel - ',$data->settings['siteTitle'],'</h1>';

	if ($data->user['userLevel']>0) {
		echo '
	<div id="loggedBar" class="buttonList">
		<a href="',$data->linkRoot,'logout">Logout</a>
		You are currently logged in as <b>',$data->user['name'],'</b>
	</div>';
	}
	echo '

	<div id="contentWrapper"><div id="content">

';
}

function theme_footer($data) {
	echo '
	<!-- #pageWrapper --></div>

<script type="text/javascript"
	src="',$data->admin['linkRoot'],'admin.js"
></script>

</body></html>';
}

function theme_sideBar($data) {
	echo '
	<!-- #content,#contentWrapper --></div></div>';

	if ($data->user['userLevel']>=USERLEVEL_MODERATOR) {
		if (!empty($data->output['forceMenu'])) {
			$currentCompare=$data->output['forceMenu'];
		} else if (empty($data->action[1])) {
			$currentCompare='about';
		} else {
			$currentCompare=$data->action[1].(
				empty($data->action[2]) ? '' : '/'.$data->action[2]
			);
		}
		echo '
	<div id="sideBar">';

		if (count($data->admin['menu'])>0) {
			$category='';
			foreach ($data->admin['menu'] as $menuItem) {
				$current=$menuItem['command']==$currentCompare;
				if ($menuItem['category']!=$category) {
					if (!empty($category)) {
						echo '
		</ul>';
					}
					$category=$menuItem['category'];
					echo '
		<h2>',$category,'</h2>
		<ul>';
				}
				echo '
			<li',($current ? ' class="current"' :	''),'>
				<a href="',$data->linkRoot,'admin/',$menuItem['command'],'">
					',$menuItem['name'],($current ? ' >>' : ''),'
				</a>
			</li>';

			}
			echo '
		</ul>';
		}
		echo '
	<!-- #sideBar --></div>';
	}
}

function theme_loginForm($data) {

	if (!empty($data->action[1])) {
		$adminLink=implode('/',array_filter($data->action));
	}	else $adminLink='admin';
	
	echo '
			<form action="',$data->linkRoot,$adminLink,'/"
				method="post"
				class="adminLoginForm"
			>
				<div class="fieldsetWrapper"><fieldset>
					<legend><span>Please Log In</span></legend>

					<label for="username">Username:</label>
					<input type="text"
						name="username"
						id="username"
						',(
							!empty($_POST['username']) ?
							'value="'.htmlspecialchars($_POST['username']).'"' :
							''
						),'
					/><br />

					<label for="password">Password:</label>
					<input type="password"
						name="password"
						id="password"
					/><br />


				</fieldset></div>
				<div class="submitsAndHiddens">
					<input type="hidden"
						name="login"
						value="',$_SERVER['REMOTE_ADDR'],'"
					/>
					<input type="hidden"
						name="lastPage"
						value="',$data->currentPage,'"
					/>
					<label for="keepLogged">
						Keep me Logged in:
						<input type="checkbox"
							name="keepLogged"
							id="keepLogged"
						/>
					</label>

					<input type="submit"
						value="Log In"
						class="submit"
					/>
				</div>
			</form>';
}

function theme_secondSideBar($data) {
		echo '
	<div id="secondSideBar">';
	if (!empty($data->output['secondSideBar'])) {
		echo $data->output['secondSideBar'];
	}
		echo'
	<!-- #secondSideBar --></div>';
}

function theme_buildForm($formData) {
	echo '
		<form method="post" action="',$formData->action,'" class="adminForm">';

	if ($formData->error) {
		echo '
			<div class="errorBox">',$formData->errorText,'</div>';
	}
	echo '
			<table class="adminFormTable">',(
				isset($formData->caption) ? '
				<caption>'.$formData->caption.'</caption>' :
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
				<tr',($class ? ' class="'.$class.'"' : ''),'>
					<th>
						<label for="',$formData->formPrefix.$formDataKey,'"',(
							$formField['class'] ? ' class="'.$formField['class'].'"' : ''
						),'>',$formField['label'],'</label>
					</th><td class="reqs">',(
							$formField['error'] ? '<b>X</b>' : (
								$formField['required'] ? (
									empty($_POST['fromForm']) ?
									'<i>&raquo;</i>' :
									'<span>&radic;</span>'
								) : ''
							)
						),'
					</td><td class="fields">
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
					echo '>',htmlspecialchars($formField['value']),'</textarea>';
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
					foreach ($formField['options'] as $option) {
						if (is_array($option)) {
							echo '
							<option',(
								($formField['value']==$option['value']) ?
								' selected="selected"' :
								''
							),' value="',$option['value'],'">',$option['text'],'</option>';
						} else {
							echo '
							<option',(
								($formField['value']==$option) ?
								' selected="selected"' :
								''
							),'>',$option,'</option>';
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

			if ($formField['compareFailed']) {
				echo '
						<div class="compareFailed">
							',$formField['compareFailMessage'],'
						</div>';
			}
			if (!empty($formField['description'])) {
				echo '
						<div class="description" id="ns_',$formData->formPrefix.$formDataKey,'">
							',$formField['description'],'
						</div>';
			}
			echo '
					</td>
				</tr>';
		}

	}
	echo '
			</table>
			<div class="submitsAndHiddens">
		    <input type="submit" class="submit" value="',$formData->submitTitle,'" />
		    <input type="hidden" name="fromForm" id="fromForm" value="',$formData->fromForm,'" />
  ';
	foreach ($formData->fields as $formDataKey => $formField) {
		if ($formField['params']['type']=='hidden') {
			echo '
		    <input type="hidden"
					name="',$formData->formPrefix.$formDataKey,'"
					id="',$formData->formPrefix.$formDataKey,'"
		    	value="',$formField['value'],'"
		    />
		  ';
		}
	}
	if (strlen($formData->extraMarkup)>0) {
		echo $formData->extraMarkup;
	}

	echo '
					<i>&raquo;</i> Indicates a required field',(
						$formData->error ? ', <b>X</b> indicates a field with errors' : ''
					),'

				</div>

			</form>
	';
}

/* theme_buildTable makes a non-form table using $formData format */

function theme_buildTable($formData) {
	echo '
			<table class="adminFormTable noInputs">
				<caption>',$formData->caption,'</caption>';

	foreach ($formData->fields as $formDataKey => $formField) {
		if ($formField['params']['type']!='hidden') {
			if (empty($formField['classes'])) {
				$fieldClass=array();
			} else {
				$fieldClass=$formField['classes'];
			}
			$fieldClass=implode(' ',$fieldClass);
			if (
				($formField['params']['type']!='password') &&
				($formField['params']['type']!='hidden')
			) {
				echo '
				<tr>
					<th>',$formField['label'],':</th>
					<td',(
						empty($fieldClass) ? '' : ' class="'.$fieldClass.'"'
					),'>',htmlspecialchars($formField['value']),'</td>
				</tr>';
			}
		}
	}
	echo '
			</table>';
}

function theme_pagination($count,$current,$linkPrefix) {
	echo '
		<ul class="pagination">
			<li>Pages:</li>';
		$lastPage=floor(($count-1)/ADMIN_SHOWPERPAGE);
		$currentPage=floor($current/ADMIN_SHOWPERPAGE);

		if ($lastPage>0) {
			echo '
				<li><a href="',$linkPrefix,'">First</a></li>';
			if ($currentPage>0) {
				echo '
				<li><a href="',$linkPrefix,($currentPage-1)*ADMIN_SHOWPERPAGE,'">&laquo;</a></li>';
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
					$noAnchor ? '<span>' : '<a href="'.$linkPrefix.($counter*ADMIN_SHOWPERPAGE).'">'
				),++$counter,(
					$noAnchor ? '</span>' : '</a>'
				),'</li>';
		}

		if ($lastPage>0) {
			if ($currentPage<$lastPage) {
				echo '
				<li><a href="',$linkPrefix,($currentPage+1)*ADMIN_SHOWPERPAGE,'">&raquo;</a></li>';
			}
			echo '
				<li><a href="',$linkPrefix,$lastPage*ADMIN_SHOWPERPAGE,'">Last</a></li>
			</ul>
			';
		}

	echo '
		</ul>';
}


?>