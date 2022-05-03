<?php

namespace CisionBlock\Frontend;

use CisionBlock\Plugin\Common\Singleton;
use CisionBlock\Plugin\Http\RemoteRequest;
use CisionBlock\Settings\Settings;
use CisionBlock\Widget\Widget;
use stdClass;

class Frontend extends Singleton
{
    const FEED_DETAIL_LEVEL = 'detail';
    const FEED_FORMAT = 'json';
    const FEED_RELEASE_URL = 'http://publish.ne.cision.com/papi/Release/';
    const FEED_URL = 'https://publish.ne.cision.com/papi/NewsFeed/';
    const SETTINGS_NAME = 'cision_block_settings';
    const TRANSIENT_KEY = 'cision_block_data';
    const USER_AGENT = 'cision-block/' . self::VERSION;
    const VERSION = '2.7.1';

    /**
     *
     * @var Settings
     */
    private static $settings;

    /**
     *
     * @var RemoteRequest
     */
    private $request;

    /**
     * @var string
     */
    private static $current_block_id;

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
        self::$settings = new Settings(self::SETTINGS_NAME);
        $this->request = new RemoteRequest();
        $this->request->setHeaders(array(
            'User-agent' => self::USER_AGENT,
        ));

        // Setup widget.
        new Widget();

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
                array_key_exists(2, $matches)
            ) {
                foreach ($matches[2] as $key => $match) {
                    if ($match === 'cision-block') {
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
     * @global stdClass $CisionItem
     *
     * @return string
     */
    public function setTitle($title)
    {
        global $CisionItem;
        if (get_query_var('cision_release_id')) {
            return get_bloginfo('name') . ' | ' . ($CisionItem ? $CisionItem->Title : __('Not found', 'cision-block'));
        }

        return $title;
    }

    /**
     * Include custom template if needed.
     *
     * @global stdClass $CisionItem
     * @global WP_Query $wp_query
     */
    public function addTemplate()
    {
        global $CisionItem;
        global $displayFiles;
        global $wp_query;
        if (get_query_var('cision_release_id')) {
            $release_id = get_query_var('cision_release_id');
            try {
                $response = $this->request->get(self::FEED_RELEASE_URL . $release_id)->toJSON();
            } catch (\Exception $e) {
                // Return a 404 page.
                $wp_query->set_404();
                status_header(404);
                return;
            }
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

            $displayFiles = self::$settings->get('show_files', false);
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
                    return __DIR__ . '/templates/cision-block-post.php';
                }
            });
        }
    }

    /**
     * Add rewrite rules.
     */
    public function addRewriteRules()
    {
        if (self::$settings->get('internal_links')) {
            add_rewrite_endpoint(
                self::$settings->get('base_slug'),
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
        load_plugin_textdomain('cision-block');
    }

    /**
     * Returns an array of all different
     * feed types.
     *
     * @return array
     */
    public static function getFeedTypes()
    {
        return array(
            'KMK' => __('Annual Financial statement', 'cision-block'),
            'RDV' => __('Annual Report', 'cision-block'),
            'PRM' => __('Company Announcement', 'cision-block'),
            'RPT' => __('Interim Report', 'cision-block'),
            'INB' => __('Invitation', 'cision-block'),
            'NBR' => __('Newsletter', 'cision-block'),
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
        return array_merge($vars, array('cb_id', 'cb_page'));
    }

    /**
     * Register stylesheet and scripts.
     */
    public function addStyles()
    {
        wp_register_style(
            'cision-block',
            $this->getPluginUrl('css/cision-block.css'),
            array(),
            self::VERSION
        );
        wp_enqueue_script(
            'cision-block',
            $this->getPluginUrl('js/cision-block.js'),
            array('jquery'),
            self::VERSION,
            true
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public function getPluginUrl($path)
    {
        return plugin_dir_url(__FILE__) . $path;
    }

    /**
     * Triggered after we have switched theme.
     */
    public function setTheme()
    {
        set_transient('cision_block_flush_rewrite_rules', 1);
    }

    /**
     * @return Settings
     */
    public static function getSettings()
    {
        $settings = self::$settings ?: new Settings(self::SETTINGS_NAME);
        return $settings;
    }

    /**
     * @param array $settings
     * @param Settings $options
     * @return array
     */
    public static function verifySettings(array $settings, Settings $options)
    {
        global $post;
        global $widget_id;

        $mapping = array(
            // general
            'count' => 'count',
            'cache_expire' => 'cache_expire',
            'source_uid' => 'source_uid',
            'types' => 'types',
            'language' => 'language',
            'readmore' => 'readmore',
            'start' => 'start_date',
            'end' => 'end_date',
            'mark_regulatory' => 'mark_regulatory',
            'regulatory_text' => 'regulatory_text',
            'non_regulatory_text' => 'non_regulatory_text',
            'date_format' => 'date_format',
            'tags' => 'tags',
            'search_term' => 'search_term',
            'categories' => 'categories',
            'view' => 'view_mode',
            'use_https' => 'use_https',
            'image_style' => 'image_style',
            'show_excerpt' => 'show_excerpt',
            'show_files' => 'show_files',
            'items_per_page' => 'items_per_page',
            'exclude_css' => 'exclude_css',
            'template' => 'template',

            // permalinks
            'internal_links' => 'internal_links',
            'base_slug' => 'base_slug',

            // filters
            'show_filters' => 'show_filters',
            'filter_all_text' => 'filter_all_text',
            'filter_regulatory_text' => 'filter_regulatory_text',
            'filter_non_regulatory_text' => 'filter_non_regulatory_text',

            // special for shortcode attrib
            'regulatory' => 'view_mode',
            'flush' => 'flush',
            'id' => 'id',

            // special for widget
            'widget' => 'widget',
            'source' => 'source_uid',
            'view_mode' => 'view_mode',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
        );

        $result = array(
            'show_excerpt' => $options->get('show_excerpt'),
            'show_files' => $options->get('show_files'),
            'use_https' => $options->get('use_https'),
            'mark_regulatory' => $options->get('mark_regulatory'),
            'show_filters' => $options->get('show_filters'),
            'internal_links' => $options->get('internal_links'),
            'exclude_css' => $options->get('exclude_css'),
        );

        foreach ($settings as $name => $value) {
            switch ($name) {
                case 'count':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_VALIDATE_INT,
                        array(
                            'options' => array(
                                'default' => Settings::DEFAULT_ITEM_COUNT,
                                'min_range' => 1,
                                'max_range' => Settings::MAX_ITEMS_PER_FEED,
                            ),
                        )
                    );
                    break;
                case 'cache_expire':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_SANITIZE_NUMBER_INT
                    );
                    break;
                case 'types':
                    if (is_string($value)) {
                        $result[$mapping[$name]] = explode(',', str_replace(' ', '', $value));
                    } else {
                        $result[$mapping[$name]] = filter_var(
                            $value,
                            FILTER_SANITIZE_STRING,
                            FILTER_REQUIRE_ARRAY
                        );
                    }
                    break;
                case 'template':
                    include_once ABSPATH . '/wp-admin/includes/theme.php';
                    $template = filter_var(
                        $value,
                        FILTER_SANITIZE_STRING
                    );
                    $templates = get_page_templates();
                    if (array_search($template, $templates) !== false) {
                        $result[$mapping[$name]] = $template;
                    } elseif (array_key_exists($template, $templates)) {
                        $result[$mapping[$name]] = $templates[$template];
                    } else {
                        $result[$mapping[$name]] = null;
                    }
                    break;
                case 'source':
                case 'source_uid':
                case 'readmore':
                case 'start':
                case 'end':
                case 'start_date':
                case 'end_date':
                case 'date_format':
                case 'tags':
                case 'search_term':
                case 'image_style':
                case 'base_slug':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_SANITIZE_STRING
                    );
                    break;
                case 'language':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_SANITIZE_STRING
                    );
                    $result[$mapping[$name]] = strtolower($result[$mapping[$name]]);
                    break;
                case 'categories':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_SANITIZE_STRING
                    );
                    $result[$mapping[$name]] = trim(strtolower($result[$mapping[$name]]));
                    break;
                case 'show_excerpt':
                case 'show_files':
                case 'mark_regulatory':
                case 'use_https':
                case 'internal_links':
                case 'show_filters':
                case 'exclude_css':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_VALIDATE_BOOLEAN
                    );
                    break;
                case 'regulatory_text':
                    $text = filter_var(
                        $value,
                        FILTER_SANITIZE_SPECIAL_CHARS
                    );
                    $result[$mapping[$name]] = $text ?: Settings::DEFAULT_MARK_REGULATORY_TEXT;
                    break;
                case 'non_regulatory_text':
                    $text = filter_var(
                        $value,
                        FILTER_SANITIZE_SPECIAL_CHARS
                    );
                    $result[$mapping[$name]] = $text ?: Settings::DEFAULT_MARK_NON_REGULATORY_TEXT;
                    break;
                case 'filter_all_text':
                    $text = filter_var(
                        $value,
                        FILTER_SANITIZE_SPECIAL_CHARS
                    );
                    $result[$mapping[$name]] = $text ?: Settings::DEFAULT_FILTER_ALL_TEXT;
                    break;
                case 'filter_regulatory_text':
                    $text = filter_var(
                        $value,
                        FILTER_SANITIZE_SPECIAL_CHARS
                    );
                    $result[$mapping[$name]] = $text ?: Settings::DEFAULT_FILTER_REGULATORY_TEXT;
                    break;
                case 'filter_non_regulatory_text':
                    $text = filter_var(
                        $value,
                        FILTER_SANITIZE_SPECIAL_CHARS
                    );
                    $result[$mapping[$name]] = $text ?: Settings::DEFAULT_FILTER_NON_REGULATORY_TEXT;
                    break;
                case 'view':
                case 'view_mode':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_VALIDATE_INT,
                        array(
                            'default' => 1,
                            'min_range' => 1,
                            'max_range' => 3,
                        )
                    );
                    break;
                case 'items_per_page':
                    $result[$mapping[$name]] = filter_var(
                        $value,
                        FILTER_VALIDATE_INT,
                        array(
                            'options' => array(
                                'default' => Settings::DEFAULT_ITEMS_PER_PAGE,
                                'min_range' => 0,
                                'max_range' => Settings::MAX_ITEMS_PER_PAGE,
                            ),
                        )
                    );
                    break;
                case 'regulatory':
                    // This is a fallback for old argument
                    $result[$mapping[$name]] = Settings::DISPLAY_MODE_REGULATORY;
                    break;
                case 'id':
                    self::$current_block_id = $value;
                    break;
                case 'widget':
                    $widget_id = $value;
                    break;
                case 'flush':
                    if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                        delete_transient(self::TRANSIENT_KEY . '_' . self::$current_block_id . '_' . ($widget_id ?: $post->ID));
                    }
            }
        }

        return $result;
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
        // Reload settings since they might be overwritten.
        self::$settings->load();

        self::$current_block_id = 'cision-block';

        // There is no need to check these values if no arguments is supplied.
        if (is_array($atts)) {
            $verified = $this->verifySettings($atts, self::$settings);
            self::$settings->setFromArray($verified);
        }

        // Add stylesheet.
        if (!self::$settings->get('exclude_css')) {
            wp_enqueue_style('cision-block');
        }

        $feed_items = $this->getFeed();
        $pager = $this->getPagination($feed_items);

        // Add variables to symbol table.
        extract(array(
            'cision_feed' => $feed_items,
            'pager' => $pager,
            'id' => self::$current_block_id,
            'readmore' => self::$settings->get('readmore'),
            'mark_regulatory' => self::$settings->get('mark_regulatory'),
            'regulatory_text' => htmlspecialchars_decode(self::$settings->get('regulatory_text')),
            'non_regulatory_text' => htmlspecialchars_decode(self::$settings->get('non_regulatory_text')),
            'show_filters' => self::$settings->get('show_filters'),
            'show_excerpt' => self::$settings->get('show_excerpt'),
            'filter_all_text' => htmlspecialchars_decode(self::$settings->get('filter_all_text')),
            'filter_regulatory_text' => htmlspecialchars_decode(self::$settings->get('filter_regulatory_text')),
            'filter_non_regulatory_text' => htmlspecialchars_decode(self::$settings->get('filter_non_regulatory_text')),
            'prefix' => apply_filters('cision_block_prefix', '', self::$current_block_id),
            'suffix' => apply_filters('cision_block_suffix', '', self::$current_block_id),
            'attributes' => $this->parseAttributes(apply_filters('cision_block_media_attributes', array(
                'class' => array(
                    'cision-feed-item',
                ),
            ), self::$current_block_id)),
            'wrapper_attributes' => $this->parseAttributes(apply_filters('cision_block_wrapper_attributes', array(
                'class' => array(
                    'cision-feed-wrapper',
                ),
            ), self::$current_block_id)),
            'options' => array(
                'date_format' => self::$settings->get('date_format'),
            )
        ), EXTR_SKIP);

        ob_start();
        $templates = array(
            'cision-block.php',
            'templates/cision-block.php'
        );
        if (self::$settings->get('template')) {
            array_unshift($templates, self::$settings->get('template'));
        }
        $template = locate_template($templates);
        if ($template) {
            // Include theme overridden template.
            include $template;
        } else {
            // Include the default plugin supplied template.
            include __DIR__ . '/templates/cision-block.php';
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
        $attributes = $this->parseAttributes(apply_filters('cision_block_pager_attributes', $attributes, self::$current_block_id));
        $active_class = apply_filters('cision_block_pager_active_class', 'active', self::$current_block_id);
        if (self::$settings->get('items_per_page') > 0) {
            $max = (int) ceil(count($items) / self::$settings->get('items_per_page'));
            $id = get_query_var('cb_id');
            $page = (int) get_query_var('cb_page', -1);
            $active = ($id === 'cision_block' ? $page : 0);
            if ($max > 1) {
                $output = '<ul' . $attributes . '>';
                for ($i = 0; $i < $max; $i++) {
                    $output .= '<li><a href="' . add_query_arg(array('cb_id' => 'cision_block', 'cb_page' => $i)) . '"' .
                        ($active === $i ? ' class="' . $active_class . '"' : '') . '>' . ($i + 1) . '</a></li>';
                }
                if ($active >= 0 && $active < $max) {
                    $items = array_slice(
                        $items,
                        $active * self::$settings->get('items_per_page'),
                        self::$settings->get('items_per_page')
                    );
                }
                $output .= '</ul>';
            }
        }
        return $output;
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
        $cacheKey = self::TRANSIENT_KEY . '_' . self::$current_block_id . '_' . ($widget_id ? $widget_id : $post->ID);
        $data = get_transient($cacheKey);
        if ($data === false) {
            $params = array(
                'PageIndex' => Settings::DEFAULT_PAGE_INDEX,
                'PageSize' => self::$settings->get('count'),
                'DetailLevel' => self::FEED_DETAIL_LEVEL,
                'Format' => self::FEED_FORMAT,
                'Tags' => self::$settings->get('tags'),
                'StartDate' => self::$settings->get('start_date'),
                'EndDate' => self::$settings->get('end_date'),
                'SearchTerm' => self::$settings->get('search_term'),
                'Regulatory' =>
                    self::$settings->get('view_mode') === Settings::DISPLAY_MODE_REGULATORY ?
                        'true' :
                        (self::$settings->get('view_mode') === Settings::DISPLAY_MODE_NON_REGULATORY ? 'false' : null),
            );
            try {
                $response = $this->request->get(self::FEED_URL . self::$settings->get('source_uid'), array(
                    'body' => $params,
                ))->toJSON();
            } catch (\Exception $e) {
                $response = null;
            }
            $data = ($response ? $this->mapSources($response) : null);

            // Store transient data.
            if ($data && self::$settings->get('cache_expire') > 0) {
                set_transient(
                    $cacheKey,
                    $data,
                    self::$settings->get('cache_expire')
                );
            }
        }
        return ($data ?: array());
    }

    /**
     * @param stdClass $release
     * @param $image_style
     * @param bool $use_https
     *
     * @return object
     */
    protected function mapFeedItem(stdClass $release, $image_style, $use_https = false)
    {
        $item = array();

        // Clean up data.
        $item['Title'] = sanitize_text_field($release->Title);
        $item['PublishDate'] = strtotime($release->PublishDate);
        $item['Intro'] = sanitize_text_field($release->Intro);
        $item['Body'] = sanitize_text_field($release->Body);
        if (self::$settings->get('internal_links')) {
            $item['CisionWireUrl'] = get_bloginfo('url') . '/' . self::$settings->get('base_slug') . '/' . $release->EncryptedId;
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
        }

        // Let user modify the data.
        return (object) apply_filters('cision_map_source_item', $item, $release, self::$current_block_id);
    }

    /**
     * Check if an item is connected to any category.
     *
     * @param stdClass $item
     * @param array $categories
     *
     * @return bool
     */
    protected function hasCategory(stdClass $item, array $categories)
    {
        foreach ($item->Categories as $category) {
            if (in_array(strtolower($category->Name), $categories)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates an array of feed items.
     *
     * @param stdClass $feed
     *   A cision feed object.
     *
     * @return array
     *   An array of mapped feed items.
     */
    protected function mapSources(stdClass $feed)
    {
        $items = array();
        $image_style = self::$settings->get('image_style');
        $use_https = self::$settings->get('use_https');
        $language = self::$settings->get('language');
        $types = self::$settings->get('types');
        $categories = array_filter(array_map('trim', explode(',', self::$settings->get('categories'))));
        if (isset($feed->Releases) && count($feed->Releases)) {
            foreach ($feed->Releases as $release) {
                if (!is_object($release) || in_array($release->InformationType, $types) === false) {
                    continue;
                }
                if ($language && $release->LanguageCode !== $language) {
                    continue;
                }
                if ($categories && !$this->hasCategory($release, $categories)) {
                    continue;
                }
                $items[] = $this->mapFeedItem($release, $image_style, $use_https);
            }

            if ($items) {
                $items = apply_filters('cision_block_sort', $items, self::$current_block_id);
            }
        }

        return $items ?: null;
    }
}
