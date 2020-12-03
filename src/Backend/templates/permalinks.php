<?php

use CisionBlock\Config\Settings;

?>
<div class="wrap">
    <h1><?php _e('Cision Block', Settings::TEXTDOMAIN); ?></h1>
    <?php $this->displayTabs(); ?>
    <form action="" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="internal-links"><?php _e('Internal links', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="internal-links"<?php checked($this->settings->get('internal_links')); ?>/>
                    <p class="description"><?php _e('Display feed items directly in Wordpress.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="base-slug"><?php _e('Slug', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" name="base-slug" value="<?php echo $this->settings->get('base_slug'); ?>" />
                    <p class="description"><?php _e('The base slug to use when displaying feed items in Wordpress.', Settings::TEXTDOMAIN); ?></p>
                    <p class="description"><?php _e(sprintf('Current format is: %s/%s/AAA093541230X/', get_bloginfo('url'), $this->settings->get('base_slug')), Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', Settings::TEXTDOMAIN), 'primary', 'cision-block-permalinks'); ?>
    </form>
</div>
