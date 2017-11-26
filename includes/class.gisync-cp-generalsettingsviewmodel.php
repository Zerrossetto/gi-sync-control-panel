<?php
namespace GISyncCP;

class GeneralSettingsViewModel
{
    use Utils\Logging;

    const START_GENERATE_PAGE = true;

    /**
      * The current version of the plugin.
      *
      * @since    1.0.0
      *
      * @var boolean True if the setting mapped by the model is blog-specifi
      *              if multisite, false if is a site-wide setting
      */
    protected $local_setting;

    /**
     *
     */
    function __construct($yaml, $do_generate_page = false)
    {

        if (!function_exists( 'yaml_parse_file' )) {
            return;
        }

        $this->data = yaml_parse_file( $yaml );

        if ($do_generate_page) {
            $this->generate_page();
        }

        $this->local_setting = false;

        add_filter(
         'pre_update_option_' . $this->data[ 'option_name'],
         array( $this, 'validate_input' ),
         10, // priority
         2   // number of args
        );
    }

/*
 * ========================================================================
 * ==                     PAGE GENERATION SECTION                        ==
 * ========================================================================
 */

    /**
     *
     */
    function generate_page()
    {

        if (array_key_exists( 'option_name', $this->data )) {
            register_setting(
                $this->data[ 'option_name' ], //option_group
                $this->data[ 'option_name' ],
                array(
                    'description' => $this->data[ 'description' ],
                    'sanitize_callback' => array( $this, 'sanitize_input' ),
                    'show_in_rest' => $this->data[ 'show_in_rest' ]
                )
            );
        }

        foreach ($this->data[ 'sections' ] as &$section) {
            $this->load_section( $section );
        }
    }

    /**
     *
     */
    protected function load_section(&$section)
    {

        if (array_key_exists( 'callback', $section )) {
            $callback = array( $this, $section[ 'callback' ] );
        } else {
            $callback = array( $this, 'section_callback' );
        }

        add_settings_section( $section[ 'id' ], $section[ 'title' ], $callback, $this->data[ 'page' ] );

        foreach ($section[ 'fields' ] as &$field) {
            $this->load_field( $section[ 'id'], $field );
        }
    }

    /**
     *
     */
    protected function load_field($section_ref, &$field)
    {
        if (array_key_exists( 'callback', $field )) {
            $callback = array( $this,  $field[ 'callback' ] );
        } else {
            $callback = array( $this, 'text_field_callback' );
        }

        add_settings_field(
            $field[ 'id' ],
            $field[ 'title' ],
            $callback,
            $this->data[ 'page' ],
            $section_ref,
            $field[ 'args' ]
        );
    }

    public function do_tabbed_navigation() {

      $tabs = new Utils\DOMBuilder();
      $tabs->tabsNavigationBar();
      $tab_data = array( 'general' => 'General Options', 'agency' => 'Agency Options');
      $base_url = menu_page_url( 'gisync_cp_settings', false );

      foreach ($tab_data as $id => $description) {
        $tabs->addTab( $id, $description, $base_url, self::current_tab() === $id );
      }

      echo $tabs->build();
    }

/*
 * ========================================================================
 * ==                        CALLBACKS SECTION                           ==
 * ========================================================================
 */
    public function section_subtitle_echo()
    {
        echo '<p>GISync Panel Section Introduction.</p>';
    }

    public function text_field_echo($args)
    {
        echo $this->prepare_input( $args )->build();
    }

    public function numeric_field_echo($args)
    {
        $args[ 'input_type' ] = 'number';
        echo $this->prepare_input( $args )->build();
    }

    /*
     * ========================================================================
     * ==               SANITIZATION AND VALIDATION SECTION                  ==
     * ========================================================================
     */
    public function sanitize_input($values)
    {
        $this->debug( 'sanitizing...', $values );
        return $values;
    }

    public function validate_input($new_values, $old_values)
    {
        $validator = new Utils\Validation( Plugin::prefix( 'messages' ) );
        $no_errors = true;

        array_walk_recursive(
            $new_values,
            function ($item, $key) use (&$new_values, &$validator, &$old_values, &$no_errors) {
                $invoke_target = array( $validator, 'invalid_' . $key );
                if (method_exists ( ...$invoke_target )) {
                    if ($error = call_user_func_array( $invoke_target, array( &$new_values ) )) {
                        $no_errors = false;
                        add_settings_error(...$error);
                        if (array_key_exists( $key, $old_values )) {
                            $new_values[ $key ] = $old_values[ $key ];
                        }
                    }
                }
            }
        );

        if ($no_errors) {
            $message = __( 'Settings saved', Plugin::PREFIX );
        } elseif ($new_values != $old_values) {
            $message = __( 'Settings partially saved', Plugin::PREFIX );
        }

        if ($values_persisted = isset( $message )) {
            add_settings_error(
                $validator->slug,
                Plugin::prefix( 'message' ),
                $message,
                'updated'
            );
        }

        return $new_values;
    }


/*
 * ========================================================================
 * ==                         UTILITIES SECTION                          ==
 * ========================================================================
 */
    protected function prepare_input(&$args)
    {
        $tag = new Utils\DOMBuilder();
        $values = $this->setting_for_model();

        if (array_key_exists( $args[ 'label_for' ], $values )) {
            $value = $values[ $args[ 'label_for' ] ];
        } else {
            $value = '';
        }

        if (array_key_exists( 'input_type', $args )) {
            $input_type = $args[ 'input_type' ];
        } else {
            $input_type = 'text';
        }

        $tag->input( $input_type )
            ->named( $args[ 'label_for' ], $this->data[ 'option_name' ] )
            ->withValue( $value );

        if (empty( $tag->node->getAttribute( 'value' ) ) && array_key_exists( 'default_value', $args )) {
             $tag->node->setAttribute( 'value', $args[ 'default_value' ] );
        }

        return $tag;
    }

    protected function global_setting_for_model($option_name = null, $use_cache = true)
    {
        return setting_for_model($option_name, false, $use_cache);
    }

    protected function uncached_setting_for_model($option_name = null, $local_option = true)
    {
        return setting_for_model($option_name, $local_option, false);
    }

    protected function setting_for_model($option_name = null, $local_option = true, $use_cache = true)
    {
        if (!isset($option_name)) {
            $option_name = $this->data[ 'option_name' ];
        }
        if (!property_exists( $this, 'all_settings' )) {
            if ($local_option) {
                $this->all_settings = get_option( $option_name, false, $use_cache ) ?: array();
            } else {
                $this->all_settings = get_site_option( $option_name, false, $use_cache ) ?: array();
            }
        }

        return $this->all_settings;
    }

    public static function current_tab() {
      if (array_key_exists( 'tab', $_GET )) {
          return $_GET['tab'];
      } else {
          return 'general';
      }
    }
}
