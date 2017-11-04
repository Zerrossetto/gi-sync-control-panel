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

// Define GISYNCCP_DIR.
if ( !defined( 'GISYNCCP_DIR' ) ) {
	define( 'GISYNCCP_DIR', plugin_dir_path( __FILE__ )  );
}

require_once( GISYNCCP_DIR . 'class.gisync-cp-core.php' );
GISync_CP_Code::add_hooks();
