<?php

use CisionBlock\Settings\Settings;

$templates = get_page_templates();
?>
<div class="wrap">
    <?php do_action('cision_block_admin_notices'); ?>
    <h1></h1>
    <?php $this->displayTabs(); ?>
    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <input type="hidden" name="action" value="cision_block_save_settings" />
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="count"><?php _e('Number of feed items', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" min="1" max="<?php echo Settings::MAX_ITEMS_PER_FEED; ?>" name="count" value="<?php echo intval($this->settings->get('count')); ?>" />
                    <p class="description"><?php _e('The maximum number of items in the feed.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="source_uid"><?php _e('Cision Feed source id', Settings::TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" class="regular-text" name="source_uid" value="<?php echo sanitize_text_field($this->settings->get('source_uid')); ?>"/>
                    <p class="description"><?php _e('A valid unique JSON identifier for your cision feed.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="types"><?php _e('Type of feed items', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <select class="regular-text" name="types[]" multiple>
                        <?php foreach (\CisionBlock\Frontend\Frontend::getInstance()->getFeedTypes() as $key => $value) : ?>
                        <option value="<?php echo $key; ?>"<?php selected(in_array($key, $this->settings->get('types'))); ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Type of feed items to include.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="start"><?php _e('Start date', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="date" name="start" value="<?php echo $this->settings->get('start_date'); ?>" />
                    <p class="description"><?php _e('Defines the start date of the date interval the press releases and/or reports are collected from. The format is 2001-12-31.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="end"><?php _e('End date', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="date" name="end" value="<?php echo $this->settings->get('end_date'); ?>" />
                    <p class="description"><?php _e('Defines the end date of the date interval the press releases and/or reports are collected from. The format is 2001-12-31.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="mark_regulatory"><?php _e('Show regulatory/non-regulatory', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="hidden" id="hidden_mark_regulatory" name="mark_regulatory" value="0" />
                    <input type="checkbox" name="mark_regulatory"<?php checked($this->settings->get('mark_regulatory')); ?>" />
                    <p class="description"><?php _e('Emphasis if a release if regulatory or non-regulatory.', Settings::TEXTDOMAIN); ?></p>
                    <p class="description"><?php _e(sprintf('Use the special value <b>%s</b> to skip marking a type of releases.', htmlspecialchars('<none>')), Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="regulatory_text"><?php _e('Regulatory item text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="regulatory_text" value="<?php echo $this->settings->get('regulatory_text'); ?>"<?php echo !$this->settings->get('mark_regulatory') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Text to display for regulatory items.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="non_regulatory_text"><?php _e('Non-regulatory item text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="non_regulatory_text" value="<?php echo $this->settings->get('non_regulatory_text'); ?>"<?php echo !$this->settings->get('mark_regulatory') ? ' disabled' : ''; ?>>
                    <p class="description"><?php _e('Text to display for non-regulatory items.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tags"><?php _e('Tags', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="tags" value="<?php echo $this->settings->get('tags'); ?>">
                    <p class="description"><?php _e('Defines a filter on tags, this will return releases with these tags. One or several
                            tags can be provided separated with a comma.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="search_term"><?php _e('Search term', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="search_term" value="<?php echo $this->settings->get('search_term'); ?>">
                    <p class="description"><?php _e('Free text search in release titles.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="categories"><?php _e('Categories', Settings::TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" class="regular-text" name="categories" value="<?php echo $this->settings->get('categories'); ?>">
                    <p class="description"><?php _e('Defines a filter on categories, this will return releases with connected to these categories. One or several
                            categories can be provided separated with a comma.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="items_per_page"><?php _e('Items per page', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" min="0" max="<?php echo Settings::MAX_ITEMS_PER_PAGE; ?>" name="items_per_page" value="<?php echo $this->settings->get('items_per_page'); ?>" />
                    <p class="description"><?php _e('Number of items on each page (set to 0 to disable).', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="language"><?php _e('Language', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <select name="language">
                        <option value=""><?php _e('Select'); ?></option>
                        <?php foreach ($this->getLanguages() as $code => $name) : ?>
                            <option value="<?php echo $code; ?>"<?php selected($code === $this->settings->get('language')) ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('The language for each feed item.'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="readmore"><?php _e('Read more text', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" name="readmore" value="<?php echo $this->settings->get('readmore'); ?>" />
                    <p class="description"><?php _e('The \'Read more\' button text. If this value is empty then the button will not be visible.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="view"><?php _e('Display mode', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <select name="view">
                        <option value="1"<?php selected($this->settings->get('view_mode') == 1); ?>><?php _e('All', Settings::TEXTDOMAIN); ?></option>
                        <option value="2"<?php selected($this->settings->get('view_mode') == 2); ?>><?php _e('Regulatory', Settings::TEXTDOMAIN); ?></option>
                        <option value="3"<?php selected($this->settings->get('view_mode') == 3); ?>><?php _e('Non-regulatory', Settings::TEXTDOMAIN); ?></option>
                    </select>
                    <p class="description"><?php _e('What kind of feed items to display.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="date_format"><?php _e('Date format', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" name="date_format" value="<?php echo $this->settings->get('date_format'); ?>" />
                    <p class="description"><?php _e('The format to use for dates.', Settings::TEXTDOMAIN); ?></p>
                    <p class="description"><?php _e('You can read more about the date/time formats: ', Settings::TEXTDOMAIN); ?><a target="_blank" href="http://php.net/manual/en/datetime.formats.php"><?php _e('Here', Settings::TEXTDOMAIN); ?></a></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="image_style"><?php _e('Image style', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <select name="image_style">
                        <option value=""><?php _e('Select', Settings::TEXTDOMAIN); ?></option>
                        <?php foreach ($this->getImageStyles() as $key => $value) : ?>
                        <option value="<?php echo $key; ?>"<?php selected($this->settings->get('image_style') == $key); ?>><?php echo $value['label']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    <p class="description"><?php _e('The image format to use. If not set no images will be displayed.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="use_https"><?php _e('Use https', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="hidden" id="hidden_use_https" name="use_https" value="0" />
                    <input type="checkbox" name="use_https"<?php checked($this->settings->get('use_https')); ?>" />
                    <p class="description"><?php _e('Ensures that all images is handled over https.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="exclude_css"><?php _e('Exclude css', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="hidden" id="hidden_exclude_css" name="exclude_css" value="0" />
                    <input type="checkbox" name="exclude_css"<?php checked($this->settings->get('exclude_css')); ?> />
                    <p class="description"><?php _e('Do not load stylesheet.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="template"><?php _e('Template', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <select name="template">
                        <option value="0"><?php _e('Select', Settings::TEXTDOMAIN); ?></option>
                        <?php foreach ($templates as $key => $template) : ?>
                            <option value="<?php echo $template; ?>"<?php selected($this->settings->get('template') === $template); ?>><?php echo $key; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('The template used for rendering the feed.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cache_expire"><?php _e('Cache lifetime', Settings::TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" min="0" name="cache_expire" value="<?php echo $this->settings->get('cache_expire'); ?>" />
                    <p class="description"><?php _e('The cache lifetime.', Settings::TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', Settings::TEXTDOMAIN), 'primary', 'cision-block-settings'); ?>
    </form>
</div>
