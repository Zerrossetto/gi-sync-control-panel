<?php
use GISyncCP\Plugin;

if ( !current_user_can( 'manage_options' ) ) {
    Plugin::debug( 'Current user doesn\'t have grants to access settings page' );
    return;
}

if (isset( $_GET['settings-updated'] ))
    add_settings_error(
        Plugin::prefix( 'messages' ),
        Plugin::prefix( 'message' ),
        __( 'Settings Saved', Plugin::PREFIX ),
        'updated'
    );

settings_errors( Plugin::prefix( 'messages' ) );
?>
<div class="wrap">
    <h1><?= esc_html( $gisync_cp_plugin->model[ 'page_title' ] ); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields( $gisync_cp_plugin->model[ 'prefix' ] );
        do_settings_sections( $gisync_cp_plugin->model[ 'page' ] );
        submit_button( 'Save Settings' );
        ?>
    </form>
</div>
