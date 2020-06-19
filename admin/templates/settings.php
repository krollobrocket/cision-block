<div class="wrap">
    <h1><?php _e('Cision Block', CISION_BLOCK_TEXTDOMAIN); ?></h1>
    <?php $this->displayTabs(); ?>
    <form action="" method="POST">
        <?php wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="count"><?php _e('Number of feed items', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="number" min="1" max="<?php echo CISION_BLOCK_MAX_ITEMS_PER_FEED; ?>" name="count" value="<?php echo intval($this->settings->get('count')); ?>" />
                    <p class="description"><?php _e('The maximum number of items in the feed.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="source-uid"><?php _e('Cision Feed source id', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" class="regular-text" name="source-uid" value="<?php echo sanitize_text_field($this->settings->get('source_uid')); ?>"/>
                    <p class="description"><?php _e('A valid unique JSON identifier for your cision feed.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="feed-types"><?php _e('Type of feed items', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <select class="regular-text" name="feed-types[]" multiple>
                        <?php foreach (\CisionBlock\CisionBlock::getInstance()->getFeedTypes() as $key => $value) : ?>
                        <option value="<?php echo $key; ?>"<?php selected(in_array($key, $this->settings->get('types'))); ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Type of feed items to include.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="start-date"><?php _e('Start date', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="date" name="start-date" value="<?php echo $this->settings->get('start_date'); ?>" />
                    <p class="description"><?php _e('Defines the start date of the date interval the press releases and/or reports are collected from. The format is 2001-12-31.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="end-date"><?php _e('End date', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="date" name="end-date" value="<?php echo $this->settings->get('end_date'); ?>" />
                    <p class="description"><?php _e('Defines the end date of the date interval the press releases and/or reports are collected from. The format is 2001-12-31.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tags"><?php _e('Tags', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" class="regular-text" name="tags" value="<?php echo $this->settings->get('tags'); ?>">
                    <p class="description"><?php _e('Defines a filter on tags, this will return releases with these tags. One or several
                            tags can be provided separated with a comma.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tags"><?php _e('Search term', CISION_BLOCK_TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="search-term" value="<?php echo $this->settings->get('search_term'); ?>">
                    <p class="description"><?php _e('Free text search in release titles.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="categories"><?php _e('Categories', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" class="regular-text" name="categories" value="<?php echo $this->settings->get('categories'); ?>">
                    <p class="description"><?php _e('Defines a filter on categories, this will return releases with connected to these categories. One or several
                            categories can be provided separated with a comma.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="items-per-page"><?php _e('Items per page', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="number" min="0" max="<?php echo CISION_BLOCK_MAX_ITEMS_PER_PAGE; ?>" name="items-per-page" value="<?php echo $this->settings->get('items_per_page'); ?>" />
                    <p class="description"><?php _e('Number of items on each page (set to 0 to disable).', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="language"><?php _e('Language', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" name="language" value="<?php echo $this->settings->get('language'); ?>" />
                    <p class="description"><?php _e('The language code for each feed item. For example \'en\' for english.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="readmore"><?php _e('Read more text', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" name="readmore" value="<?php echo $this->settings->get('readmore'); ?>" />
                    <p class="description"><?php _e('The \'Read more\' button text. If this value is empty then the button will not be visible.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="display-mode"><?php _e('Display mode', CISION_BLOCK_TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <select name="display-mode">
                        <option value="1"<?php selected($this->settings->get('view_mode') == 1); ?>><?php _e('All', CISION_BLOCK_TEXTDOMAIN); ?></option>
                        <option value="2"<?php selected($this->settings->get('view_mode') == 2); ?>><?php _e('Regulatory', CISION_BLOCK_TEXTDOMAIN); ?></option>
                        <option value="3"<?php selected($this->settings->get('view_mode') == 3); ?>><?php _e('Non-regulatory', CISION_BLOCK_TEXTDOMAIN); ?></option>
                    </select>
                    <p class="description"><?php _e('What kind of feed items to display.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="date-format"><?php _e('Date format', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="text" name="date-format" value="<?php echo $this->settings->get('date_format'); ?>" />
                    <p class="description"><?php _e('The format to use for dates.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                    <p class="description"><?php _e('You can read more about the date/time formats: ', CISION_BLOCK_TEXTDOMAIN); ?><a target="_blank" href="http://php.net/manual/en/datetime.formats.php"><?php _e('Here', CISION_BLOCK_TEXTDOMAIN); ?></a></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="image-style"><?php _e('Image style', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <select name="image-style">
                        <option value=""><?php _e('Select', CISION_BLOCK_TEXTDOMAIN); ?></option>
                        <?php foreach ($this->getImageStyles() as $key => $value) : ?>
                        <option value="<?php echo $key; ?>"<?php selected($this->settings->get('image_style') == $key); ?>><?php echo $value['label']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    <p class="description"><?php _e('The image format to use. If not set no images will be displayed.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="use-https"><?php _e('Use https', CISION_BLOCK_TEXTDOMAIN); ?></label>
                    </th>
                <td>
                    <input type="checkbox" name="use-https"<?php checked($this->settings->get('use_https')); ?>" />
                    <p class="description"><?php _e('Ensures that all images is handled over https.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cache-expire"><?php _e('Cache lifetime', CISION_BLOCK_TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" min="0" name="cache-expire" value="<?php echo $this->settings->get('cache_expire'); ?>" />
                    <p class="description"><?php _e('The cache lifetime.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="json-markup"><?php _e('Json configuration', CISION_BLOCK_TEXTDOMAIN); ?></label>
                </th>
                <td>
                    <textarea cols="60" rows="10" name="json-markup"><?php echo $this->settings->toJson(); ?></textarea>
                    <p class="description"><?php _e('The settings in json format.', CISION_BLOCK_TEXTDOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', CISION_BLOCK_TEXTDOMAIN), 'primary', 'cision-block-settings'); ?>
    </form>
</div>
