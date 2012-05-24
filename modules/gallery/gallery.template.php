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
function theme_galleryImageView($data){
	theme_contentBoxHeader('Album Info');
	echo '
				<ul>
					<li><strong><a href="', $data->output['galleryHome'], 'album/view/', $data->output['album']['shortName'], '">', $data->output['album']['name'], '</a></strong></li>
					<li>Created by <strong>', $data->output['user']['name'], '</strong></li>
				</ul>
		';
	theme_contentBoxFooter();
	theme_contentBoxHeader($data->output['image']['name']);
	echo '
		<img src="', $data->linkRoot, 'images/gallery/pictures/', $data->output['image']['image'], '" alt="', $data->output['image']['name'], '" />
	';
	theme_contentBoxFooter();
	if(isset($data->user['id']) && $data->output['album']['user'] == $data->user['id']){
		theme_contentBoxHeader('Control Panel');
		echo '
				<ul>
					<li>
						<a href="', $data->output['galleryHome'], 'image/delete/', $data->output['album']['shortName'], '/', $data->output['image']['shortName'], '">
							Delete Image
						</a>
					</li>
					<li>
						<a href="', $data->output['galleryHome'], 'image/edit/', $data->output['album']['shortName'], '/', $data->output['image']['shortName'], '">
							Edit Image
						</a>
					</li>
				</ul>
			';
		theme_contentBoxFooter();
	}
	if(!empty($data->output['comments'])){
		theme_contentBoxHeader('Comments');
		echo '<ol class="imageComments">';
		foreach($data->output['comments'] as $Comment){
			echo '
				<li>
					<blockquote>
						',$Comment['content'],'
						<cite>
							Posted by ',$Comment['user_name'],' on ', date('F j, Y \a\t g:i a', strtotime($Comment['time'])),'
						</cite>
					</blockquote>
				</li>
			';
		}
		echo '</ol>';
		theme_contentBoxFooter();
	}
	theme_buildForm($data->output['commentForm']);
}
function theme_galleryImageDelete($data){
	theme_contentBoxHeader('Delete Image?');
	echo '
			<p>You are about to delete the following image. This is a process which cannot be undone.</p>
			<p>If you are sure that you want to delete this image, click <a href="', $data->output['galleryHome'] , 'image/delete/', $data->output['album']['shortName'], '/', $data->output['image']['shortName'], '/confirm">here</a>.</p>
			<p>To return to the image page, click <a href="', $data->output['galleryHome'], 'image/view/', $data->output['album']['shortName'], $data->output['image']['shortName'], '">here</a>.</p>
			<img src="', $data->linkRoot, 'images/gallery/pictures/', $data->output['image']['image'], '" alt="', $data->output['image']['name'], '" />
		';
	theme_contentBoxFooter();
}
function theme_galleryAlbumView($data){
	theme_contentBoxHeader('Album Info');
	echo '
			<ul>
				<li><strong>', $data->output['album']['name'], '</strong></li>
				<li>Created by <strong>', $data->output['user']['name'], '</strong></li>
				<li>Images: <strong>', count($data->output['images']), '</strong></li>
			</ul>
	';
	theme_contentBoxFooter();
	if(count($data->output['images']) > 0){
		theme_contentBoxHeader('Images');
		echo '
			<ul>
		';
		foreach($data->output['images'] as $image){
			echo '
				<li>
					<p><a href="', $data->output['galleryHome'], 'image/view/', $data->output['album']['shortName'], '/', $image['shortName'], '">', $image['name'], '</a></p>
					<img src="', $data->linkRoot, 'images/gallery/thumbs/', $image['thumb'], '" />
					<p>Submitted: ', date('d/m/Y', strtotime($image['time'])), '</p>
				</li>
			';
		}
		echo '
			</ul>
		';
		theme_contentBoxFooter();
	}
	if(isset($data->user['id']) && $data->output['album']['user'] == $data->user['id']){
		theme_contentBoxHeader('Control Panel');
		echo '
			<ul>
				<li>
					<a href="', $data->output['galleryHome'], 'image/add/', $data->output['album']['shortName'], '">
						Add Image
					</a>
				</li>
				<li>
					<a href="', $data->output['galleryHome'], 'album/edit/', $data->output['album']['shortName'], '">
						Edit Album
					</a>
				</li>
				<li>
					<a href="', $data->output['galleryHome'], 'album/delete/', $data->output['album']['shortName'], '">
						Delete Album
					</a>
				</li>
			</ul>
		';
		theme_contentBoxFooter();
	}
}
function theme_galleryImageAdd($data){
	theme_buildForm($data->output['form']);
}
function theme_galleryImageEdit($data){
	echo '
		<img src="', $data->linkRoot, 'images/gallery/thumbs/', $data->output['image']['thumb'], '" />
	';
	theme_buildForm($data->output['form']);
}
function theme_galleryAlbumAdd($data){
	theme_buildForm($data->output['form']);
}
function theme_galleryAlbumEdit($data){
	theme_buildForm($data->output['form']);
}
function theme_galleryAlbumDelete($data){
	theme_contentBoxHeader('Delete Album?');
	echo '
		<p>You are about to delete an album. This is a process which cannot be undone.</p>
		<p>If you are sure that you want to delete this album, click <a href="', $data->output['galleryHome'], 'album/delete/', $data->output['album']['shortName'], '/confirm">here</a>.</p>
		<p>To return to viewing the album, click <a href="', $data->output['galleryHome'], 'album/view/', $data->output['album']['shortName'], '">here</a>.</p>
	';
	theme_contentBoxFooter();
}
function theme_galleryNotFound($data){
	theme_contentBoxHeader('Not Found');
	echo '<p>The page you are looking for has not been found.';
	theme_contentBoxFooter();
}
function theme_galleryAccessDenied($data){
	theme_contentBoxHeader('Access Denied');
	echo '<p>You do not have permission to access this resource.</p>';
	theme_contentBoxFooter();
}
function theme_galleryDefault($data){
	theme_contentBoxHeader('Gallery Home');
	echo '<ul>';
	foreach($data->output['albums'] as $album){
		echo '
			<li>
				<p><a href="', $data->output['galleryHome'], 'album/view/', $album['shortName'], '">', $album['name'], '</a></p>
			</li>
		';
	}
	echo '</ul>';
	echo '<p>Do you want to <a href="', $data->output['galleryHome'], 'album/add">add an album</a>?</p>';
	theme_contentBoxFooter();
}