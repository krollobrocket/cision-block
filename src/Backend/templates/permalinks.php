<div class="wrap">
    <?php $this->displayTabs(); ?>
    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <input type="hidden" name="action" value="cision_block_save_settings" />
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="internal_links"><?php _e('Internal links', 'cision-block'); ?></label>
                </th>
                <td>
                    <input type="hidden" id="hidden_internal_links" name="internal_links" value="0" />
                    <input type="checkbox" id="internal_links" name="internal_links"<?php checked($this->settings->get('internal_links')); ?>/>
                    <p class="description"><?php _e('Display feed items directly in Wordpress.', 'cision-block'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="base_slug"><?php _e('Slug', 'cision-block'); ?></label>
                </th>
                <td>
                    <input type="text" id="base_slug" name="base_slug" value="<?php echo $this->settings->get('base_slug'); ?>" />
                    <p class="description"><?php _e('The base slug to use when displaying feed items in Wordpress.', 'cision-block'); ?></p>
                    <p class="description"><?php _e(sprintf('Current format is: %s/%s/AAA093541230X/', get_bloginfo('url'), $this->settings->get('base_slug')), 'cision-block'); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', 'cision-block'), 'primary', 'cision-block-permalinks'); ?>
    </form>
</div>
