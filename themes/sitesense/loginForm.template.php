<?php
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