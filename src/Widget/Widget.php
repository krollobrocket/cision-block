<?php

namespace CisionBlock\Widget;

use Cassandra\Set;
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
            'mark_regulatory' => $instance['mark_regulatory'],
            'regulatory_text' => $instance['regulatory_text'],
            'non_regulatory_text' => $instance['non_regulatory_text'],
            'show_filters' => $instance['show_filters'],
            'filter_all_text' => $instance['filter_all_text'],
            'filter_regulatory_text' => $instance['filter_regulatory_text'],
            'filter_non_regulatory_text' => $instance['filter_non_regulatory_text'],
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
        $defaults = array(
            'count' => Settings::DEFAULT_ITEM_COUNT,
            'source' => '',
            'types' => array(Settings::DEFAULT_FEED_TYPE),
            'tags' => '',
            'items_per_page' => Settings::DEFAULT_ITEMS_PER_PAGE,
            'view_mode' => Settings::DEFAULT_DISPLAY_MODE,
            'cache_expire' => Settings::DEFAULT_CACHE_LIFETIME,
            'start_date' => '',
            'end_date' => '',
            'image_style' => Settings::DEFAULT_IMAGE_STYLE,
            'language' => '',
            'readmore' => Settings::DEFAULT_READ_MORE_TEXT,
            'date_format' => Settings::DEFAULT_DATE_FORMAT,
            'mark_regulatory' => false,
            'regulatory_text' => Settings::DEFAULT_MARK_REGULATORY_TEXT,
            'non_regulatory_text' => Settings::DEFAULT_FILTER_NON_REGULATORY_TEXT,
            'show_filters' => false,
            'filter_all_text' => Settings::DEFAULT_FILTER_ALL_TEXT,
            'filter_regulatory_text' => Settings::DEFAULT_FILTER_REGULATORY_TEXT,
            'filter_non_regulatory_text' => Settings::DEFAULT_FILTER_NON_REGULATORY_TEXT,
        );
        foreach ($defaults as $key => $value) {
            if (!isset($instance[$key])) {
                $instance[$key] = $value;
            }
        }

        $feed_types = Frontend::getInstance()->getFeedTypes();
        $type_options = '';
        foreach ($feed_types as $key => $value) {
            $type_options .= '<option value="' . $key . '"' .
                selected(in_array($key, $instance['types']), true, false) . '>' .
                $value . '</option>';
        }

        $image_styles = Backend::getInstance()->getImageStyles();
        $image_style_options = '';
        foreach ($image_styles as $key => $image_style) {
            $image_style_options .= '<option value="' . $key . '"' .
                selected($key == $instance['image_style'], true, false) .
                '>' . $image_style['label'] . '</option>';
        }

        $output = '';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('source') . '">' . __('Cision Feed source id', Settings::TEXTDOMAIN) . '</label>';
        $output .= '<input type="text" class="widefat" name="' . $this->get_field_name('source') . '" value="' . sanitize_text_field($instance['source']) . '"/>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('count') . '">' . __('Number of feed items', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input class="tiny-text" type="number" min="1" max="' . Settings::MAX_ITEMS_PER_FEED . '" name="' . $this->get_field_name('count') . '" value="' . $instance['count'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('items_per_page') . '">' . __('Items per page', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input class="tiny-text" type="number" min="0" max="' . Settings::MAX_ITEMS_PER_PAGE . '" name="' . $this->get_field_name('items_per_page') . '" value="' . $instance['items_per_page'] . '" />';
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
        $output .= '<label for="' . $this->get_field_id('mark_regulatory') . '">' . __('Show regulatory/non-regulatory', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="checkbox" name="' . $this->get_field_name('mark_regulatory') . '"' . checked($instance['mark_regulatory'], true, false) . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('regulatory_text') . '">' . __('Regulatory item text', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('regulatory_text') . '" value="' . $instance['regulatory_text'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('non_regulatory_text') . '">' . __('Non-regulatory item text', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('non_regulatory_text') . '" value="' . $instance['non_regulatory_text'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('show_filters') . '">' . __('Show filters', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="checkbox" name="' . $this->get_field_name('show_filters') . '"' . checked($instance['show_filters'], true, false) . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('filter_all_text') . '">' . __('All item filter text', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('filter_all_text') . '" value="' . $instance['filter_all_text'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('filter_regulatory_text') . '">' . __('Regulatory item filter text', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('filter_regulatory_text') . '" value="' . $instance['filter_regulatory_text'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('filter_non_regulatory_text') . '">' . __('Non-regulatory item filter text', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<input type="text" name="' . $this->get_field_name('filter_non_regulatory_text') . '" value="' . $instance['filter_non_regulatory_text'] . '" />';
        $output .= '</p>';
        $output .= '<p>';
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
        $output .= '<select name="' . $this->get_field_name('language') . '">';
        $output .= '<option>' . __('Select', Settings::TEXTDOMAIN) . '</option>';
        foreach (Backend::getInstance()->getLanguages() as $key => $value) :
            $output .= '<option value="' . $key . '"' . selected($instance['language'] === $key, true, false) . '>' . $value . '</option>';
        endforeach;
        $output .= '</select>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label for="' . $this->get_field_id('view_mode') . '">' . __('View mode', Settings::TEXTDOMAIN) . ': </label>';
        $output .= '<select class="widefat" name=' . $this->get_field_name('view_mode') . '">';
        $output .= '<option value="1"' . selected($instance['view_mode'] == 1, true, false) . '>' . __('All', Settings::TEXTDOMAIN) . '</option>';
        $output .= '<option value="2"' . selected($instance['view_mode'] == 2, true, false) . '>' . __('Regulatory', Settings::TEXTDOMAIN) . '</option>';
        $output .= '<option value="3"' . selected($instance['view_mode'] == 3, true, false) . '>' . __('Non-regulatory', Settings::TEXTDOMAIN) . '</option>';
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
        $config = Frontend::getInstance()->verifySettings($new_instance);
        $config['source'] = $config['source_uid'];
        return $config;
    }
}
