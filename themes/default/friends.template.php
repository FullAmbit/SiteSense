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
    $x=empty($data->output['search'][':userName']);
    $y=empty($data->output['search'][':fullName']);
    $z=empty($data->output['search'][':publicEmail']);
    if(count($data->output['search']) > 1) {
        if(!empty($data->output['search'][':userName']) && !empty($data->output['search'][':fullName']) && !empty($data->output['search'][':publicEmail'])) {
            $header = 'Full Name: '.$data->output['search'][':fullName'].', User Name: '.$data->output['search'][':userName'].', Public Email: '.$data->output['search'][':publicEmail'];
            theme_contentBoxHeader('Search Results For "'.$header.'"');
        } elseif(!empty($data->output['search'][':userName']) && !empty($data->output['search'][':fullName'])) {
            $header = 'Full Name: '.$data->output['search'][':fullName'].', User Name: '.$data->output['search'][':userName'];
            theme_contentBoxHeader('Search Results For "'.$header.'"');
        } elseif(!empty($data->output['search'][':fullName']) && !empty($data->output['search'][':publicEmail'])) {
            $header = 'Full Name: '.$data->output['search'][':fullName'].', Public Email: '.$data->output['search'][':publicEmail'];
            theme_contentBoxHeader('Search Results For "'.$header.'"');
        } elseif(!empty($data->output['search'][':userName']) && !empty($data->output['search'][':publicEmail'])) {
            $header = 'User Name: '.$data->output['search'][':userName'].', Public Email: '.$data->output['search'][':publicEmail'];
            theme_contentBoxHeader('Search Results For "'.$header.'"');
        }

    } elseif(count($data->output['search']) == 1) {
        theme_contentBoxHeader('Search Results For "'.$data->output['search'].'"');
    } else {
        theme_contentBoxHeader('Please fill in at least one of the fields');
    }
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