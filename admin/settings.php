<?php
if ( !current_user_can( 'manage_options' ) ) {
    GISyncCP\Plugin::debug( 'Current user doesn\'t have grants to access settings page' );
    return;
}
?>
<div class="wrap">
    <h1><?= esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields( GISyncCP\Plugin::PREFIX );
        do_settings_sections( 'gisync_cp_settings');
        submit_button( 'Save Settings' );
        ?>
    </form>
</div>
