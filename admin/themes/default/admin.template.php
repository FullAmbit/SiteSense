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
	href="',$data->linkRoot,'admin/themes/default/admin.screen.css"
	media="screen,projection,tv"
/>';
foreach($data->plugins as $key => $pluginObj)
{
	if(method_exists($data->plugins[$key],'theme_header'))
	{
		call_user_func_array(array($data->plugins[$key],'theme_header'),array($data));
	}
}
echo '
<title>
	',(
		empty($data->output['pageTitle']) ? '' : $data->output['pageTitle'].' '
	),'Control Panel - ',$data->settings['siteTitle'],'
</title>
</head><body>
<div id="pageWrapper">
	<h1>Control Panel - <a href="',$data->domainName,$data->linkRoot,'">',$data->settings['siteTitle'],'</a></h1>';
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
	src="',$data->linkRoot,'admin/admin.js"
></script>
</body></html>';
}
function theme_leftSideBar($data) {
	echo '
	<!-- #content,#contentWrapper --></div></div>';
	if ($data->user['userLevel']>=USERLEVEL_BLOGGER) {
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
			$x = 0;
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
				// Is It The Last One??
				if(($x+1) > count($data->admin['menu']) || @$data->admin['menu'][$x + 1]['category'] !== $menuItem['category'])
				{
					$class = ($current) ? 'current last' : 'last';
				} else {
					$class = ($current) ? 'current' : '';
				}
				echo '
			<li class="',$class,'">
				<a href="',$data->linkRoot,'admin/',$menuItem['command'],'">
					',$menuItem['name'],($current ? ' >>' : ''),'
				</a>
			</li>';
			
			$x++;
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
function theme_rightSideBar($data) {
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
		<form method="post" action="',$formData->action,'" class="adminForm" enctype="'.$formData->enctype.'">';
	if ($formData->error) {
		echo '
			<div class="errorBox">',$formData->errorText,'</div>';
	}
	echo '
			<table class="adminFormTable" id="adminFormTable">',(
				isset($formData->caption) ? '
				<caption><span>'.$formData->caption.'</span></caption>' :
				''
			);
			
	$priorGroup = NULL;
	
	foreach ($formData->fields as $formDataKey => $formField)
	{
		/* 
		 *	Grouping
		**/
		if(isset($formField['group']) && $formField['group'] !== $priorGroup)
		{
			echo '
			<tr class="groupHeading">
				<th colspan="3"><span>',$formField['group'],'</span></th>
			</tr>';
			
			$priorGroup = $formField['group'];
		}
		
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
					</td><td class="fields" id="cell_'.$formData->formPrefix.$formDataKey.'">
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
						echo $formField['addEditor'];
						//$data->jsEditor->addEditor($formData->formPrefix.$formDataKey);
						/*echo '
<script type="text/javascript">
	CKEDITOR.replace(\'',$formData->formPrefix,$formDataKey,'\', {
		customConfig:CMSBasePath+"ckeditor/paladin/config.js"
	});
</script>';*/
					}
				break;
				case 'select':
					echo '
						>';
					$optgroup = FALSE;
					foreach ($formField['options'] as $key => $option) {
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
							<option',(
								($formField['value']==$option['value']) ?
								' selected="selected"' :
								''
							),' value="',$option['value'],'">',$option['text'],'</option>';
							if(!isset($formField['options'][$key+1]) && $optgroup)
							{
								echo '</optgroup>';
							}
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
			// Content After/
			if(!empty($formField['contentAfter']))
			{
				echo $formField['contentAfter'];
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

function theme_databaseSaveError() {
	echo 'There was an error in saving to the database.';
}

function theme_rejectError($data) {
	echo '<h2>',$data->output['rejectError'],'</h2>',$data->output['rejectText'];
}

function theme_accessDenied($login=false) {
	echo '
      <h2>Access Denied</h2>';
	  
	if( $login )
		echo '<p>You must be logged in to access the administration panel.</p>';
	else
		echo '<p>You do not have sufficient user rights to access the administration panel.</p>';
}

function theme_fatalError($message) {
      echo '
      <h2>Fatal Error</h2>
      <p>',$message,'</p>';
}
?>