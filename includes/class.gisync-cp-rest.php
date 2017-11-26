<?php
namespace GISyncCP;

class Rest
{
    use Utils\Logging;

    public static function all_settings()
    {
        if (is_multisite()) {
          $this->debug(get_sites());
          return new \WP_REST_Response(
              array( 'pippo' => 1, 'pluto' => 2 ),
              200
          );
        } else {
          return new \WP_REST_Response(
              \get_option( 'gisync_cp_options' ),
              200
          );
        }
    }

    public static function agency_settings($request)
    {

        $blog_id = $request[ 'blog_id' ];

        if (!is_numeric( $blog_id )) {
            return new \WP_REST_Response(
                array(
                    'code' => 'bad-request',
                    'description' => "invalid sequence identifier '$blog_id'"
                ),
                400
            );
        }

        $options = \get_option( 'gisync_cp_options' );

        if (array_key_exists( $sequence, $options[ 'agencies' ] )) {
            return new \WP_REST_Response(
                $options[ 'agencies' ][ $sequence ],
                200
            );
        }

        return new \WP_REST_Response(
            array(
                'code' => 'agency-not-found',
                'description' => "agency nr. $blog_id wasn't found"
            ),
            404
        );
    }
}
