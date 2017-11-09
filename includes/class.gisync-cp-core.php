<?php
/**
 * Creates GISync control panel
 *
 * @since 0.1
 *
 * @package GISync_cp
 */
 class GISync_CP_Plugin {

     const PREFIX = 'gisync_cp';

     public static function activation() {

         // check if tables exist
         write_log( 'activation in progress' );
     }

     public static function deactivation() {

         write_log( 'deactivation in progress' );
     }

     public static function uninstall() {

         // delete existing tables
         write_log( 'uninstall in progress' );
     }

     public static function admin_menu() {
         add_management_page(
             'GI Sync',
             'GI Sync',
             'manage_options',
             GISYNCCP_DIR . '/admin/view.php',
             null
         );
     }

     public static function add_hooks() {

         if ( is_admin() ) {
             GISync_CP_Plugin::hook( 'activation' );
             GISync_CP_Plugin::hook( 'deactivation' );
             GISync_CP_Plugin::hook( 'uninstall' );
        }

        add_action( 'admin_menu', array( 'GISync_CP_Plugin', 'admin_menu') );
     }

     private static function hook( $hook ) {
         $hook_function = 'register_'. $hook .'_hook';
         $hook_function( GISYNCCP_FILE, array( 'GISync_CP_Plugin', $hook ) );
     }
 }
