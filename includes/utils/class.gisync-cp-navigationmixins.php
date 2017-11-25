<?php
namespace GISyncCP\Utils;

trait NavigationMixins
{
    public static function tab_url($tab_name)
    {
        return menu_page_url( 'gisync_cp_settings', false ) . '&tab=' . urlencode( $tab_name );
    }

    public static function current_tab()
    {
        if (array_key_exists( 'tab', $_GET )) {
            return $_GET['tab'];
        } else {
            return 'general';
        }
    }

    public static function active_class_if_active( $tab_id ) {
        if ( self::current_tab() === $tab_id ) {
          return ' nav-tab-active';
        } else {
          return '';
        }
    }
}
