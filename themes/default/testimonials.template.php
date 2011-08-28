<?php

function theme_testimonialHeader() {
	theme_contentBoxHeader('Testimonials');

	echo '
						<div id="testimonials">
							<p>Below are the various testimonials received from my past clients:</p>';
}

function theme_testimonialItem($testimonial,$key,$last) {
		echo '
							<blockquote',(($key==$last) ? ' class="last"' : ''),'
								cite="',$testimonial['author'],'"
								id="testimonial_',$testimonial['id'],'"
							>
								&quot;',$testimonial['quote'],'&quot;
								<p class="cite">
									-- <cite>',$testimonial['author'],'</cite>
									',$testimonial['authorTitle'],(
										!empty($testimonial['siteName']) ? '<br />'.(
											(
												!empty($testimonial['siteURL']) ?
												'<a href="'.$testimonial['siteURL'].'">' :
												''
											).$testimonial['siteName'].(
												!empty($testimonial['siteURL']) ? '</a>' : ''
											)
										) : ''
									),'
								</p>
							</blockquote>';
}

function theme_testimonialFooter() {
	echo '
						<!-- #testimonials --></div>';
	theme_contentBoxFooter();
}

?>