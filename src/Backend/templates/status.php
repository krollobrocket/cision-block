<?php

$settings = $this->settings->toJson();

?>
<div class="wrap">
    <?php $this->displayTabs(); ?>
    <form id="status-form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <input type="hidden" name="action" value="cision_block_save_settings" />
        <table class="form-table">
            <tr>
                <td>
                    <textarea name="settings" readonly><?php echo $settings; ?></textarea>
                    <p class="description"><?php _e('If you need help, copy and paste the above information for faster support.', 'cision-block'); ?></p>
                </td>
            </tr>
        </table>
        <button type="button" class="button" onclick="document.querySelector('textarea').select(); document.execCommand('copy');"><?php _e('Copy for support', 'cision-block'); ?></button>
    </form>
</div>
