<?php

namespace CisionBlock\Backend;

use CisionBlock\Common\Singleton;
use CisionBlock\Config\Settings;
use CisionBlock\Widget\Widget;
use CisionBlock\Frontend\Frontend;

class Backend extends Singleton
{
    const PARENT_MENU_SLUG = 'options-general.php';
    const MENU_SLUG = 'cision-block';

    const DISPLAY_MODE_ALL = 1;
    const DISPLAY_MODE_REGULATORY = 2;
    const DISPLAY_MODE_NONE_REGULATORY = 3;

    /**
     *
     * @var Settings
     */
    private $settings;

    /**
     *
     * @var Widget
     */
    private $widget;

    /**
     * @var string $capability
     */
    private $capability = 'manage_options';

    /**
     * @var string $currentTab
     */
    private $currentTab = '';

    /**
     *
     */
    public function init()
    {
        // Allow people to change what capability is required to use this plugin.
        $this->capability = apply_filters('cision_block_cap', $this->capability);

        $this->addActions();
        $this->addFilters();
        $this->settings = new Settings(Frontend::SETTINGS_NAME);
        $this->checkForUpdate();
        $this->setTabs();

        // Setup widget.
        $this->widget = new Widget();
    }

    /**
     * Add actions.
     */
    public function addActions()
    {
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('admin_init', array($this, 'handleFormSubmission'));
        add_action('admin_enqueue_scripts', array($this, 'registerStyles'));
    }

    /**
     * Add filters.
     */
    public function addFilters()
    {
        add_filter('plugin_action_links', array($this, 'addActionLinks'), 10, 2);
    }

