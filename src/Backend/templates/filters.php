<?php

use CisionBlock\Config\Settings;

?>
<div class="wrap">
    <?php $this->displayTabs(); ?>
    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <input type="hidden" name="action" value="cision_block_save_settings" />
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="show_filters"><?php _e('Show filters', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="hidden" id="hidden_show_filters" name="show_filters" value="0" />
                    <input type="checkbox" name="show_filters"<?php checked($this->settings->get('show_filters')); ?>" />
                    <p class="description"><?php _e('Enable filtering of feed items.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="filter_all_text"><?php _e('All item filter text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="filter_all_text" value="<?php echo $this->settings->get('filter_all_text'); ?>"<?php echo !$this->settings->get('show_filters') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Button text for \'all\' filter.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="filter_regulatory_text"><?php _e('Regulatory item filter text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="filter_regulatory_text" value="<?php echo $this->settings->get('filter_regulatory_text'); ?>"<?php echo !$this->settings->get('show_filters') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Button text for \'regulatory\' filter.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="filter_non_regulatory_text"><?php _e('Non-regulatory item filter text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="filter_non_regulatory_text" value="<?php echo $this->settings->get('filter_non_regulatory_text'); ?>"<?php echo !$this->settings->get('show_filters') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Button text for \'non-regulatory\' filter.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', Settings::TEXTDOMAIN), 'primary', 'cision-block-filters'); ?>
    </form>
</div>
