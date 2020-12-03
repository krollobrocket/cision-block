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
                    <label for="show-filters"><?php _e('Show filters', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="show-filters"<?php checked($this->settings->get('show_filters')); ?>" />
                    <p class="description"><?php _e('Enable filtering of feed items.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="filter-all-text"><?php _e('All item filter text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="filter-all-text" value="<?php echo $this->settings->get('filter_all_text'); ?>"<?php echo !$this->settings->get('show_filters') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Button text for \'all\' filter.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="filter-regulatory-text"><?php _e('Regulatory item filter text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="filter-regulatory-text" value="<?php echo $this->settings->get('filter_regulatory_text'); ?>"<?php echo !$this->settings->get('show_filters') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Button text for \'regulatory\' filter.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="filter-non-regulatory-text"><?php _e('Non-regulatory item filter text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="filter-non-regulatory-text" value="<?php echo $this->settings->get('filter_non_regulatory_text'); ?>"<?php echo !$this->settings->get('show_filters') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Button text for \'non-regulatory\' filter.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', Settings::TEXTDOMAIN), 'primary', 'cision-block-filters'); ?>
    </form>
</div>