    /**
     * @param $links
     * @param $file
     *
     * @return mixed
     */
    public function addActionLinks($links, $file)
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=cision-block') . '">' . __('General Settings', Settings::TEXTDOMAIN) . '</a>';
        if ($file == 'cision-block/bootstrap.php') {
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Check if we need to update.
     */
    protected function checkForUpdate()
    {
        if (version_compare($this->settings->get('version'), Frontend::VERSION, '<')) {
            // Rename old settings field.
            $this->settings->rename('source', 'source_uid');

            // Remove old sort_by field.
            $this->settings->remove('sort_by');

            $defaults = $this->settings->getDefaults();

            // Set defaults.
            foreach ($defaults as $key => $value) {
                $this->settings->add($key, $value);
            }

            // Handle our view_mode
            if (version_compare($this->settings->get('version'), '1.5.4', '<')) {
                $regulatory = $this->settings->get('is_regulatory');
                if ($regulatory) {
                    $this->settings->set('view_mode', self::DISPLAY_MODE_REGULATORY);
                }
                $this->settings->remove('is_regulatory');
            }

            $this->settings->version = Frontend::VERSION;

            // Store updated settings.
            $this->settings->cleanOptions($defaults);
            $this->settings->save();
        }
    }

    /**
     * Check if any updates needs to be performed.
     */
    public static function activate()
    {
    }

    /**
     * Add menu item for plugin.
     */
    public function addMenu()
    {
        add_submenu_page(
            self::PARENT_MENU_SLUG,
            __('Cision Block', Settings::TEXTDOMAIN),
            __('Cision Block', Settings::TEXTDOMAIN),
            $this->capability,
            self::MENU_SLUG,
            array($this, 'displaySettingsPage')
        );
    }

    /**
     * Registers styles and scripts.
     *
     * @return bool
     */
    public function registerStyles()
    {
        wp_enqueue_script(
            'cision-block-admin',
            plugin_dir_url(__FILE__) . 'js/cision-block-admin.js',
            array('jquery'),
            '',
            true
        );
    }

    /**
     * Handle form data for configuration page.
     *
     * @return bool
     */
    public function handleFormSubmission()
    {
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-settings', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', Settings::TEXTDOMAIN));
            }

            // Verify nonce and referer.
            if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
                // Filter and sanitize form values.
                $this->settings->count = filter_input(
                    INPUT_POST,
                    'count',
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'default' => Settings::DEFAULT_ITEM_COUNT,
                            'min_range' => 1,
                            'max_range' => Settings::MAX_ITEMS_PER_FEED,
                        ),
                    )
                );
                $this->settings->cache_expire = filter_input(
                    INPUT_POST,
                    'cache-expire',
                    FILTER_SANITIZE_NUMBER_INT
                );
                $this->settings->source_uid = filter_input(
                    INPUT_POST,
                    'source-uid',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->types = filter_input(
                    INPUT_POST,
                    'feed-types',
                    FILTER_SANITIZE_STRING,
                    FILTER_REQUIRE_ARRAY
                );
                $this->settings->language = filter_input(
                    INPUT_POST,
                    'language',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->readmore = filter_input(
                    INPUT_POST,
                    'readmore',
                    FILTER_SANITIZE_STRING
                );
                // Force lowercase language code.
                $this->settings->language = strtolower($this->settings->language);
                $this->settings->start_date = filter_input(
                    INPUT_POST,
                    'start-date',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->end_date = filter_input(
                    INPUT_POST,
                    'end-date',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->mark_regulatory = filter_input(
                    INPUT_POST,
                    'mark-regulatory',
                    FILTER_VALIDATE_BOOLEAN
                );
                $text = filter_input(
                    INPUT_POST,
                    'regulatory-text',
                    FILTER_SANITIZE_STRING
                );
                if ($text === '') {
                    $this->settings->regulatory_text = Settings::DEFAULT_MARK_REGULATORY_TEXT;
                } elseif ($text !== null) {
                    $this->settings->regulatory_text = $text;
                }
                $text = filter_input(
                    INPUT_POST,
                    'non-regulatory-text',
                    FILTER_SANITIZE_STRING
                );
                if ($text === '') {
                    $this->settings->non_regulatory_text = Settings::DEFAULT_MARK_NON_REGULATORY_TEXT;
                } elseif ($text !== null) {
                    $this->settings->non_regulatory_text = $text;
                }
                $this->settings->date_format = filter_input(
                    INPUT_POST,
                    'date-format',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->tags = filter_input(
                    INPUT_POST,
                    'tags',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->search_term = filter_input(
                    INPUT_POST,
                    'search-term',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->categories = filter_input(
                    INPUT_POST,
                    'categories',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->categories = trim(strtolower($this->settings->categories));
                $this->settings->view_mode = filter_input(
                    INPUT_POST,
                    'display-mode',
                    FILTER_VALIDATE_INT
                );
                $this->settings->use_https = filter_input(
                    INPUT_POST,
                    'use-https',
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->image_style = filter_input(
                    INPUT_POST,
                    'image-style',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->items_per_page = filter_input(
                    INPUT_POST,
                    'items-per-page',
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'default' => Settings::DEFAULT_ITEMS_PER_PAGE,
                            'min_range' => 0,
                            'max_range' => Settings::MAX_ITEMS_PER_PAGE,
                        ),
                    )
                );

                Frontend::clearCache();

                return $this->settings->save();
            }
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-permalink', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', Settings::TEXTDOMAIN));
            }
            // Verify nonce and referer.
            if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
                // Filter and sanitize form values.
                $this->settings->internal_links = filter_input(
                    INPUT_POST,
                    'internal-links',
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->base_slug = filter_input(
                    INPUT_POST,
                    'base-slug',
                    FILTER_SANITIZE_STRING
                );

                Frontend::clearCache();

                $this->settings->save();

                // Make sure we flush the rewrite rules.
                set_transient('cision_block_flush_rewrite_rules', 1);
            }
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-filters', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', Settings::TEXTDOMAIN));
            }
            // Verify nonce and referer.
            if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
                $this->settings->show_filters = filter_input(
                    INPUT_POST,
                    'show-filters',
                    FILTER_VALIDATE_BOOLEAN
                );
                $text = filter_input(
                    INPUT_POST,
                    'filter-all-text',
                    FILTER_SANITIZE_STRING
                );
                if ($text === '') {
                    $this->settings->filter_all_text = Settings::DEFAULT_FILTER_ALL_TEXT;
                } elseif ($text !== null) {
                    $this->settings->filter_all_text = $text;
                }
                $text = filter_input(
                    INPUT_POST,
                    'filter-regulatory-text',
                    FILTER_SANITIZE_STRING
                );
                if ($text === '') {
                    $this->settings->filter_regulatory_text = Settings::DEFAULT_FILTER_REGULATORY_TEXT;
                } elseif ($text !== null) {
                    $this->settings->filter_regulatory_text = $text;
                }
                $text = filter_input(
                    INPUT_POST,
                    'filter-non-regulatory-text',
                    FILTER_SANITIZE_STRING
                );
                if ($text === '') {
                    $this->settings->filter_non_regulatory_text = Settings::DEFAULT_FILTER_NON_REGULATORY_TEXT;
                } elseif ($text !== null) {
                    $this->settings->filter_non_regulatory_text = $text;
                }

                Frontend::clearCache();

                $this->settings->save();
            }
        }
    }

    /**
     * Returns an array of available image styles.
     *
     * @return array
     */
    public function getImageStyles()
    {
        return array(
            'DownloadUrl' => array(
                'label' => __('Original Image', Settings::TEXTDOMAIN),
                'class' => 'image-original',
            ),
            'UrlTo100x100ArResized' => array(
                'label' => __('100x100 Resized', Settings::TEXTDOMAIN),
                'class' => 'image-100x100-resized',
            ),
            'UrlTo200x200ArResized' => array(
                'label' => __('200x200 Resized', Settings::TEXTDOMAIN),
                'class' => 'image-200x200-resized',
            ),
            'UrlTo400x400ArResized' => array(
                'label' => __('400x400 Resized', Settings::TEXTDOMAIN),
                'class' => 'image-400x400-resized',
            ),
            'UrlTo800x800ArResized' => array(
                'label' => __('800x800 Resized', Settings::TEXTDOMAIN),
                'class' => 'image-800x800-resized',
            ),
            'UrlTo100x100Thumbnail' => array(
                'label' => __('100x100 Thumbnail', Settings::TEXTDOMAIN),
                'class' => 'image-100x100-thumbnail',
            ),
            'UrlTo200x200Thumbnail' => array(
                'label' => __('200x200 Thumbnail', Settings::TEXTDOMAIN),
                'class' => 'image-200x200-thumbnail',
            ),
        );
    }

    /**
     * Sets the current tab.
     */
    public function setTabs()
    {
        $this->currentTab = isset($_GET['tab']) && in_array($_GET['tab'], array_keys($this->getTabs())) ? $_GET['tab'] : 'settings';
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        return array(
            'settings' => __('General Settings', Settings::TEXTDOMAIN),
            'permalinks' => __('Permalinks', Settings::TEXTDOMAIN),
            'filters' => __('Filters', Settings::TEXTDOMAIN),
        );
    }

    /**
     * @return string
     */
    public function getCurrentTab()
    {
        return $this->currentTab;
    }

    /**
     * Renders tabs.
     */
    public function displayTabs()
    {
        include_once 'templates/tabs.php';
    }

    /**
     * Display the settings page.
     */
    public function displaySettingsPage()
    {
        $template = $this->getCurrentTab() . '.php';
        include_once 'templates/' . $template;
    }
}
