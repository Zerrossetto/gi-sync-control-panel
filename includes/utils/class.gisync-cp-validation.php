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


/*
 * ============================================================================
 * ==                    GI OPTION FLAGS VALIDATION                          ==
 * ============================================================================
 */

    public function invalid_abstract(&$values)
    {
        return $this->validate_checkbox('abstract', $values[ 'opt' ] );
    }

    public function invalid_agente(&$values)
    {
        return $this->validate_checkbox('agente', $values[ 'opt' ] );
    }

    public function invalid_finiture(&$values)
    {
        return $this->validate_checkbox('finiture', $values[ 'opt' ] );
    }

    public function invalid_flag_storico(&$values)
    {
        return $this->validate_checkbox('flag_storico', $values[ 'opt' ] );
    }

    public function invalid_geo_id(&$values)
    {
        return $this->validate_checkbox('geo_id', $values[ 'opt' ] );
    }

    public function invalid_i18n(&$values)
    {
        return $this->validate_checkbox('i18n', $values[ 'opt' ] );
    }

    public function invalid_ind_reale(&$values)
    {
        return $this->validate_checkbox('ind_reale', $values[ 'opt' ] );
    }

    public function invalid_latlng(&$values)
    {
        return $this->validate_checkbox('latlng', $values[ 'opt' ] );
    }

    public function invalid_micro_categorie(&$values)
    {
        return $this->validate_checkbox('micro_categorie', $values[ 'opt' ] );
    }

    public function invalid_note_nascoste(&$values)
    {
        return $this->validate_checkbox('note_nascoste', $values[ 'opt' ] );
    }

    public function invalid_persone(&$values)
    {
        return $this->validate_checkbox('persone', $values[ 'opt' ] );
    }

    public function invalid_stima(&$values)
    {
        return $this->validate_checkbox('stima', $values[ 'opt' ] );
    }

    public function invalid_video(&$values)
    {
        return $this->validate_checkbox('video', $values[ 'opt' ] );
    }

    public function invalid_virtual(&$values)
    {
        return $this->validate_checkbox('virtual', $values[ 'opt' ] );
    }

/*
 * ============================================================================
 * ==              IMAGE MANIPULATION FLAGS VALIDATION                       ==
 * ============================================================================
 */

    public function invalid_apply_watermark(&$values)
    {
         return $this->validate_checkbox('apply_watermark', $values[ 'image' ] );
    }

    public function invalid_normalize(&$values)
    {
         return $this->validate_checkbox('normalize', $values[ 'image' ] );
    }
    public function invalid_resize(&$values)
    {
         return $this->validate_checkbox('resize', $values[ 'image' ] );
    }

/*
 * ============================================================================
 * ==                            UTILITIES                                   ==
 * ============================================================================
 */

    private function validate_checkbox($name, &$values)
    {
        if (!array_key_exists( $name, $values )) {
            return array(
            $this->slug,
            'checkbox-key-not-present',
            'Internal error, Checkbox '. $name .' should be present'
            );
        } elseif (!in_array( intval($values[ $name ]), array( 0, 1 ))) {
            return array(
            $this->slug,
            'checkbox-invalid-value',
            'Unexpected value for checkbox '. $name .'. Gotten '. $values[ $name ]
            );
        }
    }
}
