<?php

function languages_plugins_admin_en_us(){
	return array(
		'core' => array(
			'plugins'                        => 'Plugins'
		),
		'installerOutput'                  => 'Installer Output',
		'installSuccess'                   => 'Install Success!',
		'returnToPlugins'                  => 'Return To Plugins',
		'changesWereSaved'                 => 'The changed were saved.',
		'please'                           => 'Please',
		'clickHere'                        => 'click here',
		'toReturnToThePlugins'             => 'to return to the plugins.',
		'success'                          => 'Success!',
		'pluginEnabled'                    => 'Plugin successfully enabled!',
		'pluginEnabledSuccessMessage'      => 'You have successfully disabled the plugin. 
		The data stored by this plugin are still in the database, but the 
		plugin is disabled from user access. If you would like to remove all 
		of the data stored by this plugin from the database then click 
		uninstall. But beware, this data will be gone forever.',
	    'uninstallPlugin'                  => 'Uninstall Plugin',
	    'returnToTheListOfPlugins'         => 'Return to the List of Plugins',
	    'pluginSuccessfullyUninstalled'    => 'Plugin successfully uninstalled!',
	    'pluginSuccessfullyDisabled'       => 'Plugin successfully disabled!',
	    'save'                             => 'Save',
	    'modifyingPlugin'                  => 'Modifying Plugin',
	    'insuficientPrameters'             => 'insufficient parameters',
	    'noPluginNameEntered'              => 'No plugin name specified',
	    'improperInstallFile'              => 'Improper installation file',
	    'uninstallFunctionNotFound'        => 'The plugin uninstall function could not be found within the plugin installation file.',
	    'pluginNotFound'                   => 'The plugin you specified could not be found.',
	    'coreSiteSensePluginError'         => 'This plugin is auto-loaded by SiteSense for core functionality.'
	);
}
?>