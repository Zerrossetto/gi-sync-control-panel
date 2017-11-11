<?php
/**
 * Creates GISync control panel
 *
 * @since 0.1
 *
 * @package GISync_cp
 */
 namespace GISyncCP;

 class Plugin {
     use Utils\Logging;

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

         $key = Plugin::PREFIX . '_version';
         $installed_version = get_option( $key );
         Plugin::debug( 'plugin version ' .  $installed_version ?: 'fist install' );

         if ( strcmp( $installed_version, Plugin::VERSION ) < 0 ) {
            Plugin::debug( 'Starting plugin structure upgrade' );
            //add_option( $key, Plugin::VERSION );
        }
     }

     public static function deactivation() {
         //technically should do nothing
     }

     public static function uninstall() {
         // delete existing tables
     }

     public function settings_panel() {

         $settings = new SettingsModel(
             plugin_dir_path( GISYNCCP_FILE ).'includes/data/setting-fields.yaml'
         );
         $settings->generate_page();

        Plugin::debug( 'end' );
     }

     public function admin_menu() {
         add_management_page(
             'GI Sync',
             'GI Sync',
             'manage_options',
             plugin_dir_path( GISYNCCP_FILE ) . '/admin/settings.php',
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
        add_action( 'admin_init', array( $this, 'settings_panel') );
     }

     private static function bind_hook_internal( $hook ) {
         $hook_function = 'register_'. $hook .'_hook';
         $hook_function( GISYNCCP_FILE, array( __CLASS__, $hook ) );
     }

     public static function prefix( $setting_name ) {
         return Plugin::PREFIX . '_' . $setting_name;
     }
 }
