<?php
/**
 * Creates GISync control panel
 *
 * @since 0.1
 *
 * @package GISync_cp
 */
 namespace GISyncCP;

 use GISyncCP\Utils as utils;

 class Plugin {

     const PREFIX = 'gisync_cp';

     const VERSION = '0.0.1';

     /**
      * The current version of the plugin.
      *
      * @since    1.0.0
      *
      * @var string The current version of the plugin.
      */
     protected $version;

     /**
      * Define the core functionality of the plugin.
      *
      * Set the plugin name and the plugin version that can be used throughout the plugin.
      * Load the dependencies, define the locale, and set the hooks for the admin area and
      * the public-facing side of the site.
      *
      * @since    1.0.0
      */
     public function __construct() {

         $this->version = Plugin::VERSION;
     }

     public static function activation() {
         utils::write_log( 'begin' );

         $key = Plugin::PREFIX . '_version';
         $installed_version = get_option( $key );
         utils::write_log( 'plugin version ' .  $installed_version ?: 'fist install' );

         if ( strcmp( $installed_version, Plugin::VERSION ) < 0 ) {
            utils::write_log( 'Starting plugin structure upgrade' );
            //add_option( $key, Plugin::VERSION );
        }
         //add_option( $key, $this->version );
         /*register_setting(
             Plugin::PREFIX,
             Plugin::PREFIX . '_options'
         );*/
         // check if tables exist
         utils::write_log( 'end' );
     }

     public static function deactivation() {

         utils::write_log( 'begin' );
         //technically should do nothing
         utils::write_log( 'end' );
     }

     public static function uninstall() {
         utils::write_log( 'begin' );
         // delete existing tables
         utils::write_log( 'end' );
     }

     public function admin_menu() {
         add_management_page(
             'GI Sync',
             'GI Sync',
             'manage_options',
             GISYNCCP_DIR . '/admin/settings.php',
             null
         );
     }

     public function bind_hooks() {

         if ( is_admin() ) {
            $hooks = array( 'activation', 'deactivation', 'uninstall' );
            foreach ( $hooks as &$hook )
                Plugin::bind_hook_internal( $hook );
        }

        add_action( 'admin_menu', array( $this, 'admin_menu') );
     }

     private static function bind_hook_internal( $hook ) {
         $hook_function = 'register_'. $hook .'_hook';
         $hook_function( GISYNCCP_FILE, array( __CLASS__, $hook ) );
     }
 }
