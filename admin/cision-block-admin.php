<?php
namespace CisionBlock;

use CisionBlock\Common\Singleton;
use CisionBlock\Config\Settings;
use CisionBlock\Widget\CisionBlockWidget;

class CisionBlockAdmin extends Singleton
{
    const PARENT_MENU_SLUG = 'options-general.php';
    const MENU_SLUG = 'cision-block';
    /**
    * Default settings.
    *
    * @var array
    */
    public static $default_settings = array(
        'count' => CISION_BLOCK_DEFAULT_ITEM_COUNT,
        'source_uid' => '',
        'tags' => '',
        'categories' => '',
        'start_date' => '',
        'end_date' => '',
        'search_term' => '',
        'image_style' => CISION_BLOCK_DEFAULT_IMAGE_STYLE,
        'use_https' => false,
        'date_format' => CISION_BLOCK_DEFAULT_DATE_FORMAT,
        'view_mode' => CISION_BLOCK_DEFAULT_DISPLAY_MODE,
        'language' => '',
        'readmore' => CISION_BLOCK_DEFAULT_READMORE_TEXT,
        'items_per_page' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
        'cache_expire' => CISION_BLOCK_DEFAULT_CACHE_LIFETIME,
        'types' => array(CISION_BLOCK_DEFAULT_FEED_TYPE),
        'internal_links' => false,
        'base_slug' => 'cision',
        'version' => CisionBlock::VERSION,
    );

    const DISPLAY_MODE_ALL = 1;
    const DISPLAY_MODE_REGULATORY = 2;
    const DISPLAY_MODE_NONE_REGULATORY = 3;

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
    * @var string $capability
    */
    private $capability = 'manage_options';

    /**
     * @var string $currentTab
     */
    private $currentTab = '';

    /**
    * Constructor.
    */
    protected function __construct()
    {
    }

    /**
     *
     */
    public function initialize()
    {
        // Allow people to change what capability is required to use this plugin.
        $this->capability = apply_filters('cision_block_cap', $this->capability);

        $this->addActions();
        $this->addFilters();
        $this->settings = new Settings(CisionBlock::SETTINGS_NAME);
        $this->checkForUpdate();
        $this->setTabs();

        // Setup widget.
        $this->widget = new CisionBlockWidget();
    }

    /**
     * Add actions.
     */
    public function addActions()
    {
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('admin_init', array($this, 'handleFormSubmission'));
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
        $settings_link = '<a href="' . admin_url('options-general.php?page=cision-block') . '">' . __('General Settings', CISION_BLOCK_TEXTDOMAIN) . '</a>';
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
        if (version_compare($this->settings->get('version'), CisionBlock::VERSION, '<')) {
            // Rename old settings field.
            $this->settings->rename('source', 'source_uid');

            // Remove old sort_by field.
            $this->settings->remove('sort_by');

            // Set defaults.
            foreach (self::$default_settings as $key => $value) {
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

            $this->settings->version = CisionBlock::VERSION;

            // Store updated settings.
            $this->settings->clean(self::$default_settings);
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
            __('Cision Block', CISION_BLOCK_TEXTDOMAIN),
            __('Cision Block', CISION_BLOCK_TEXTDOMAIN),
            $this->capability,
            self::MENU_SLUG,
            array($this, 'displaySettingsPage')
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
                die(__('You are not allowed to perform this action.', CISION_BLOCK_TEXTDOMAIN));
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
                            'default' => CISION_BLOCK_DEFAULT_ITEM_COUNT,
                            'min_range' => 1,
                            'max_range' => CISION_BLOCK_MAX_ITEMS_PER_FEED,
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
                            'default' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
                            'min_range' => 0,
                            'max_range' => CISION_BLOCK_MAX_ITEMS_PER_PAGE,
                        ),
                    )
                );

                CisionBlock::clearCache();

                return $this->settings->save();
            }
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-experimental', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', CISION_BLOCK_TEXTDOMAIN));
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

                CisionBlock::clearCache();

                $this->settings->save();

                // Make sure we flush the rewrite rules.
                set_transient('cision_block_flush_rewrite_rules', 1);
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
                'label' => __('Original Image', CISION_BLOCK_TEXTDOMAIN),
                'class' => 'image-original',
            ),
            'UrlTo100x100ArResized' => array(
                'label' => __('100x100 Resized', CISION_BLOCK_TEXTDOMAIN),
                'class' => 'image-100x100-resized',
            ),
            'UrlTo200x200ArResized' => array(
                'label' => __('200x200 Resized', CISION_BLOCK_TEXTDOMAIN),
                'class' => 'image-200x200-resized',
            ),
            'UrlTo400x400ArResized' => array(
                'label' => __('400x400 Resized', CISION_BLOCK_TEXTDOMAIN),
                'class' => 'image-400x400-resized',
            ),
            'UrlTo800x800ArResized' => array(
                'label' => __('800x800 Resized', CISION_BLOCK_TEXTDOMAIN),
                'class' => 'image-800x800-resized',
            ),
            'UrlTo100x100Thumbnail' => array(
                'label' => __('100x100 Thumbnail', CISION_BLOCK_TEXTDOMAIN),
                'class' => 'image-100x100-thumbnail',
            ),
            'UrlTo200x200Thumbnail' => array(
                'label' => __('200x200 Thumbnail', CISION_BLOCK_TEXTDOMAIN),
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
            'settings' => __('General Settings', CISION_BLOCK_TEXTDOMAIN),
            'experimental' => __('Single Press Releases', CISION_BLOCK_TEXTDOMAIN),
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
    public function displayTabs($return = false)
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
