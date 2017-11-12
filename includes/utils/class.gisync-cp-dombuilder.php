<?php
namespace GISyncCP\Utils;

class DOMBuilder {
    use Logging;

    const NO_SETTINGS_API_GROUP = NULL;

    function __construct ( $doc = NULL ) {
        if ( isset( $doc ) && $doc instanceof \DOMDocument )
            $this->doc = $doc;
        else
            $this->doc = new \DOMDocument();
    }

    function input( $type = 'text' ) {
        $this->node = $this->doc->createElement( 'input' );
        $this->doc->appendChild( $this->node );
        $this->node->setAttribute( 'type', $type );
        return $this;
    }

    function divForCheckboxes() {
        $this->node = $this->doc->createElement( 'div' );
        $this->node->setAttribute( 'class', 'gisync-checkbox-container' );
        $this->doc->appendChild( $this->node );
        return $this;
    }

    function named( $name, $group = NULL ) {

        if ( isset( $group ) )
            $name = $group . '[' . $name . ']';

        $this->node->setAttribute( 'name', $name );
        return $this;
    }

    function withValue( $value ) {
        if ( empty( $value ) ) {
            /*$this->debug(
                $this->node->getAttribute( 'name' ) ?: 'name-not-set',
                'option, discarding empty or null value'
            );*/
            $this->node->setAttribute( 'value', '' );
        } else
            $this->node->setAttribute( 'value', esc_attr( $value ) );

        return $this;
    }

    function disabled() {
        $this->node->setAttribute( 'disabled', 'disabled' );
        return $this;
    }

    function withCheckboxElements( &$group, &$checkboxes, $prefix = NULL ) {

        foreach ( $checkboxes as $name => $checked ) {

            $span = $this->doc->createElement( 'span', $name );

            if ( isset( $group ) )
                $name = $prefix . '[' . $group . ']' .'[' . $name . ']' ;

            $label = $this->doc->createElement( 'label' );
            $label->setAttribute( 'for', $name );

            $checkbox = $this->doc->createElement( 'input' );
            $checkbox->setAttribute( 'type', 'checkbox' );
            $checkbox->setAttribute( 'name', $name );
            $checkbox->setAttribute( 'value', '1' );
            if ( $checked )
                $checkbox->setAttribute( 'checked', 'checked' );

            $label->appendChild( $span );
            $label->appendChild( $checkbox );

            $this->node->appendChild( $label );
        }
        return $this;
    }

    function build() {
        return $this->doc->saveHTML();
    }
}
