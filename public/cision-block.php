<?php
namespace CisionBlock;

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('CISION_BLOCK_PLUGIN_VERSION', '1.5.2');
define('CISION_BLOCK_FEED_URL', 'https://publish.ne.cision.com/papi/NewsFeed/');
define('CISION_BLOCK_FEED_RELEASE_URL', 'http://publish.ne.cision.com/papi/Release/');
define('CISION_BLOCK_DOMAIN_FILTER_REGEXP', '/^(http|https):\/\/publish\.ne\.cision\.com/');
define('CISION_BLOCK_USER_AGENT', 'cision-block/' . CISION_BLOCK_PLUGIN_VERSION);
define('CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE', 0);
define('CISION_BLOCK_DEFAULT_ITEM_COUNT', 50);
define('CISION_BLOCK_DEFAULT_CACHE_LIFETIME', 60 * 5);
define('CISION_BLOCK_DEFAULT_DATE_FORMAT', 'd-m-Y');
define('CISION_BLOCK_DEFAULT_IMAGE_STYLE', 'DownloadUrl');
define('CISION_BLOCK_MAX_ITEMS_PER_FEED', 100);
define('CISION_BLOCK_MAX_ITEMS_PER_PAGE', 100);
define('CISION_BLOCK_SETTINGS_OPTION', 'cision_block_settings');
define('CISION_BLOCK_TRANSIENT_KEY', 'cision_block_data');
define('CISION_BLOCK_DEFAULT_FEED_TYPE', 'PRM');
define('CISION_BLOCK_DEFAULT_FEED_FORMAT', 'json');
define('CISION_BLOCK_DEFAULT_DETAIL_LEVEL', 'detail');
define('CISION_BLOCK_DEFAULT_PAGE_INDEX', 1);
define('CISION_BLOCK_DEFAULT_LANGUAGE', '');
define('CISION_BLOCK_DEFAULT_READMORE_TEXT', 'Read more');
define('CISION_BLOCK_TEXTDOMAIN', 'cision-block');

require_once CISION_BLOCK_PLUGIN_DIR . '/src/Common/Singleton.php';
require_once CISION_BLOCK_PLUGIN_DIR . '/src/Config/Settings.php';
require_once CISION_BLOCK_PLUGIN_DIR . '/src/Widget/CisionBlockWidget.php';
require_once CISION_BLOCK_PLUGIN_DIR . '/admin/cision-block-admin.php';

use CisionBlock\Common\Singleton;
use CisionBlock\Config\Settings;
use CisionBlock\Widget\CisionBlockWidget;

class CisionBlock extends Singleton
{
    /**
     *
     * @var \src\config\Settings
     */
    private $settings;

    /**
     *
     * @var \src\widget\CisionBlockWidget
     */
    private $widget;

    /**
     * Constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Uninstalls the plugin.
     */
    public function delete()
    {
        $this->settings->delete();
        $this->clearCache();

        // Delete any sidebar widgets.
        $sidebars = get_option('sidebars_widgets');
        foreach ($sidebars as $sidebar_id => $sidebar) {
            if (is_array($sidebar)) {
                foreach ($sidebar as $key => $widget_id) {
                    if ($widget_id && strstr($widget_id, 'widget_cision_block_widget')) {
                        unset($sidebars[$sidebar_id][$key]);
                    }
                }
            }
        }
        //update_option('sidebars_widgets', $sidebars);
    }

    /**
     * Delete any transient cache data.
     */
    public function clearCache()
    {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM $wpdb->options WHERE `option_name` LIKE ('_transient%" .
            CISION_BLOCK_TRANSIENT_KEY .
            "%')"
        );
    }

    /**
     * Initialization.
     */
    public function initialize()
    {
        $this->settings = new Settings(CISION_BLOCK_SETTINGS_OPTION);

        // Setup widget.
        $this->widget = new CisionBlockWidget();

        add_shortcode('cision-block', array($this, 'displayFeed'));
        add_action('wp_enqueue_scripts', array($this, 'addStyles'));

        $this->addFilters();
        $this->localize();
    }

    /**
     * Register filters.
     */
    protected function addFilters()
    {
        add_filter('query_vars', array($this, 'addQueryVars'), 10, 1);
        add_filter('cision_block_pager_attributes', array($this, 'addPagerAttributes'), 10, 1);
        add_filter('cision_block_pager_active_class', array($this, 'setPagerActiveClass'), 10, 1);
        add_filter('cision_block_media_attributes', array($this, 'addMediaAttributes'), 10, 1);
        add_filter('cision_block_wrapper_attributes', array($this, 'addWrapperAttributes'), 10, 1);
        add_filter('cision_block_prefix', array($this, 'addPrefix'), 10, 1);
        add_filter('cision_block_suffix', array($this, 'addSuffix'), 10, 1);
        add_filter('cision_block_sort', array($this, 'sortFilter'), 10, 1);
    }

