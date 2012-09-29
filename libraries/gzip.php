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
function gzip_start() {
	$a = ob_get_clean();
	ob_start();	echo $a;
	ob_implicit_flush(0);
}
function gzip_end($data) {
  $contents=ob_get_contents();
  ob_end_clean();
  header('Content-Encoding: '.$data->compressionType);
  print("\x1F\x8B\x08\x00\x00\x00\x00\x00");
	if($data->settings['removeAttribution'] == '0') {
		$count = preg_match('/<p id="attribution">Powered by <a href="http:\/\/www.sitesense.org">SiteSense<\/a>&trade; '.$data->version.', a <a href="http:\/\/www.fullambit.com">Full Ambit Media<\/a> product.<\/p>/',$contents,$matches);
		if($count < 1){
			$fC = preg_match('/<!-- #footer -->/',$contents);
			if($fC < 1){
				$contents = preg_replace('/<\/body>/','<p id="attribution">Powered by <a href="http://www.sitesense.org">SiteSense</a>&trade; '.$data->version.', a <a href="http://www.fullambit.com">Full Ambit Media</a> product.</p></body>',$contents);
			}else{
				$contents = preg_replace('/<!-- #footer -->/','<p id="attribution">Powered by <a href="http://www.sitesense.org">SiteSense</a>&trade; '.$data->version.', a <a href="http://www.fullambit.com">Full Ambit Media</a> product.</p><!-- #footer -->',$contents);
			}
		}
	}
	$contents=gzencode($contents,$data->settings['compressionLevel'],FORCE_DEFLATE);	
	print($contents);
}
?>