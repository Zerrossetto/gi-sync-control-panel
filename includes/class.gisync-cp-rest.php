<?php
namespace GISyncCP;

class Rest {
    use Utils\Logging;

    public static function all_settings() {
        return new \WP_REST_Response(
            \get_option( 'gisync_cp_options' ),
            200
        );
    }

    public static function agency_settings( $request ) {

        $sequence = $request[ 'sequence' ];

        if ( !is_numeric( $sequence ) )
            return new \WP_REST_Response(
                array(
                    'code' => 'bad-request',
                    'description' => "invalid sequence identifier '$sequence'"
                ),
                400
            );

        $options = \get_option( 'gisync_cp_options' );

        if ( array_key_exists( $sequence, $options[ 'agencies' ] ) )
            return new \WP_REST_Response(
                $options[ 'agencies' ][ $sequence ],
                200
            );

        return new \WP_REST_Response(
            array(
                'code' => 'agency-not-found',
                'description' => "agency nr. $sequence wasn't found"
            ),
            404
        );
    }
}
