<?php
namespace GISyncCP\Utils;

class Validation
{
    use Logging;

    function __construct($slug)
    {
        $this->slug = $slug;
    }

    public function is_url(&$value)
    {
        return !empty( $value ) && filter_var( $value, FILTER_VALIDATE_URL );
    }

    public function is_numeric(&$value)
    {
        return !empty( $value ) && \is_numeric( $value );
    }

    public function invalid_gi_homepage(&$values)
    {
        if (!array_key_exists( 'gi_homepage', $values )) {
            return array(
                $this->slug,
                'gi-homepage-not-present',
                'Internal error, GI homepage should be present'
            );
        } elseif (!self::is_url( $values [ 'gi_homepage' ] )) {
            return array(
                $this->slug,
                'gi-homepage-invalid-url',
                'Homepage GI is not a valid url'
            );
        }
    }

    public function invalid_connection_timeout(&$values)
    {
        if (!array_key_exists( 'connection_timeout', $values )) {
            return array(
                $this->slug,
                'connection-timeout-not-present',
                'Internal error, Connection timeout should be present'
            );
        } elseif (!self::is_numeric( $values [ 'connection_timeout' ] )) {
            return array(
                $this->slug,
                'connection-timeout-invalid-number',
                'Expected numeric value for Connection timeout, gotten '.$values[ 'connection_timeout' ].' instead '
            );
        }
    }
}
