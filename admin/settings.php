<?php
if (!current_user_can('manage_options'))
    return;
?>
<div class="wrap">
    <h1><?= esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields(GISync_CP_Plugin::PREFIX . '_options');
        do_settings_sections(GISync_CP_Plugin::PREFIX);
        submit_button('Save Settings');
        ?>
    </form>
</div>
