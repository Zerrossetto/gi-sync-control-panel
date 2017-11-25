<?php
/**
 * Creates GISync control panel
 *
 * @since 0.1
 *
 * @package GISync_cp
 */
 namespace GISyncCP;

class Plugin
{
    use Utils\Logging;
    use Utils\NavigationMixins;
    use Utils\FileSystemMixins;

    const PREFIX = 'gisync_cp';

    const VERSION = '0.0.1';

    const WITH_HOOKS_BINDING = true;

    const MIN_PHP_VERSION = '5.6.0';

    /**
      * The current version of the plugin.
      *
      * @since    1.0.0
      *
      * @var string The current version of the plugin.
      */
    protected $version;

    /**
     * An instance of type *ViewModel
     *
     * @since 1.0.0.
     *
     * @var stdClass The view model instance for the setting page
     */
    public $model;

    /**
      * Define the core functionality of the plugin.
      *
      * Set the plugin name and the plugin version that can be used throughout the plugin.
      * Load the dependencies, define the locale, and set the hooks for the admin area and
      * the public-facing side of the site.
      *
      * @since    1.0.0
      */
    public function __construct($do_hooks_binding = false)
    {

        $this->version = Plugin::VERSION;

        if ($do_hooks_binding) {
            $this->bind_hooks();
        }
    }

    public static function activation()
    {

        if (version_compare( PHP_VERSION, self::MIN_PHP_VERSION ) < 0) {
            trigger_error(
            'This plugin requires at least PHP '.self::MIN_PHP_VERSION.' version',
            E_USER_ERROR
            );
        }


        if (! function_exists( 'yaml_parse_file' )) {
            trigger_error(
            'Cannot activate plugin. Yaml module not present',
            E_USER_ERROR
            );
        }

        $key = self::prefix( 'version' );
        $installed_version = get_option( $key );
        Plugin::debug( 'plugin version ' .  $installed_version ?: 'fist install' );

        if (strcmp( $installed_version, Plugin::VERSION ) < 0) {
            Plugin::debug( 'Starting plugin structure upgrade' );
            //add_option( $key, Plugin::VERSION );
        }
    }

    public static function deactivation()
    {
        //technically should do nothing
    }

    public static function uninstall()
    {
        // delete existing tables but, HEY! THERE AREN'T!!1!
    }

    public function settings_panel()
    {
        $this->current_tab = self::current_tab();

        switch ($this->current_tab) {
            case 'general':
                $this->model = new GeneralSettingsViewModel(
                self::yaml( 'general-settings-fields' ),
                GeneralSettingsViewModel::START_GENERATE_PAGE
                );
                break;
            case 'agency':
                $this->model = new AgencySettingsViewModel(
                  self::yaml( 'agency-settings-fields' ),
                  AgencySettingsViewModel::START_GENERATE_PAGE
                );
                break;
            default:
                wp_die(
                'Invalid tab selector',
                'Unexpected error',
                array( 'back_link' => true )
                );
        }

        require_once( self::page_path( 'settings' ) );
    }

    public function admin_menu()
    {
        wp_register_style(
          self::prefix( 'settings_panel' ),
          self::stylesheet( 'gisync-control-panel' )
        );
        add_management_page(
            'GI Sync',
            'GI Sync',
            'manage_options',
            $this->prefix( 'settings' ),
            array( $this, 'settings_panel' )
        );
    }

    public function enqueue_scripts()
    {
        $screen = get_current_screen();
        if (property_exists($screen, 'id') && $screen->id === 'tools_page_gisync_cp_settings') {
            wp_enqueue_style( self::prefix( 'settings_panel' ) );
        }
    }

    public function rest_endpoint()
    {
        $namespace = self::prefix( 'v1', '/' );
        register_rest_route(
            $namespace,
            '/settings',
            array(
                'methods' => 'GET',
                'callback' => array( 'GISyncCP\Rest', 'all_settings' )
            )
        );
        register_rest_route(
            $namespace,
            '/agency/(?P<sequence>\d+)',
            array(
                'methods' => 'GET',
                'callback' => array( 'GISyncCP\Rest', 'agency_settings' )
            )
        );
    }

    public function whitelist_custom_options($whitelist)
    {
        $models = array (
          new GeneralSettingsViewModel( self::yaml( 'general-settings-fields' ) ),
          new AgencySettingsViewModel( self::yaml( 'agency-settings-fields' ) )
        );

        foreach ($models as &$model) {
            $option = $model->data[ 'option_name' ];
            $prefix_present = array_key_exists( $option, $whitelist );
            if ($prefix_present && !in_array( $option, $whitelist[ $option ] )) {
                array_push( $whitelist[ $option ], $option );
            } elseif (!$prefix_present) {
                $whitelist[ $option ] = array( $option );
            }
        }

        return $whitelist;
    }

    public function bind_hooks()
    {

        if (is_admin()) {
            foreach (array( 'activation', 'deactivation', 'uninstall' ) as &$hook) {
                Plugin::bind_hook_internal( $hook );
            }
        }

        add_action( 'admin_menu', array( $this, 'admin_menu') );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'rest_api_init', array( $this, 'rest_endpoint' ) );
        add_action(
          'whitelist_options',
          array( $this, 'whitelist_custom_options' ),
          11
        );
    }

    private static function bind_hook_internal($hook)
    {
        $hook_function = 'register_'. $hook .'_hook';
        $hook_function( GISYNCCP_FILE, array( __CLASS__, $hook ) );
    }

    public static function prefix($setting_name, $sep = '_')
    {
        return self::PREFIX . $sep . $setting_name;
    }
}
