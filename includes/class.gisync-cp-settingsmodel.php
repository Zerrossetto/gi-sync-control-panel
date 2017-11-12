<?php
namespace GISyncCP;

class SettingsModel {
    use Utils\Logging;

    const START_GENERATE_PAGE = TRUE;

    /**
     *
     */
    function __construct( $yaml, $do_generate_page = FALSE ) {
        $this->data = yaml_parse_file( $yaml );

        if ( $do_generate_page )
            $this->generate_page();
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

        $tag = new Utils\DOMBuilder();

        $all_settings = get_option( $args[ 'settings_key' ] ) ?: array();

        if ( array_key_exists( $args[ 'label_for' ], $all_settings ) )
            $value = $all_settings[ $args[ 'label_for' ] ];
        else
            $value = '';

        echo $tag->input()
                 ->named( $args[ 'label_for' ], $args[ 'settings_key' ] )
                 ->withValue( $value )
                 ->build();
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
                array( 'settings_key' => $this->data[ 'settings_key' ] )
            )
        );
    }
}
