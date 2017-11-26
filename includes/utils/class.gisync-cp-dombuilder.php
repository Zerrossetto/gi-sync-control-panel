<?php
namespace GISyncCP\Utils;

class DOMBuilder
{
    use Logging;

    const NO_SETTINGS_API_GROUP = null;

    function __construct($doc = null)
    {
        if (isset( $doc ) && $doc instanceof \DOMDocument) {
            $this->doc = $doc;
        } else {
            $this->doc = new \DOMDocument();
        }
    }

    function input($type = 'text')
    {
        $this->node = $this->doc->createElement( 'input' );
        $this->doc->appendChild( $this->node );
        $this->node->setAttribute( 'type', $type );
        return $this;
    }

    function divForCheckboxes()
    {
        $this->node = $this->doc->createElement( 'div' );
        $this->node->setAttribute( 'class', 'gisync-checkbox-container' );
        $this->doc->appendChild( $this->node );
        return $this;
    }

    function tabsNavigationBar()
    {
        $this->node = $this->doc->createElement( 'h2' );
        $this->node->setAttribute( 'class', 'nav-tab-wrapper' );
        $this->doc->appendChild( $this->node );
        return $this;
    }

    function named($name, $group = null)
    {

        if (isset( $group )) {
            $name = $group . '[' . $name . ']';
        }

        $this->node->setAttribute( 'name', $name );
        return $this;
    }

    function withValue($value)
    {
        if (empty( $value )) {
            /*$this->debug(
                $this->node->getAttribute( 'name' ) ?: 'name-not-set',
                'option, discarding empty or null value'
            );*/
            $this->node->setAttribute( 'value', '' );
        } else {
            $this->node->setAttribute( 'value', esc_attr( $value ) );
        }

        return $this;
    }

    function disabled()
    {
        $this->node->setAttribute( 'disabled', 'disabled' );
        return $this;
    }

    function withCheckboxElements(&$group, &$checkboxes, $prefix = null)
    {

        foreach ($checkboxes as $name => $checked) {
            $span = $this->doc->createElement( 'span', $name );

            if (isset( $group )) {
                $name = $prefix . '[' . $group . ']' .'[' . $name . ']' ;
            }

            $label = $this->doc->createElement( 'label' );
            $label->setAttribute( 'for', $name );

            $checkbox = $this->doc->createElement( 'input' );
            $checkbox->setAttribute( 'type', 'checkbox' );
            $checkbox->setAttribute( 'name', $name );
            $checkbox->setAttribute( 'value', '1' );
            if ($checked) {
                $checkbox->setAttribute( 'checked', 'checked' );
            }

            $label->appendChild( $span );
            $label->appendChild( $checkbox );

            $this->node->appendChild( $label );
        }
        return $this;
    }

    function addTab($id, $description, $base_url, $active_tab = false)
    {
        $anchor = $this->doc->createElement( 'a', $description );
        $anchor->setAttribute(
          'href',
          menu_page_url( 'gisync_cp_settings', false ) . '&tab=' . urlencode( $id )
        );
        $anchor->setAttribute(
          'class',
          $active_tab ? 'nav-tab nav-tab-active' : 'nav-tab'
        );
        $this->node->appendChild( $anchor );

        return $this;
    }

    function build()
    {
        return $this->doc->saveHTML();
    }
}
