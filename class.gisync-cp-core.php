<?php
/**
 * Creates GISync control panel
 *
 * @since 0.1
 *
 * @package GISync_cp
 */
 class GISync_CP_Plugin{

     const TABLE_PREFIX = 'gisync_'

     public static function activate() {

         // check if tables exist

     }

     public static function deactivate() {

         // check if tables exist

     }

     public static function uninstall() {

         // delete existing tables

     }

     public static function add_hooks() {
         register_activation_hook( __FILE__, array( 'GISync_Control_Panel', 'activate' ) );
         register_deactivation_hook( __FILE__, array( 'GISync_Control_Panel', 'deactivate' ) );
         register_uninstall_hook( __FILE__, array( 'GISync_Control_Panel', 'uninstall' )
     }
 }
