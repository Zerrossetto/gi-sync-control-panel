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

if (!defined( 'ABSPATH' ) || !function_exists( 'add_action' )) {
    exit; // exit when accessed directly or outside plugin scope
}

// Define GISYNCCP_FILE
if (!defined( 'GISYNCCP_FILE' )) {
    define( 'GISYNCCP_FILE', __FILE__  );
}

(function () {

    // PHP-FIG PSR-4 specification compliant autoloader
    spl_autoload_register( function ($class) {
        if (class_exists( $class )) {
            return;
        }
        $prefix = 'GISyncCP\\';
        $len = strlen( $prefix );
        if (strncmp( $prefix, $class, $len ) !== 0) {
            return;
        }
        $class = strtolower( substr( $class, $len ) );
        $path = array_merge(
            preg_split( '#/#', plugin_dir_path( __FILE__ ), -1, PREG_SPLIT_NO_EMPTY ),
            array( 'includes' ),
            preg_split( '#\\\\#', $class, -1, PREG_SPLIT_NO_EMPTY )
        );
        $last = count( $path ) - 1;
        $path[ $last ] = 'class.gisync-cp-' . $path[ $last ] . '.php';
        $file = '/' . implode( '/', $path );
        if (file_exists( $file )) {
            require_once $file;
        }
    } );

    $gisync_cp_plugin = new GISyncCP\Plugin( GiSyncCp\Plugin::WITH_HOOKS_BINDING );
})();
