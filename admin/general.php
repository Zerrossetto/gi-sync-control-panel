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
