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

// write_log facility
if ( !function_exists( 'write_log' ) ) {
   function write_log ( $log )  {
      if ( !WP_DEBUG ) {
		  // does nothing at all
	  } else if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
	 } else {
         error_log( $log );
	 }
   }
}

require_once( GISYNCCP_DIR . '/includes/class.gisync-cp-core.php' );
GISync_CP_Plugin::add_hooks();
