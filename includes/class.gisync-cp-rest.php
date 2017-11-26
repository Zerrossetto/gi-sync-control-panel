<?php
namespace GISyncCP;

class Rest
{
    use Utils\Logging;

    private const SITE_FILTER_CB = array('GISyncCP\Rest', 'public_active_sites');

    public static function all_settings()
    {
        if (is_multisite()) {
            $json = self::multisite_all_settings();
        } else {
            $json = self::singlesite_all_settings();
        }
        return new \WP_REST_Response( $json, 200 );
    }

    public static function agency_settings($request)
    {
        assert( is_multisite() );

        $json = array();
        $json = self::get_general_options( BLOG_ID_CURRENT_SITE );
        $json[ 'agency' ] = get_site_option( Plugin::prefix( 'agency' ), array() );

        return new \WP_REST_Response( $json, 200 );
    }

    private static function singlesite_all_settings() {

      $json = self::get_general_options();
      $json[ 'agencies' ] = array( get_option( Plugin::prefix( 'agency' ), array() ) );

      return $json;
    }

    private static function multisite_all_settings()
    {
        $json = array();
        $agency_options = array();

        foreach (array_filter( get_sites(), self::SITE_FILTER_CB, ARRAY_FILTER_USE_BOTH ) as &$site) {
            if (is_main_site( $site->id )) {
                $json = self::get_general_options( $site->id );
            } else {
                $agency = get_blog_option(
                  $site->id,
                  Plugin::prefix( 'agency' ),
                  array()
                );
                if ($agency) {
                  array_push( $agency_options, $agency );
                }
            }
        }
        $json[ 'agencies' ] = $agency_options;
        return $json;
    }

    private static function public_active_sites($site, $index)
    {
        return $site->public && !$site->deleted && !$site->archived;
    }

    private static function get_general_options( $blog_id = null ) {

      $json = array();
      $option = Plugin::prefix( 'general' );
      $default = array(
        'gi_homepage' => 'http://gestionaleimmobiliare.it/',
        'connection_timeout' => 15
      );

      if (is_multisite()) {
        $json = get_blog_option( $blog_id ?: BLOG_ID_CURRENT_SITE, $option, $default );
      } else {
        $json = get_option( $option, $default );
      }

      if (array_key_exists( 'connection_timeout', $json )) {
        $json[ 'connection_timeout' ] = intval( $json[ 'connection_timeout' ] );
      }

      return $json;
    }
}
