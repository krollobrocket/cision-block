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
        add_action('admin_post_cision_block_save_settings', array($this, 'saveSettings'));
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
            // $this->settings->cleanOptions($defaults);
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
     * Handle form data for configuration pages.
     *
     * @return bool
     */
    public function saveSettings()
    {
        $tab = '';
        $settings = array();

        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-settings', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', Settings::TEXTDOMAIN));
            }

            // Verify nonce and referer.
            if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
                $settings = Frontend::getInstance()->verifySettings($_POST);

                $tab = 'settings';
            }
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-permalinks', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', Settings::TEXTDOMAIN));
            }
            // Verify nonce and referer.
            if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
                $settings = Frontend::getInstance()->verifySettings($_POST);

                // Make sure we flush the rewrite rules.
                set_transient('cision_block_flush_rewrite_rules', 1);
                $tab = 'permalinks';
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
                $settings = Frontend::getInstance()->verifySettings($_POST);

                $tab = 'filters';
            }
        }
        $this->settings->setFromArray($settings)->save();
        Frontend::clearCache();
        wp_redirect(admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG . '&tab=' . $tab));
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
