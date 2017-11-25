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
                'connection-timeout-invalid-value',
                'Expected numeric value for Connection timeout, gotten '.$values[ 'connection_timeout' ].' instead '
            );
        } elseif (intval($values [ 'connection_timeout' ]) <= 0) {
            return array(
              $this->slug,
              'connection-timeout-invalid-value',
              'Connection timeout value must be greater than 0'
            );
        }
    }

    public function invalid_abstract(&$values) {
      return validate_checkbox_value('abstract', $values);
    }

    private function validate_checkbox_value($checkbox_name, &$values) {
      if (!array_key_exists( $checkbox_name, $values )) {
        return array(
            $this->slug,
            'checkbox-key-not-present',
            'Internal error, Checkbox '. $checkbox_name .' should be present'
        );
      } elseif (!in_array( intval($values[ $checkbox_name ]), array( 0, 1 ))) {
        return array(
            $this->slug,
            'checkbox-invalid-value',
            'Unexpected value for checkbox '. $checkbox_name .'. Gotten '. $values[ $checkbox_name ]
        );
      }
    }
}