    /**
     * Localize plugin.
     */
    protected function localize()
    {
        load_plugin_textdomain(CISION_BLOCK_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Returns an array of all different
     * feed types.
     *
     * @return array
     */
    public function getFeedTypes()
    {
        return array(
            'KMK' => __('Annual Financial statement', CISION_BLOCK_TEXTDOMAIN),
            'RDV' => __('Annual Report', CISION_BLOCK_TEXTDOMAIN),
            'PRM' => __('Company Announcement', CISION_BLOCK_TEXTDOMAIN),
            'RPT' => __('Interim Report', CISION_BLOCK_TEXTDOMAIN),
            'INB' => __('Invitation', CISION_BLOCK_TEXTDOMAIN),
            'NBR' => __('Newsletter', CISION_BLOCK_TEXTDOMAIN),
        );
    }

    /**
     * Add custom query variables, used for pager.
     *
     * @param array $vars
     *   Array of available query variables.
     *
     * @return array
     *   Updated array of query variables.
     */
    public function addQueryVars(array $vars)
    {
        $vars[] = 'cb_id';
        $vars[] = 'cb_page';
        return $vars;
    }

    /**
     * Filter used to sort feed items.
     *
     * @param array $items
     *   A mapped array of cision feed items.
     *
     * @return array
     *   A sorted array of cision feed items.
     */
    public function sortFilter(array $items)
    {
        return $items;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function addPagerAttributes(array $attributes)
    {
        return $attributes;
    }

    /**
     *
     * @param string $class
     *
     * @return string
     */
    public function setPagerActiveClass($class)
    {
        return $class;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function addMediaAttributes(array $attributes)
    {
        return $attributes;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function addWrapperAttributes(array $attributes)
    {
        return $attributes;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function addPrefix($prefix)
    {
        return $prefix;
    }

    /**
     * @param string $suffix
     *
     * @return string
     */
    public function addSuffix($suffix)
    {
        return $suffix;
    }

    /**
     * Register stylesheet and scripts.
     */
    public function addStyles()
    {
        wp_register_style('cision-block', CISION_BLOCK_PLUGIN_URL . 'css/cision-block.css');
    }

    /**
     * @param array $atts
     *   An array of shortcode arguments.
     */
    protected function checkShortcodeAttributes(array $atts)
    {
        global $post;
        global $widget_id;

        if (isset($atts['widget'])) {
            $widget_id = $atts['widget'];
        }
        if (isset($atts['language'])) {
            $this->settings->language = $atts['language'];
        }
        if (isset($atts['readmore'])) {
            $this->settings->readmore = $atts['readmore'];
        }
        if (isset($atts['date_format'])) {
            $this->settings->date_format = $atts['date_format'];
        }
        if (isset($atts['items_per_page'])) {
            $this->settings->items_per_page = filter_var(
                $atts['items_per_page'],
                FILTER_SANITIZE_NUMBER_INT,
                array(
                    'options' => array(
                        'default' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
                        'min_range' => 0,
                        'max_range' => CISION_BLOCK_MAX_ITEMS_PER_PAGE,
                    ),
                )
            );
        }
        if (isset($atts['count'])) {
            $this->settings->count = filter_var(
                $atts['count'],
                FILTER_VALIDATE_INT,
                array(
                    'options' => array(
                        'default' => CISION_BLOCK_DEFAULT_ITEM_COUNT,
                        'min_range' => 1,
                        'max_range' => CISION_BLOCK_MAX_ITEMS_PER_FEED,
                    ),
                )
            );
        }
        if (isset($atts['types'])) {
            $this->settings->types = explode(',', str_replace(' ', '', $atts['types']));
        }
        if (isset($atts['regulatory'])) {
            $this->settings->is_regulatory = filter_var($atts['regulatory'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($atts['flush']) && filter_var($atts['flush'], FILTER_VALIDATE_BOOLEAN) == true) {
            delete_transient(CISION_BLOCK_TRANSIENT_KEY . '_' . ($widget_id ? $widget_id : $post->ID));
        }
        if (isset($atts['tags'])) {
            $this->settings->tags = $atts['tags'];
        }
        if (isset($atts['categories'])) {
            $this->settings->categories = strtolower($atts['categories']);
        }
        if (isset($atts['start'])) {
            $this->settings->start_date = $atts['start'];
        }
        if (isset($atts['end'])) {
            $this->settings->end_date = $atts['end'];
        }
        if (isset($atts['image_style'])) {
            $this->settings->image_style = $atts['image_style'];
        }
        if (!empty($atts['source_uid'])) {
            $this->settings->source_uid = filter_var(
                $atts['source_uid'],
                FILTER_SANITIZE_STRING
            );
        }
    }

    /**
     * Returns the generated markup for the feed.
     *
     * @param mixed $atts
     *   Shortcode attributes.
     *
     * @return mixed
     */
    public function displayFeed($atts)
    {
        // Add stylesheet.
        wp_enqueue_style('cision-block');

        // Reload settings since they might be overwritten.
        $this->settings->load();

        $block_id = 'cision-block';

        // There is no need to check these values if no arguments is supplied.
        if (is_array($atts)) {
            $this->checkShortcodeAttributes($atts);
            if (isset($atts['id'])) {
                $block_id = $atts['id'];
            }
        }
        $feed_items = $this->getFeed();
        $pager = $this->getPagination($feed_items);

        // Add variables to symbol table.
        extract(array(
            'cision_feed' => $feed_items,
            'pager' => $pager,
            'id' => $block_id,
            'readmore' => $this->settings->get('readmore'),
            'prefix' => apply_filters('cision_block_prefix', ''),
            'suffix' => apply_filters('cision_block_suffix', ''),
            'attributes' => $this->parseAttributes(apply_filters('cision_block_media_attributes', array(
                'class' => array(
                    'cision-feed-item',
                ),
            ))),
            'wrapper_attributes' => $this->parseAttributes(apply_filters('cision_block_wrapper_attributes', array(
                'class' => array(
                    'cision-feed-wrapper',
                ),
            ))),
            'options' => array(
                'date_format' => $this->settings->get('date_format'),
            )
        ));

        ob_start();
        $template = locate_template(array(
            'cision-block.php',
            'templates/cision-block.php'
        ));
        if ($template) {
            // Include theme overridden template.
            include $template;
        } else {
            // Include the default plugin supplied template.
            include CISION_BLOCK_PLUGIN_DIR . 'templates/cision-block.php';
        }
        return ob_get_clean();
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    protected function parseAttributes(array $attributes)
    {
        $attributeString = '';
        foreach ($attributes as $key => $attribute) {
            if (is_array($attribute)) {
                $attributeString .= ' ' . $key . '="' . implode(' ', $attribute) . '" ';
            } else {
                $attributeString .= ' ' . $key . '="' . $attribute . '" ';
            }
        }
        return rtrim($attributeString);
    }

    /**
     * Creates and returns markup for a pager.
     *
     * @param array $items
     *   Array of all feed items.
     *
     * @return string
     *   Markup for the generated pager.
     */
    protected function getPagination(array &$items)
    {
        $output = '';
        $attributes = array(
            'class' => array(
                'cision-feed-pager',
            ),
        );
        $attributes = $this->parseAttributes(apply_filters('cision_block_pager_attributes', $attributes));
        $active_class = apply_filters('cision_block_pager_active_class', 'active');
        if ($this->settings->get('items_per_page') > 0) {
            $max = (int) ceil(count($items) / $this->settings->get('items_per_page'));
            $id = get_query_var('cb_id');
            $page = (int) get_query_var('cb_page', -1);
            $active = ($id == 'cision_block' ? $page : 0);
            if ($max > 1) {
                $output = '<ul' . $attributes. '>';
                for ($i = 0; $i < $max; $i++) {
                    $output .= '<li><a href="' . add_query_arg(array('cb_id' => 'cision_block', 'cb_page' => $i)) . '"' .
                        ($active == $i ? ' class="' . $active_class . '"' : '') . '>' . ($i + 1) . '</a></li>';
                }
                if ($active >= 0 && $active < $max) {
                    $items = array_slice(
                        $items,
                        $active * $this->settings->get('items_per_page'),
                        $this->settings->get('items_per_page')
                    );
                }
                $output .= '</ul>';
            }
        }
        return $output;
    }

    /**
     * Performs a remote request.
     *
     * @param string $url
     *   A valid url.
     *
     * @return mixed
     *   The json decoded feed or null.
     */
    protected function remoteRequest($url)
    {
        $result = null;
        $response = wp_safe_remote_request($url, array(
            'headers' => array(
                'User-Agent' => CISION_BLOCK_USER_AGENT,
            ),
        ));
        if (!is_wp_error($response) && ($response['response']['code'] == 200 || $response['response']['code'] == 201)) {
            $result = json_decode($response['body']);
        }
        return $result;
    }

    /**
     * Retrieve a feed from the specified source URL.
     *
     * @return array
     *   Returns an array of feed items.
     */
    protected function getFeed()
    {
        global $post;
        global $widget_id;

        // Try to get data from transient.
        $data = get_transient(CISION_BLOCK_TRANSIENT_KEY . '_' . ($widget_id ? $widget_id : $post->ID));
        if ($data === false) {
            $params = array(
                'PageIndex' => CISION_BLOCK_DEFAULT_PAGE_INDEX,
                'PageSize' => $this->settings->get('count'),
                'detailLevel' => CISION_BLOCK_DEFAULT_DETAIL_LEVEL,
                'format' => CISION_BLOCK_DEFAULT_FEED_FORMAT,
                'tags' => $this->settings->get('tags'),
                'startDate' => $this->settings->get('start_date'),
                'endDate' => $this->settings->get('end_date'),
            );

            $response = $this->remoteRequest(
                CISION_BLOCK_FEED_URL . $this->settings->get('source_uid') . '?' . http_build_query($params)
            );

            $data = ($response ? $this->mapSources($response) : null);

            // Store transient data.
            if ($data && $this->settings->get('cache_expire') > 0) {
                set_transient(
                    CISION_BLOCK_TRANSIENT_KEY . '_' . ($widget_id ? $widget_id : $post->ID),
                    $data,
                    $this->settings->get('cache_expire')
                );
            }
        }
        return ($data ? $data : array());
    }

    /**
     * @param \stdClass $release
     * @param $image_style
     * @param bool $use_https
     *
     * @return object
     */
    protected function mapFeedItem(\stdClass $release, $image_style, $use_https = false)
    {
        $item = array();

        // Clean up data.
        $item['Title'] = sanitize_text_field($release->Title);
        $item['PublishDate'] = strtotime($release->PublishDate);
        $item['Intro'] = sanitize_text_field($release->Intro);
        $item['Body'] = sanitize_text_field($release->Body);
        $item['CisionWireUrl'] = esc_url_raw($release->CisionWireUrl);
        $item['IsRegulatory'] = (int) $release->IsRegulatory;
        if (!empty($image_style)) {
            foreach ($release->Images as $image) {
                if ($use_https) {
                    $image->{$image_style} = str_replace('http:', 'https:', $image->{$image_style});
                }
                $item['Images'][] = (object) array(
                    'DownloadUrl' => esc_url_raw($image->{$image_style}),
                    'Description' => sanitize_text_field($image->Description),
                    'Title' => sanitize_text_field($image->Title),
                );
            }
        } else {
            $release->Images = array();
        }

        // Let user modify the data.
        return (object) apply_filters('cision_map_source_item', $item, $release);
    }

    protected function hasCategory(\stdClass $item, array $categories) {
        foreach ($item->Categories as $category) {
            if (in_array(strtolower($category->Name), $categories)) {
                return true;
            }
        }
    }

    /**
     * Creates an array of feed items.
     *
     * @param \stdClass $feed
     *   A cision feed object.
     *
     * @return array
     *   An array of mapped feed items.
     */
    protected function mapSources(\stdClass $feed)
    {
        $items = null;
        $image_style = $this->settings->get('image_style');
        $use_https = $this->settings->get('use_https');
        $is_regulatory = $this->settings->get('is_regulatory');
        $language = $this->settings->get('language');
        $types = $this->settings->get('types');
        if ($this->settings->get('categories') !== '') {
            $categories = array_map('trim', explode(',', $this->settings->get('categories')));
        } else {
            $categories = array();
        }
        if (isset($feed->Releases) && count($feed->Releases)) {
            foreach ($feed->Releases as $release) {
                if (!is_object($release) || in_array($release->InformationType, $types) == false) {
                    continue;
                }
                if ($is_regulatory && (int) $release->IsRegulatory == 0) {
                    continue;
                }
                if (!empty($language) && $release->LanguageCode != $language) {
                    continue;
                }
                if (count($categories) && !$this->hasCategory($release, $categories)) {
                    continue;
                }
                $items[] = $this->mapFeedItem($release, $image_style, $use_https);
            }

            if (count($items)) {
                $items = apply_filters('cision_block_sort', $items);
            }
        }

        return $items;
    }
}
