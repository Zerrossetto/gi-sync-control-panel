<?php
/**
 * Creates GISync control panel
 *
 * @since 0.1
 *
 * @package GISync_cp
 */
 class GISync_CP_Plugin {

     const TABLE_PREFIX = 'gisync_';

     public static function activate() {

         // check if tables exist
         write_log( 'activation in progress' );
     }

     public static function deactivate() {

         write_log( 'deactivation in progress' );
     }

     public static function uninstall() {

         // delete existing tables
         write_log( 'uninstall in progress' );
     }

     public static function add_hooks() {
         register_activation_hook( GISYNCCP_FILE, array( 'GISync_CP_Plugin', 'activate') );
         register_deactivation_hook( GISYNCCP_FILE, array( 'GISync_CP_Plugin', 'deactivate') );
         register_uninstall_hook( GISYNCCP_FILE, array( 'GISync_CP_Plugin', 'uninstall') );
     }
 }
