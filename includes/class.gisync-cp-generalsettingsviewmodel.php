<?php
namespace GISyncCP;

class GeneralSettingsViewModel
{
    use Utils\Logging;
    use Utils\NavigationMixins;

    const START_GENERATE_PAGE = true;

    private static $option_name;

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

        add_filter(
         'pre_update_option_' . $this->data[ 'option_name'],
         array( $this, 'validate_input' ),
         11, // priority
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
                Plugin::PREFIX, //option_group
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
        $old_values = $this->setting_for_model();
        $no_errors = true;

        // FIXME: WP is returning null and I don't know why, to invetigate
        /*array_walk_recursive(
            $new_values,
            function ($item, $key) use (&$new_values, &$validator, &$old_values, &$no_errors) {
                $invoke_target = array( $validator, 'invalid_' . $key );
                if (method_exists ( ...$invoke_target )) {
                    if ($error = call_user_func_array( $invoke_target, array( &$values ) )) {
                        $no_errors = false;
                        add_settings_error(...$error);
                        if (array_key_exists( $key, $old_values )) {
                            $values[ $key ] = $old_values[ $key ];
                        }
                    }
                }
            }
        );*/

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

    protected function setting_for_model($option_name = null)
    {
        if (!isset($option_name)) {
            $option_name = $this->data[ 'option_name' ];
        }
        if (!property_exists( $this, 'all_settings' )) {
            $this->all_settings = get_option( $option_name ) ?: array();
            $this->debug('got option', $option_name, 'with', $this->all_settings);
        } else {
            $this->debug('cached option', $option_name, 'with', $this->all_settings);
        }

        return $this->all_settings;
    }
}
