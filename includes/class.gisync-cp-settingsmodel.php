<?php
namespace GISyncCP;

class SettingsModel {
    use Utils\Logging;

    /**
     *
     */
    function __construct( $yaml ) {
        $this->data = yaml_parse_file( $yaml );
    }

    /**
     *
     */
    function generate_page() {

        register_setting( $this->data[ 'prefix' ], $this->data[ 'settings_key' ] );

        foreach ( $this->data[ 'sections' ] as &$section )
            $this->load_section( $this->data[ 'page' ], $section );
    }

    /**
     *
     */
    public static function default_section_callback () {
        echo '<p>GISync Panel Section Introduction.</p>';
    }

    /**
     *
     */
    public static function default_field_callback( $args ) {
        $key = $args[ Plugin::prefix( 'field' ) ];
        $setting = get_option( $key ) ?: '';
        echo '<input type="text" name="'.$key.'" value="'.esc_attr( $setting ).'"s/>';
    }

    /**
     *
     */
    protected function load_section( &$page, &$section ) {

        if ( array_key_exists( 'callback', $section ) )
            $callback = $section[ 'callback' ];
        else
            $callback = array( __CLASS__, 'default_section_callback' );

        add_settings_section( $section[ 'id' ], $section[ 'title' ], $callback, $page );

        foreach ( $section[ 'fields' ] as &$field )
            $this->load_field( $page, $section[ 'id' ], $field );
    }

    /**
     *
     */
    protected function load_field( &$page, &$section, &$field ) {

        if ( array_key_exists( 'callback', $field ) )
            $callback = $field[ 'callback' ];
        else
            $callback = array( __CLASS__, 'default_field_callback' );

        add_settings_field(
            $field[ 'id' ],
            $field[ 'title' ],
            $callback,
            $page,
            $section,
            array_merge(
                $field[ 'args' ],
                array( 'gisync_cp_field' => $field[ 'id' ] )
            )
        );
    }
}
