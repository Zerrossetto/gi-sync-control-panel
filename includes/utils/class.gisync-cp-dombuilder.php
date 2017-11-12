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

    function named( $name, $group = NULL ) {

        if ( isset( $group ) )
            $name = $group . '[' . $name . ']';

        $this->node->setAttribute( 'name', $name );
        return $this;
    }

    function withValue( $value ) {
        if ( empty( $value ) ) {
            $this->debug(
                $this->node->getAttribute( 'name' ) ?: 'name-not-set',
                'option, discarding empty or null value'
            );
            $this->node->setAttribute( 'value', '' );
        } else
            $this->node->setAttribute( 'value', esc_attr( $value ) );

        return $this;
    }

    function build() {
        return $this->doc->saveHTML();
    }
}
