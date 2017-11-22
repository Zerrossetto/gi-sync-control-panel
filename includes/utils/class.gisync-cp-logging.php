<?php
namespace GISyncCP\Utils;

trait Logging
{
    public static function debug(...$log_args)
    {
        if (WP_DEBUG) {
            $caller = debug_backtrace()[1];

            if (array_key_exists( 'class', $caller )) {
                $to_log = $caller[ 'class' ] . '->' . $caller[ 'function' ] . ' :: ';
            } else {
                $to_log = $caller[ 'function' ] . ' :: ';
            }

            foreach ($log_args as &$log) {
                if (is_array( $log ) || is_object( $log )) {
                     $to_log = $to_log . print_r( $log, true ) . ' ';
                } else {
                    $to_log = $to_log . $log . ' ';
                }
            }

            error_log( $to_log );
        }
    }
}
