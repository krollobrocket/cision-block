<div class="wrap">
    <h1><?php _e('Cision Block', CISION_BLOCK_TEXTDOMAIN); ?></h1>
    <?php $this->displayTabs(); ?>
    <form action="" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="internal-links"><?php _e('Internal links', CISION_BLOCK_TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="internal-links"<?php checked($this->settings->get('internal_links')); ?>/>
                    <p class="description"><?php _e('Display feed items directly in Wordpress.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="base-slug"><?php _e('Slug', CISION_BLOCK_TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" name="base-slug" value="<?php echo $this->settings->get('base_slug'); ?>" />
                    <p class="description"><?php _e('The base slug to use when displaying feed items in Wordpress.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                    <p class="description"><?php _e(sprintf('Current format is: %s/%s/AAA093541230X/', get_bloginfo('url'), $this->settings->get('base_slug')), CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', CISION_BLOCK_TEXTDOMAIN), 'primary', 'cision-block-experimental'); ?>
    </form>
</div>
