<?php
function modules_admin_common_runUpgrader($data,$db,$to,$from,$shortName,$file) {
	common_include('modules/' . $update['shortName'] . '/updaters/' . $file);
	$functionName = $shortName . '_admin_updater_';
	$functionName .= preg_replace('/\D/', '', $from);
	
	$functionName .= preg_replace('/\D/', '', $to);
	if (function_exists()) {} // TODO


}
function modules_admin_common_getUpgradePath($to,$from,$shortName,$updaters) { // if ANYBODY knows how to calculate upgrade paths faster, stronger or better, send some code our way!
	$nu = array();
	foreach ($updaters as $i=>$updater) {
		$updater = basename($updater);
		if (fnmatch($shortName . '.updater.*to*.php',$updater)) {
			$updaterParts = substr($updater,strlen($shortName . '.updater.'));
			$updaterParts = substr($updaterParts,0,strlen($updaterParts)-4);
			$updaterParts = explode('to',$updaterParts);
			$nu[$i] = array(
				'from'=>$updaterParts[0],
				'to'=>$updaterParts[1],
				'file'=>$updater
			);
			if (version_compare($nu[$i]['to'],$to,'>')||version_compare($nu[$i]['from'],$from,'<')) {
				unset($nu[$i]); // this updater won't help us out, don't bother with it any more
				continue;
			}
		}
	}
	$paths = array();
	foreach ($nu as $i=>$n) {
		if ($n['from']==$from) { // we're going to use every one which begins the process as a "seed"
			$paths[$i][] = $n; // seeding
			$lastStep = end($paths[$i]);
			$j = 0; // prevent infinite-looping without doing more looping
			do {
				foreach ($nu as $u) {
					if ($lastStep['to']==$u['from']) {
						$paths[$i][] = $u;
						break;
					}
				}
				$lastStep = $u;
				$j++;
			} while ($lastStep['to']!=$to&&$j<200); // by the time we've reached 200 iterations we might have big issues
			$valid = TRUE;
			foreach ($paths[$i] as $step) {
				if (isset($prevStep)) {
					if ($prevStep['to']!=$step['from']) {
						$valid = FALSE;
						break;
					}
				}
				$prevStep = $step;
			}
			if (!$valid) {
				unset($paths[$i]);
			}
		}
	}
	if (count($paths)==0) {
		return FALSE; // we didn't get any valid paths ;-;
	} else { // give them a path to use to upgrade with! we're done, 70 revisions and hours of work later!
		return end($paths);
	}
}