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

     const WITH_HOOKS_BINDING = TRUE;

     const MINIMAL_PHP_VERSION = '5.6.0';

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
     public function __construct( $do_hooks_binding = FALSE ) {

         $this->version = Plugin::VERSION;

         if ( $do_hooks_binding )
            $this->bind_hooks();
     }

     public static function activation() {

         if ( version_compare( PHP_VERSION, MINIMAL_PHP_VERSION ) < 0 )
            wp_die(
                'This plugin requires at least PHP '.MINIMAL_PHP_VERSION.' version',  //message
                'PHP version compatibility check failed',  // title
                array( 'back_link' => TRUE )  //arguments
            );

         $key = self::prefix( 'version' );
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
             plugin_dir_path( GISYNCCP_FILE ).'includes/data/setting-fields.yaml',
             SettingsModel::START_GENERATE_PAGE
         );
         $this->model = $settings->data;
     }

     public function admin_menu() {
         wp_register_style(
             $this->prefix( 'settings_panel' ),
             plugin_dir_url(GISYNCCP_FILE) . 'admin/css/gisync-control-panel.css'
         );
         add_management_page(
             'GI Sync',
             'GI Sync',
             'manage_options',
             plugin_dir_path( GISYNCCP_FILE ) . '/admin/settings.php',
             null
         );
     }

     public function enqueue_scripts () {
         wp_enqueue_style( $this->prefix( 'settings_panel' ) );
     }

     public function bind_hooks() {

         if ( is_admin() ) {
            $hooks = array( 'activation', 'deactivation', 'uninstall' );
            foreach ( $hooks as &$hook )
                Plugin::bind_hook_internal( $hook );
        }

        add_action( 'admin_menu', array( $this, 'admin_menu') );
        add_action( 'admin_init', array( $this, 'settings_panel') );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
     }

     private static function bind_hook_internal( $hook ) {
         $hook_function = 'register_'. $hook .'_hook';
         $hook_function( GISYNCCP_FILE, array( __CLASS__, $hook ) );
     }

     public static function prefix( $setting_name ) {
         return self::PREFIX . '_' . $setting_name;
     }
 }
