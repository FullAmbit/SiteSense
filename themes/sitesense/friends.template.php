<?php
function theme_friendList($data){
	theme_buildForm($data->output['friendsearch']);
	theme_contentBoxHeader("Your Friends");
	if(count($data->output['friends']) > 0){
		echo '<ol class="friendList">';
		foreach($data->output['friends'] as $friend){
			$fullname = '';
			if(strlen($friend['fullName']) > 0){
				$fullName = ' (' . $friend['fullName'] . ')';
			} 
			echo '
			<li>
				<a href="', $data->linkRoot, 'users/', $friend['name'], '">', $friend['name'], $fullName, '</a>
			</li>
			';
		}
		echo '</ol>';
	}else{
		echo "<p>You do not have any friends.</p>";
	}
	theme_contentBoxFooter();	
}
function theme_searchResults($data){
	theme_buildForm($data->output['friendsearch']);
	theme_contentBoxHeader('Search Results For "' . $data->output['search'] . '"');
	$results = $data->output['results'];
	if(count($results) == 0){
		echo '<p>No results found</p>';
	}else{
		echo '<ol>';
		foreach($data->output['results'] as $result){
			echo '
				<li>
					', empty($result['icon']) ? '' : ('<img src="' . $data->linkRoot . 'gallery/icons/' . $result['icon'] . '" />'), '
					<a href="', $data->linkRoot, 'users/', $result['name'], '">', $result['name'], '</a>
				</li>
			';
		}
		echo '</ol>';
	}
	theme_contentBoxFooter();
}
function theme_viewRequests($data){
	theme_contentBoxHeader('Friend Requests');
	if(count($data->output['requests']) == 0){
		echo '<p>You dont have any friend requests</p>';
	}else{
		echo '
			<table>
				<tr>
					<th>User</th>
					<th>Controls</th>
				</tr>';
		foreach($data->output['requests'] as $request){
			if(strlen($request['fullName']) > 0){
				$fullname = ' (' . $request['fullName'] . ')';
			}else{
				$fullname = '';
			}
			echo '
				<tr>
					<td>
						<a href="', $data->linkRoot, 'users/', $request['name'], '">', $request['name'], $fullname, '</a>
					</td>
					<td>
						<a href="', $data->output['friendsHome'] . 'requests/accept/', $request['name'], '">Accept</a> | <a href="', $data->output['friendsHome'], 'requests/ignore/', $request['name'], '">Ignore</a>
					</td>
				</tr>
			';
		}
		echo '
			</table>
		';
	}
}