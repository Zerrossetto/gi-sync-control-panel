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
         Plugin::debug( 'begin' );

         $key = Plugin::PREFIX . '_version';
         $installed_version = get_option( $key );
         Plugin::debug( 'plugin version ' .  $installed_version ?: 'fist install' );

         if ( strcmp( $installed_version, Plugin::VERSION ) < 0 ) {
            Plugin::debug( 'Starting plugin structure upgrade' );
            //add_option( $key, Plugin::VERSION );
        }
         // check if tables exist
         Plugin::debug( 'end' );
     }

     public static function deactivation() {

         Plugin::debug( 'begin' );
         //technically should do nothing
         Plugin::debug( 'end' );
     }

     public static function uninstall() {
         Plugin::debug( 'begin' );
         // delete existing tables
         Plugin::debug( 'end' );
     }

     public function settings_panel() {
        Plugin::debug( 'begin' );

        $key = $this->setting( 'test_options' );

        register_setting('reading', $key);
        add_settings_section(
            Plugin::PREFIX . '_settings_section',
            'Gi Sync Settings Section',
            function () {
                echo '<p>GISync Panel Section Introduction.</p>';
            },
            'reading'
        );
        add_settings_field(
            Plugin::PREFIX . '_settings_field',
            'GI Sync Setting',
            function () {
                $setting = get_option( $key ) ?: '';
                echo '<input type="text" name="'.$key.'" value="'.esc_attr($setting).'"s/>';
            },
            'reading',
            Plugin::PREFIX . '_settings_section'
        );

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

     private static function setting( $setting_name ) {
         return Plugin::PREFIX . '_' . $setting_name;
     }
 }
