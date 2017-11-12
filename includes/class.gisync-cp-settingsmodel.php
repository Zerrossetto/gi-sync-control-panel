<?php
namespace GISyncCP;

class SettingsModel {
    use Utils\Logging;

    const START_GENERATE_PAGE = TRUE;

    private static $settings_key;

    /**
     *
     */
    function __construct( $yaml, $do_generate_page = FALSE ) {
        $this->data = yaml_parse_file( $yaml );

        //self::$settings_key = $this->data[ 'settings_key' ];

        if ( $do_generate_page )
            $this->generate_page();
    }

    /**
     *
     */
    function generate_page() {

        if ( array_key_exists( 'setting_key', $this->data ) ) {
            register_setting(
                $this->data[ 'prefix' ],
                $this->data[ 'settings_key' ],
                array(
                    'description' => $this->data[ 'description' ],
                    'sanitize_callback' => $this->data[ 'validation_callback' ],
                    'show_in_rest' => $this->data[ 'show_in_rest' ]
                )
            );
        }

        foreach ( $this->data[ 'sections' ] as &$section )
            $this->load_section( $this->data[ 'page' ], $section );
    }

    /**
     *
     */
    public static function general_section_callback () {
        echo '<p>GISync Panel Section Introduction.</p>';
    }

    /**
     *
     */
    public static function agency_section_callback () {
        echo '<p>Agencies section</p>';
    }

    public static function validate_settings( $values ) {

        $validator = new Utils\Validation( Plugin::prefix( 'messages' ) );
        $old_values = get_option( self::$settings_key );
        $no_errors = TRUE;

        foreach ( array_keys( $values ) as &$field ) {
            $invoke_target = array( $validator, 'invalid_' . $field );
            if ( method_exists ( ...$invoke_target ) ) {
                 if ( $error = call_user_func_array( $invoke_target, array( &$values ) ) ) {
                    $no_errors = FALSE;
                    SettingsModel::debug( $error );
                    add_settings_error(...$error);
                    if ( array_key_exists( $field, $old_values ) )
                        $values[ $field ] = $old_values[ $field ];
                }
            } else
                SettingsModel::debug(
                    "Skipping validation for field '$field' due to undefined validator"
                );
        }

        if ( $no_errors )
            $message = __( 'Settings saved', Plugin::PREFIX );
        else if ( $values != $old_values )
            $message = __( 'Settings partially saved', Plugin::PREFIX );

        if ( $values_persisted = isset( $message ) )
            add_settings_error(
                $validator->slug,
                Plugin::prefix( 'message' ),
                $message,
                'updated'
            );

        return $values;
    }

    /**
     *
     */
    protected function load_section( &$page, &$section ) {

        if ( array_key_exists( 'setting_key', $section ) ) {
            register_setting(
                $this->data[ 'prefix' ],
                $section[ 'settings_key' ],
                array(
                    'description' => $this->data[ 'description' ],
                    'sanitize_callback' => $this->data[ 'validation_callback' ],
                    'show_in_rest' => $this->data[ 'show_in_rest' ]
                )
            );
        }

        if ( array_key_exists( 'callback', $section ) )
            $callback = $section[ 'callback' ];
        else
            $callback = array( __CLASS__, 'default_section_callback' );

        add_settings_section( $section[ 'id' ], $section[ 'title' ], $callback, $page );

        foreach ( $section[ 'fields' ] as &$field )
            $this->load_field( $page, $section, $field );
    }

    /**
     *
     */
    protected function load_field( &$page, &$section, &$field ) {

        if ( array_key_exists( 'settings_key', $section ) )
            $settings_key = $section[ 'settings_key' ];
        else
            $settings_key = $this->data[ 'settings_key' ];

        if ( array_key_exists( 'callback', $field ) )
            $callback = $field[ 'callback' ];
        else
            $callback = array( __CLASS__, 'default_field_callback' );

        add_settings_field(
            $field[ 'id' ],
            $field[ 'title' ],
            $callback,
            $page,
            $section[ 'id' ],
            array_merge(
                $field[ 'args' ],
                array( 'settings_key' => $settings_key )
            )
        );
    }

    private static function prepare_default_input( &$args ) {
        $tag = new Utils\DOMBuilder();

        $all_settings = get_option( $args[ 'settings_key' ] ) ?: array();

        if ( array_key_exists( $args[ 'label_for' ], $all_settings ) )
            $value = $all_settings[ $args[ 'label_for' ] ];
        else
            $value = '';

        return $tag->input()
                   ->named( $args[ 'label_for' ], $args[ 'settings_key' ] )
                   ->withValue( $value );
    }

    public static function default_field_callback( $args ) {
        echo self::prepare_default_input( $args )->build();
    }

    public static function disabled_field_callback ( $args ) {
        echo self::prepare_default_input( $args )->disabled()->build();
    }

    public static function checkbox_fields_callback( $args ) {

        $builder = new Utils\DOMBuilder();

        $all_settings = get_option( $args[ 'settings_key' ] ) ?: array();

        if ( array_key_exists( $args[ 'label_for' ], $all_settings ) )
            $values = $all_settings[ $args[ 'label_for' ] ];
        else
            $values = array();

        foreach ( $args[ 'checkbox_fields' ] as &$field_name ) {
            if ( ! array_key_exists( $field_name, $values ) )
                $values[ $field_name ] = FALSE;
        }

        echo $builder->divForCheckboxes()
                     ->withCheckboxElements( $values, $args[ 'settings_key' ] )
                     ->build();
    }
}
