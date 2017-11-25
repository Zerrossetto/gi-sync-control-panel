<?php
namespace GISyncCP\Utils;

trait FileSystemMixins
{

    public static function page_path( $page, $section = 'admin')
    {
        return plugin_dir_path( GISYNCCP_FILE ) . $section . '/'. urlencode( $page ) .'.php';
    }

    public static function yaml( $resource ) {
      return plugin_dir_path( GISYNCCP_FILE ) . 'includes/view-template/'. $resource .'.yaml';
    }

    public static function stylesheet( $resource, $section = 'admin' ) {
      return plugin_dir_path( GISYNCCP_FILE ) . $section . '/css/'. $resource . '.css';
    }

    public static function javacript( $resource, $section = 'admin' ) {
      return plugin_dir_path( GISYNCCP_FILE ) . $section . '/js/'. $resource . '.js';
    }
}
