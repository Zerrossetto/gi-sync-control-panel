<?php
if (!current_user_can( 'manage_options' )) {
  wp_die(
    'You don\'t have enough permissions to access this page',
    'Forbidden',
    array( 'back_link' => true )
  );
}
?>

<form action="options.php" method="post">
    <?php
    settings_fields( $this->model->data[ 'prefix' ] );
    do_settings_sections( $this->model->data[ 'page' ] );
    submit_button( 'Save Settings' );
    ?>
</form>
