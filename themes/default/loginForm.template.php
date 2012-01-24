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
function theme_loginForm($data) {
	if ($data->user['userLevel']==0) {
		echo '
			<form action="',$data->linkRoot,'login"
				method="post"
				class="bigLoginForm"
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
	} else {
		if (
			($_POST['lastPage']=='login') ||
			($_POST['lastPage']=='logout')
		) {
			echo '
			<p>You have successfully logged in.</p>';
		} else {
			echo '
			<p>You are already logged in as ',$data->user['name'],'</p>';
		}
		echo '
			<p><a href="',$data->linkRoot,'logout">Log Out</a></p>';
	}
}
?>