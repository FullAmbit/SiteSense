<?php

function gzip_start() {
	ob_end_clean();
	ob_start();
	ob_implicit_flush(0);
}

function gzip_end($data) {
  $contents=ob_get_contents();
  ob_end_clean();
  header('Content-Encoding: '.$data->compressionType);
  print("\x1F\x8B\x08\x00\x00\x00\x00\x00");
	$size=strlen($contents);
	$crc=crc32($contents);
	$contents=gzcompress($contents,$data->settings['compressionLevel']);
 	/* strip off faulty 4 digit CRC when printing */
  print(substr($contents,0,strlen($contents)-4));
}

?>