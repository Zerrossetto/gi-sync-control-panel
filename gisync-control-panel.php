<?php
/**
 * @package GISync_Control_Panel
 * @version 0.1
 */
/*
 * Plugin Name: GISync Control Panel
 * Plugin URI: http://blackwhitestudio.it/
 * Description: Control and monitor syncronization job for gestionale immobiliare
 * Author: Black & White Studio
 * Version: 0.1
 * Author URI: http://zerrosset.to/
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_action' ) ) {
	exit; // exit when accessed directly or outside plugin scope
}

// Define GISYNCCP_FILE
if ( !defined( 'GISYNCCP_FILE' ) ) {
	define( 'GISYNCCP_FILE', __FILE__  );
}

// Define GISYNCCP_DIR
if ( !defined( 'GISYNCCP_DIR' ) ) {
	define( 'GISYNCCP_DIR', plugin_dir_path( __FILE__ )  );
}

(function () {

	// PHP-FIG PSR-4 specification compliant autoloader
	spl_autoload_register(function ($class) {
    	$prefix = 'GISyncCP\\';
    	$base_dir = GISYNCCP_DIR . 'includes/';
    	$len = strlen($prefix);
    	if (strncmp($prefix, $class, $len) !== 0) return;
    	$relative_class =  strtolower( substr($class, $len) );
    	$file = sprintf(
			'%sclass.gisync-cp-%s.php',
			$base_dir,
			str_replace('\\', '/', $relative_class )
		);
	    if (file_exists($file)) require_once $file;
	});

    $plugin = new GISyncCP\Plugin();
    $plugin->bind_hooks();
})();
