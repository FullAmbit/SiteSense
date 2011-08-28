<?php

function admin_content($settings) {
	echo '
	<h2>Requested Action Error</h2>
	<p>
		You attempted to access the "',$settings->action[1],'" function, which does not exist in this system. If you believe this to be in error, please contact ___support info___.
	</p>';
}

?>