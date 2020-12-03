<?php

namespace CisionBlock\Widget;

use CisionBlock\Frontend\Frontend;
use CisionBlock\Backend\Backend;
use CisionBlock\Config\Settings;

class Widget extends Base\Widget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'cision_block_widget';
        $this->description = __('Display pressreleases from cision.', Settings::TEXTDOMAIN);
        $this->title = __('Cision Block', Settings::TEXTDOMAIN);
        parent::__construct();
    }

    /**
     * Render the widget content.
     *
     * @param array $instance
     */
    public function render($instance)
    {
        $args = array(
            'source_uid' => $instance['source'],
            'count' => $instance['count'],
            'items_per_page' => $instance['items_per_page'],
            'flush' => ($instance['cache_expire'] == 0 ? 'true' : 'false'),
            'view' => $instance['view_mode'],
            'types' => implode($instance['types'], ', '),
            'tags' => $instance['tags'],
            'start' => $instance['start_date'],
            'end' => $instance['end_date'],
            'image_style' => $instance['image_style'],
            'language' => $instance['language'],
            'readmore' => isset($instance['readmore']) ? $instance['readmore'] : null,
            'date_format' => $instance['date_format'],
            'widget' => $this->id,
            // Always disable filters and marked releases here.
            'mark_regulatory' => false,
            'show_filters' => false,
        );

        $shortcode_args = '';
        foreach ($args as $key => $value) {
            $shortcode_args .= $key . '="' . $value . '" ';
        }
        $shortcode_args = rtrim($shortcode_args);

        echo do_shortcode('[cision-block ' . $shortcode_args . ']');
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        // Make sure so keys are set.
        if (empty($instance)) {
            $instance = array(
                'count' => CISION_BLOCK_DEFAULT_ITEM_COUNT,
                'source' => '',
                'types' => array(CISION_BLOCK_DEFAULT_FEED_TYPE),
                'tags' => '',
                'items_per_page' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
                'view_mode' => 1,
                'cache_expire' => CISION_BLOCK_DEFAULT_CACHE_LIFETIME,
                'start_date' => '',
                'end_date' => '',
                'image_style' => CISION_BLOCK_DEFAULT_IMAGE_STYLE,
                'language' => '',
                'readmore' => CISION_BLOCK_DEFAULT_READMORE_TEXT,
                'date_format' => CISION_BLOCK_DEFAULT_DATE_FORMAT,
            );
        }

        $feed_types = Frontend::getInstance()->getFeedTypes();
        $type_options = '';
        foreach ($feed_types as $key => $value) {
            $type_options .= '<option value="' . $key . '"' .
                selected(in_array($key, $instance['types'])) . '>' .
                $value . '</option>';
        }

        $image_styles = Backend::getInstance()->getImageStyles();
        $image_style_options = '';
        foreach ($image_styles as $key => $image_style) {
            $image_style_options .= '<option value="' . $key . '"' .
                selected($key == $instance['image_style']) .
                '>' . $image_style['label'] . '</option>';
        }

        $output = '';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('source') . '">' . __('Cision Feed source id', Settings::TEXTDOMAIN) . '</label>';
        $output .= '<input type="text" class="widefat" name="' . $this->get_field_name('source') . '" value="' . sanitize_text_field($instance['source']) . '"/>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('count') . '">' . __('Number of feed items', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input class="tiny-text" type="number" min="1" max="' . CISION_BLOCK_MAX_ITEMS_PER_FEED . '" name="' . $this->get_field_name('count') . '" value="' . $instance['count'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('items_per_page') . '">' . __('Items per page', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input class="tiny-text" type="number" min="0" max="' . CISION_BLOCK_MAX_ITEMS_PER_PAGE . '" name="' . $this->get_field_name('items_per_page') . '" value="' . $instance['items_per_page'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('types') . '">' . __('Type of feed items', Settings::TEXTDOMAIN) . '</label>';
        $output .= '<select class="widefat" name="' . $this->get_field_name('types') . '[]" multiple>';
        $output .= $type_options;
        $output .= '</select>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('start_date') . '">' . __('Start date', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="date" name="' . $this->get_field_name('start_date') . '" value="' . $instance['start_date'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('end_date') . '">' . __('End date', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="date" name="' . $this->get_field_name('end_date') . '" value="' . $instance['end_date'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('tags') . '">' . __('Tags', Settings::TEXTDOMAIN) . '</label>';
        $output .= '<input type="text" class="widefat" name="' . $this->get_field_name('tags') . '" value="' . $instance['tags'] . '">';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('readmore') . '">' . __('Read more text', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('readmore') . '" value="' . $instance['readmore'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('language') . '">' . __('Language', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('language') . '" value="' . $instance['language'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('view_mode') . '">' . __('View mode', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<select class="widefat" name=' . $this->get_field_name('view_mode') . '">';
        $output .= '<option value="1"' . selected($instance['view_mode'] == 1) . '>' . __('All', Settings::TEXTDOMAIN) . '</option>';
        $output .= '<option value="2"' . selected($instance['view_mode'] == 2) . '>' . __('Regulatory', Settings::TEXTDOMAIN) . '</option>';
        $output .= '<option value="3"' . selected($instance['view_mode'] == 3) . '>' . __('Non-regulatory', Settings::TEXTDOMAIN) . '</option>';
        $output .= '</select>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('date_format') . '">' . __('Date format', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('date_format') . '" value="' . $instance['date_format'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('image_style') . '">' . __('Image style', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<select class="widefat" name=' . $this->get_field_name('image_style') . '">';
        $output .= '<option value="">' . __('Select', Settings::TEXTDOMAIN) . '</option>';
        $output .= $image_style_options;
        $output .= '</select>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('cache_expire') . '">' . __('Cache lifetime', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input class="tiny-text" type="number" min="0" name="' . $this->get_field_name('cache_expire') . '" value="' . $instance['cache_expire'] . '" />';
        $output .= '</p>';
        echo $output;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();

        // Filter and sanitize form values.
        $instance['count'] = isset($new_instance['count']) ? (int)$new_instance['count'] : 0;
        if ($instance['count'] < 1 || $instance['count'] > CISION_BLOCK_MAX_ITEMS_PER_FEED) {
            $instance['count'] = CISION_BLOCK_DEFAULT_ITEM_COUNT;
        }
        $instance['source'] = isset($new_instance['source']) ? $new_instance['source'] : '';
        $instance['types'] = array();
        if (!isset($new_instance['types'])) {
            // Set default options.
            $instance['types'] = array(CISION_BLOCK_DEFAULT_FEED_TYPE);
        } else {
            $types = $new_instance['types'];
            $all_feed_types = array_keys(Frontend::getInstance()->getFeedTypes());
            $feed_types = array();
            foreach ($types as $type) {
                if (in_array($type, $all_feed_types)) {
                    $feed_types[] = $type;
                }
            }
            $instance['types'] = $feed_types;
        }
        $instance['tags'] = isset($new_instance['tags']) ? $new_instance['tags'] : '';
        $instance['items_per_page'] = isset($new_instance['items_per_page']) ? (int)$new_instance['items_per_page'] : 0;
        if ($instance['items_per_page'] < 0 || $instance['items_per_page'] > CISION_BLOCK_MAX_ITEMS_PER_PAGE) {
            $instance['items_per_page'] = CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE;
        }
        $instance['view_mode'] = isset($new_instance['view_mode']) ? (int)$new_instance['view_mode'] : 1;
        $instance['cache_expire'] = isset($new_instance['cache_expire']) ? (int)$new_instance['cache_expire'] : CISION_BLOCK_DEFAULT_CACHE_LIFETIME;
        $instance['start_date'] = isset($new_instance['start_date']) ? $new_instance['start_date'] : '';
        $instance['end_date'] = isset($new_instance['end_date']) ? $new_instance['end_date'] : '';
        $instance['image_style'] = isset($new_instance['image_style']) ? $new_instance['image_style'] : CISION_BLOCK_DEFAULT_IMAGE_STYLE;
        $instance['language'] = isset($new_instance['language']) ? $new_instance['language'] : '';
        $instance['readmore'] = isset($new_instance['readmore']) ? $new_instance['readmore'] : null;
        $instance['date_format'] = isset($new_instance['date_format']) ? $new_instance['date_format'] : '';

        return $instance;
    }
}
