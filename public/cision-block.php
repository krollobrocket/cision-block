<?php
namespace CisionBlock;

define('CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE', 0);
define('CISION_BLOCK_DEFAULT_ITEM_COUNT', 50);
define('CISION_BLOCK_DEFAULT_CACHE_LIFETIME', 60 * 5);
define('CISION_BLOCK_DEFAULT_DATE_FORMAT', 'd-m-Y');
define('CISION_BLOCK_DEFAULT_IMAGE_STYLE', 'DownloadUrl');
define('CISION_BLOCK_MAX_ITEMS_PER_FEED', 100);
define('CISION_BLOCK_MAX_ITEMS_PER_PAGE', 100);
define('CISION_BLOCK_DEFAULT_FEED_TYPE', 'PRM');
define('CISION_BLOCK_DEFAULT_PAGE_INDEX', 1);
define('CISION_BLOCK_DEFAULT_LANGUAGE', '');
define('CISION_BLOCK_DEFAULT_READMORE_TEXT', 'Read more');
define('CISION_BLOCK_DISPLAY_MODE_ALL', 1);
define('CISION_BLOCK_DISPLAY_MODE_REGULATORY', 2);
define('CISION_BLOCK_DISPLAY_MODE_NON_REGULATORY', 3);
define('CISION_BLOCK_DEFAULT_DISPLAY_MODE', CISION_BLOCK_DISPLAY_MODE_ALL);
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
    const FEED_DETAIL_LEVEL = 'detail';
    const FEED_FORMAT = 'json';
    const FEED_RELEASE_URL = 'http://publish.ne.cision.com/papi/Release/';
    const FEED_URL = 'https://publish.ne.cision.com/papi/NewsFeed/';
    const SETTINGS_NAME = 'cision_block_settings';
    const TRANSIENT_KEY = 'cision_block_data';
    const USER_AGENT = 'cision-block/' . self::VERSION;
    const VERSION = '2.2.2';

    /**
     *
     * @var \CisionBlock\Config\Settings
     */
    private $settings;

    /**
     *
     * @var \CisionBlock\Widget\CisionBlockWidget
     */
    private $widget;

    /**
     * @var string
     */
    private $current_block_id;

    /**
     * Uninstalls the plugin.
     */
    public static function delete()
    {
        delete_option(self::SETTINGS_NAME);
        self::clearCache();

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
    public static function clearCache()
    {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM $wpdb->options WHERE `option_name` LIKE ('_transient%" .
            self::TRANSIENT_KEY .
            "%')"
        );
    }

    /**
     * Initialization.
     */
    public function init()
    {
        $this->settings = new Settings(self::SETTINGS_NAME);

        // Setup widget.
        $this->widget = new CisionBlockWidget();

        add_shortcode('cision-block', array($this, 'displayFeed'));

        $this->addActions();
        $this->addFilters();
        $this->localize();
    }

    /**
     * Clears transient based on block_id and page_id.
     *
     * @param int $post_ID
     * @param string $content
     */
    protected function checkTransient($post_ID, $content)
    {
        if (has_shortcode($content, 'cision-block')) {
            $regex = get_shortcode_regex();
            $matches = array();
            $block_id = 'cision_block';
            if (preg_match_all('/' . $regex . '/', $content, $matches) &&
                array_key_exists(2, $matches)) {
                foreach ($matches[2] as $key => $match) {
                    if ($match == 'cision-block') {
                        if (array_key_exists(3, $matches)) {
                            $atts = shortcode_parse_atts($matches[3][$key]);
                            if (isset($atts['id'])) {
                                $block_id = $atts['id'];
                            }
                        }
                        delete_transient(self::TRANSIENT_KEY . '_' . $block_id . '_' . $post_ID);
                    }
                }
            }
        }
    }

    /**
     * Sets the page title when visiting a press release.
     *
     * @param string $title
     * @global \stdClass $CisionItem
     *
     * @return string
     */
    public function setTitle($title)
    {
        global $CisionItem;
        if (get_query_var('cision_release_id')) {
            return get_bloginfo('name') . ' | ' . ($CisionItem ? $CisionItem->Title : __('Not found', CISION_BLOCK_TEXTDOMAIN));
        }

        return $title;
    }

    /**
     * Include custom template if needed.
     *
     * @global \stdClass $CisionItem
     * @global WP_Query $wp_query
     */
    public function addTemplate()
    {
        global $CisionItem;
        global $wp_query;
        if (get_query_var('cision_release_id')) {
            $release_id = get_query_var('cision_release_id');
            $response = $this->remoteRequest(self::FEED_RELEASE_URL . $release_id);

            if ($response) {
                $CisionItem = $response->Release;

                // We remove all inline styles here.
                $CisionItem->HtmlBody = preg_replace(
                    '/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i',
                    '$1$3',
                    $CisionItem->HtmlBody
                );
            } else {
                // Return a 404 page.
                $wp_query->set_404();
                status_header(404);
                return;
            }

            add_filter('template_include', function () {
                $template = locate_template(array(
                    'cision-block-post.php',
                    'templates/cision-block-post.php'
                ));
                if ($template) {
                    // Include theme overridden template.
                    return $template;
                } else {
                    // Include the default plugin supplied template.
                    return CISION_BLOCK_PLUGIN_DIR . 'templates/cision-block-post.php';
                }
            });
        }
    }

    /**
     * Add rewrite rules.
     */
    public function addRewriteRules()
    {
        if ($this->settings->get('internal_links')) {
            add_rewrite_endpoint(
                $this->settings->get('base_slug'),
                EP_ROOT,
                'cision_release_id'
            );
        }
        // Flush rewrite rules if needed.
        if (get_transient('cision_block_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_transient('cision_block_flush_rewrite_rules');
        }
    }

    /**
     * Triggered when a post is updated.
     *
     * @param int $post_ID
     * @param object $post_after
     * @param object $post_before
     */
    public function postUpdated($post_ID, $post_after, $post_before)
    {
        $this->checkTransient($post_ID, $post_before->post_content);
        $this->checkTransient($post_ID, $post_after->post_content);
    }

    /**
     * Register actions.
     */
    protected function addActions()
    {
        add_action('wp_enqueue_scripts', array($this, 'addStyles'));
        add_action('post_updated', array($this, 'postUpdated'), 10, 3);
        add_action('init', array($this, 'addRewriteRules'));
        add_action('template_redirect', array($this, 'addTemplate'));
        add_action('after_setup_theme', array($this, 'setTheme'));
    }

    /**
     * Register filters.
     */
    protected function addFilters()
    {
        add_filter('query_vars', array($this, 'addQueryVars'), 10);
        add_filter('pre_get_document_title', array($this, 'setTitle'), 10, 2);
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
     * Register stylesheet and scripts.
     */
    public function addStyles()
    {
        wp_register_style('cision-block', CISION_BLOCK_PLUGIN_URL . 'css/cision-block.css');
    }

    /**
     * Triggered after we have switched theme.
     */
    public function setTheme()
    {
        set_transient('cision_block_flush_rewrite_rules', 1);
    }

    /**
     * @param array $atts
     *   An array of shortcode arguments.
     */
    protected function checkShortcodeAttributes(array $atts)
    {
        global $post;
        global $widget_id;

        if (isset($atts['id'])) {
            $this->current_block_id = $atts['id'];
        }
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
        if (isset($atts['view'])) {
            $this->settings->view_mode = filter_var($atts['view'], FILTER_VALIDATE_INT);
        }
        if (isset($atts['regulatory']) && filter_var($atts['regulatory'], FILTER_VALIDATE_BOOLEAN)) {
            // This is a fallback for old argument
            $this->settings->view_mode = 2;
        }
        if (isset($atts['flush']) && filter_var($atts['flush'], FILTER_VALIDATE_BOOLEAN)) {
            delete_transient(self::TRANSIENT_KEY . '_' . $this->current_block_id . '_' . ($widget_id ? $widget_id : $post->ID));
        }
        if (isset($atts['tags'])) {
            $this->settings->tags = $atts['tags'];
        }
        if (isset($atts['search_term'])) {
            $this->settings->search_term = $atts['search_term'];
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

        $this->current_block_id = 'cision-block';

        // There is no need to check these values if no arguments is supplied.
        if (is_array($atts)) {
            $this->checkShortcodeAttributes($atts);
        }
        $feed_items = $this->getFeed();
        $pager = $this->getPagination($feed_items);

        // Add variables to symbol table.
        extract(array(
            'cision_feed' => $feed_items,
            'pager' => $pager,
            'id' => $this->current_block_id,
            'readmore' => $this->settings->get('readmore'),
            'prefix' => apply_filters('cision_block_prefix', '', $this->current_block_id),
            'suffix' => apply_filters('cision_block_suffix', '', $this->current_block_id),
            'attributes' => $this->parseAttributes(apply_filters('cision_block_media_attributes', array(
                'class' => array(
                    'cision-feed-item',
                ),
            ), $this->current_block_id)),
            'wrapper_attributes' => $this->parseAttributes(apply_filters('cision_block_wrapper_attributes', array(
                'class' => array(
                    'cision-feed-wrapper',
                ),
            ), $this->current_block_id)),
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
        $attributes = $this->parseAttributes(apply_filters('cision_block_pager_attributes', $attributes, $this->current_block_id));
        $active_class = apply_filters('cision_block_pager_active_class', 'active', $this->current_block_id);
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
                'User-Agent' => self::USER_AGENT,
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
        $data = get_transient(self::TRANSIENT_KEY . '_' . $this->current_block_id . '_' . ($widget_id ? $widget_id : $post->ID));
        if ($data === false) {
            $params = array(
                'PageIndex' => CISION_BLOCK_DEFAULT_PAGE_INDEX,
                'PageSize' => $this->settings->get('count'),
                'DetailLevel' => self::FEED_DETAIL_LEVEL,
                'Format' => self::FEED_FORMAT,
                'Tags' => $this->settings->get('tags'),
                'StartDate' => $this->settings->get('start_date'),
                'EndDate' => $this->settings->get('end_date'),
                'SearchTerm' => $this->settings->get('search_term'),
                'Regulatory' =>
                $this->settings->get('view_mode') === CISION_BLOCK_DISPLAY_MODE_REGULATORY ?
                'true' :
                ($this->settings->get('view_mode') === CISION_BLOCK_DISPLAY_MODE_NON_REGULATORY ? 'false' : null),
            );
            $response = $this->remoteRequest(
                self::FEED_URL . $this->settings->get('source_uid') . '?' . http_build_query($params)
            );
            $data = ($response ? $this->mapSources($response) : null);

            // Store transient data.
            if ($data && $this->settings->get('cache_expire') > 0) {
                set_transient(
                    self::TRANSIENT_KEY . '_' . $this->current_block_id . '_' . ($widget_id ? $widget_id : $post->ID),
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
        if ($this->settings->get('internal_links')) {
            $item['CisionWireUrl'] = get_bloginfo('url') . '/' . $this->settings->get('base_slug') . '/' . $release->EncryptedId;
            $item['LinkTarget'] = '_self';
        } else {
            $item['CisionWireUrl'] = esc_url_raw($release->CisionWireUrl);
            $item['LinkTarget'] = '_blank';
        }
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
        return (object) apply_filters('cision_map_source_item', $item, $release, $this->current_block_id);
    }

    /**
     * Check if an item is connected to any category.
     *
     * @param \stdClass $item
     * @param array $categories
     *
     * @return bool
     */
    protected function hasCategory(\stdClass $item, array $categories)
    {
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
        $items = array();
        $image_style = $this->settings->get('image_style');
        $use_https = $this->settings->get('use_https');
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
                if (!empty($language) && $release->LanguageCode != $language) {
                    continue;
                }
                if (count($categories) && !$this->hasCategory($release, $categories)) {
                    continue;
                }
                $items[] = $this->mapFeedItem($release, $image_style, $use_https);
            }

            if (count($items)) {
                $items = apply_filters('cision_block_sort', $items, $this->current_block_id);
            }
        }

        return count($items) ? $items : null;
    }
}
