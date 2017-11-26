<?php
if (!current_user_can( 'manage_options' )) {
    wp_die(
      'You don\'t have enough permissions to access this page',
      'Forbidden',
      array( 'back_link' => true )
    );
}

use GISyncCP\Plugin;
?>
<div class="wrap">

    <h2><?= esc_html( $this->model->data[ 'page_title' ] ); ?></h2>
<?php
$this->model->do_tabbed_navigation();
settings_errors( $this->prefix( 'messages' ) );

switch ( $this->current_tab ) {
    case 'general':
    case 'agency':
        echo '<form action="options.php" method="post">';
        settings_fields( $this->model->data[ 'option_name' ] );
        do_settings_sections( $this->model->data[ 'page' ] );
        submit_button( 'Save Settings' );
        echo '</form>';
        break;
    default:
        wp_die(
          'Invalid tab selector',
          'Unexpected error',
          array( 'back_link' => true )
        );
}

?>

</div>
