<?php

use CisionBlock\Config\Settings;

?>
<div class="wrap">
    <h1><?php _e('Cision Block', Settings::TEXTDOMAIN); ?></h1>
    <?php $this->displayTabs(); ?>
    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <input type="hidden" name="action" value="cision_block_save_settings" />
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="internal_links"><?php _e('Internal links', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="hidden" id="hidden_internal_links" name="internal_links" value="0" />
                    <input type="checkbox" name="internal_links"<?php checked($this->settings->get('internal_links')); ?>/>
                    <p class="description"><?php _e('Display feed items directly in Wordpress.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="base_slug"><?php _e('Slug', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" name="base_slug" value="<?php echo $this->settings->get('base_slug'); ?>" />
                    <p class="description"><?php _e('The base slug to use when displaying feed items in Wordpress.', Settings::TEXTDOMAIN); ?></p>
                    <p class="description"><?php _e(sprintf('Current format is: %s/%s/AAA093541230X/', get_bloginfo('url'), $this->settings->get('base_slug')), Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', Settings::TEXTDOMAIN), 'primary', 'cision-block-permalinks'); ?>
    </form>
</div>
