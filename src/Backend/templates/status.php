<?php

use CisionBlock\Config\Settings;

$settings = $this->settings->toJson();

?>
<style type="text/css">
#status-form textarea {
    font-family: monospace;
    width: 100%;
    margin: 0;
    height: 300px;
    padding: 20px;
    border-radius: 0;
    resize: none;
    font-size: 12px;
    line-height: 20px;
    outline: 0;
}
</style>
<div class="wrap">
    <?php $this->displayTabs(); ?>
    <form id="status-form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <input type="hidden" name="action" value="cision_block_save_settings" />
        <table class="form-table">
            <tr>
                <td>
                    <textarea name="settings" readonly><?php echo $settings; ?></textarea>
                    <p class="description"><?php _e('If you need help, copy and paste the above information for faster support.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <button type="button" class="button" onclick="document.querySelector('textarea').select(); document.execCommand('copy');"><?php _e('Copy for support', Settings::TEXTDOMAIN); ?></button>
    </form>
</div>
