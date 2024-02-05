<?php

namespace CisionBlock\Backend;

use CisionBlock\Plugin\Common\Singleton;
use CisionBlock\Settings\Settings;
use CisionBlock\Widget\Widget;
use CisionBlock\Frontend\Frontend;

class Backend extends Singleton
{
    const PARENT_MENU_SLUG = 'options-general.php';
    const MENU_SLUG = 'cision-block';

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

        // WPML
        if (has_action('wpml_register_single_string')) {
            do_action('wpml_register_single_string', 'cision-block', 'Read More Button Text', $this->settings->get('readmore'));
            do_action('wpml_register_single_string', 'cision-block', 'All Filter Button Text', $this->settings->get('filter_all_text'));
            do_action('wpml_register_single_string', 'cision-block', 'Non-regulatory Filter Button Text', $this->settings->get('filter_non_regulatory_text'));
            do_action('wpml_register_single_string', 'cision-block', 'Regulatory Filter Button Text', $this->settings->get('filter_regulatory_text'));
            do_action('wpml_register_single_string', 'cision-block', 'Text For Non-regulatory Releases', $this->settings->get('non_regulatory_text'));
            do_action('wpml_register_single_string', 'cision-block', 'Text For Regulatory Releases', $this->settings->get('regulatory_text'));
        }

        // Setup widget.
        $this->widget = new Widget();
    }

    /**
     * @return Widget
     */
    public function getWidget(): Widget
    {
        return $this->widget;
    }

    /**
     * Add actions.
     */
    public function addActions()
    {
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('in_admin_header', array($this, 'addHeader'));
        add_action('admin_post_cision_block_save_settings', array($this, 'saveSettings'));
        add_action('admin_enqueue_scripts', array($this, 'registerStyles'));
        add_action('cision_block_admin_notices', array($this, 'renderNotices'));
        add_action('wp_ajax_cision_block_dismiss_notice', array($this, 'doDismissNotice'));
    }

    /**
     * Add filters.
     */
    public function addFilters()
    {
        add_filter('plugin_action_links', array($this, 'addActionLinks'), 10, 2);
        add_filter('plugin_row_meta', array($this, 'filterPluginRowMeta'), 10, 2);
    }

    /**
     * Marks a notification as dismissed.
     *
     * @param string $id
     * @return bool
     */
    private function dismissNotice($id)
    {
        $notes = $this->settings->get('notes');
        foreach ($notes as $key => $note) {
            if ($note['id'] === (int) $id) {
                $notes[$key]['dismissed'] = true;
                $notes[$key]['time'] = time();
                $this->settings->set('notes', $notes);
                $this->settings->save();
                return true;
            }
        }
        return false;
    }

    /**
     * Resets a notification.
     *
     * @param string $id
     * @return bool
     */
    public function resetNotice($id)
    {
        $notes = $this->settings->get('notes');
        foreach ($notes as $key => $note) {
            if ($note['id'] === (int) $id) {
                $notes[$key]['dismissed'] = false;
                $notes[$key]['time'] = time();
                $this->settings->set('notes', $notes);
                $this->settings->save();
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a notification by name.
     *
     * @param string $name
     * @return mixed|null
     */
    public function getNoticeByName($name)
    {
        $notes = $this->settings->get('notes');
        return isset($notes[$name]) ? $notes[$name] : null;
    }

    /**
     * Render any notifications.
     */
    public function renderNotices()
    {
        foreach ($this->settings->get('notes') as $note) {
            // TODO: Check so it is callable.
            if (!$note['dismissed'] || ($note['dismissed'] && !$note['persistent'] && time() - $note['time'] > 30 * 24 * 60 * 60)) {
                ?>
                <div id="note-<?php echo $note['id']; ?>" class="cision-block-notice notice-<?php echo $note['type']; ?> notice<?php echo ($note['dismissible'] ? ' is-dismissible' : ''); ?> inline">
                <?php echo call_user_func(array($this, $note['callback'])); ?>
                </div>
                <?php
            }
        }
    }

    /**
     * Ajax handler for dismissing notifications.
     */
    public function doDismissNotice()
    {
        check_ajax_referer('cision_block_dismiss_notice');
        if (!current_user_can('administrator')) {
            return wp_send_json_error(__('You are not allowed to perform this action.', 'cision-block'));
        }
        if (!filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
            return wp_send_json_error(__('No valid notification id supplied.', 'cision-block'));
        }
        if (!$this->dismissNotice($_POST['id'])) {
            return wp_send_json_error(__('Notification could not be found.', 'cision-block'));
        }
        wp_send_json_success();
    }

    /**
     * Adds premium admin notification.
     */
    public function addPremiumNotice()
    {
        ?>
        <h3><?php _e('Pro Version', 'cision-block'); ?></h3>
        <p><?php _e('There is now a <b>PRO</b> version of this plugin, which includes extended features. For instance:', 'cision-block'); ?></p>
        <div>
            <ul style="">
                <li><?php _e('Support to fetch entire feed and not only the last 100 entries.', 'cision-block'); ?></li>
                <li><?php _e('Custom post types. Creates a post for each item in Wordpress. This means that all news have standard Wordpress links.', 'cision-block'); ?></li>
                <li><?php _e('Manually created posts can be added to the feed.', 'cision-block'); ?></li>
                <li><?php _e('Custom taxonomies for categories and tags fetched from Cision.', 'cision-block'); ?></li>
                <li><?php _e('Support to create, update and delete posts based on PUSH events sent from Cision.', 'cision-block'); ?></li>
                <li><?php _e('Support to create, update and delete posts during CRON at configurable intervals.', 'cision-block'); ?></li>
                <li><?php _e('Import categories and tags for each feed item.', 'cision-block'); ?></li>
                <li><?php _e('Support to hide news based on id.', 'cision-block'); ?></li>
                <li><?php _e('Make changes to imported posts and mark them as locally modified.', 'cision-block'); ?></li>
                <li><?php _e('Free support, installation and configuration help.', 'cision-block'); ?></li>
            </ul>
            <p><?php _e('Available extensions:', 'cision-block'); ?></p>
            <ul>
                <li><?php _e('Calendar module.', 'cision-block'); ?></li>
                <li><?php _e('Cron module.', 'cision-block-pro'); ?></li>
                <li><?php _e('Insider module.', 'cision-block'); ?></li>
                <li><?php _e('Link Back module.', 'cision-block'); ?></li>
                <li><?php _e('Media module.', 'cision-block'); ?></li>
                <li><?php _e('Push module.', 'cision-block-pro'); ?></li>
                <li><?php _e('Sharegraph module.', 'cision-block'); ?></li>
                <li><?php _e('Shareholder module.', 'cision-block'); ?></li>
                <li><?php _e('Subscription module.', 'cision-block'); ?></li>
                <li><?php _e('Ticker module.', 'cision-block'); ?></li>
            </ul>
        </div>
        <p><?php echo sprintf(__('To get more information about the Pro version, please send me an email at <a href="mailto:cisionblock@gmail.com?subject=%s" target="_blank" rel="noopener noreferrer">cisionblock@gmail.com</a> or give me a <a href="%s">call</a>, you can also contact me at my <a href="%s" target="_blank" rel="noopener noreferrer">slack channel</a>.', 'cision-block'), 'Cision%20Block%20Pro', 'tel:+46767013987', 'https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ'); ?></p>
        <?php
    }

    /**
     * Adds review admin notification.
     */
    public function addReviewNotice()
    {
        ?>
        <h3><?php _e('Thank you for using Cision Block!', 'cision-block'); ?></h3>
        <p><?php echo sprintf(__('If you use and enjoy Cision Block, I would be really happy if you could give it a positive review at <a href="%s" target="_blank" rel="noopener noreferrer">Wordpress.org</a>.', 'cision-block'), 'https://wordpress.org/support/plugin/cision-block/reviews/?rate=5#new-post'); ?></p>
        <p><?php _e('Doing this would help me keeping the plugin free and up to date.', 'cision-block'); ?></p>
        <p><?php _e('Also, if you would like to support me you can always buy me a cup of coffee at:', 'cision-block'); ?> <a href="https://www.buymeacoffee.com/cyclonecode" target="_blank" rel="noopener noreferrer">https://www.buymeacoffee.com/cyclonecode</a></p>
        <p><?php _e('Thank you very much!', 'cision-block'); ?></p>
        <?php
    }

    /**
     * Adds support admin notification.
     */
    public function addSupportNotice()
    {
        ?>
        <h3><?php _e('Do you have any feedback or need support?', 'cision-block'); ?></h3>
        <p><?php echo sprintf(__('If you have any request for improvement or just need some help. Do not hesitate to open a ticket in the <a href="%s" target="_blank">support section</a>.', 'cision-block'), 'https://wordpress.org/support/plugin/cision-block/#new-topic-0'); ?></p>
        <p><?php echo sprintf(__('I can also be reached by email at <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', 'cision-block'), 'mailto:cisionblock@gmail.com?subject=Cision%20Block', 'cisionblock@gmail.com'); ?></p>
        <p><?php echo sprintf(__('There is also a slack channel that you can <a href="%s" target="_blank" rel="noopener noreferrer">join</a>.', 'cision-block'), 'https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ'); ?></p>
        <p><?php _e('I hope you will have an awesome day!', 'cision-block'); ?></p>
        <?php
    }

    /**
     * Render admin header.
     */
    public function addHeader()
    {
        if (get_current_screen()->id !== 'settings_page_cision-block') {
            return;
        }
        $sectionText = $this->getTabs();
        if ($this->currentTab === 'info' && !empty($this->currentSection)) {
            $title = ' | ' . $sectionText[$this->currentSection];
        } else {
            $title = $this->currentTab ? ' | ' . $sectionText[$this->currentTab] : '';
        }
        ?>
        <div id="cision-block-admin-header">
            <span><img width="64" src="<?php echo plugin_dir_url(__FILE__); ?>assets/icon-128x128.png" alt="<?php _e('Cision Block', 'cision-block'); ?>" />
                <h1><?php _e('Cision Block', 'cision-block'); ?><?php echo $title; ?></h1>
            </span>
        </div>
        <?php
    }

    /**
     * @param $links
     * @param $file
     *
     * @return mixed
     */
    public function addActionLinks($links, $file)
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=cision-block') . '">' . __('General Settings', 'cision-block') . '</a>';
        if ($file == 'cision-block/bootstrap.php') {
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Filters the array of row meta for each plugin in the Plugins list table.
     *
     * @param string[] $plugin_meta An array of the plugin's metadata.
     * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
     * @return string[] An array of the plugin's metadata.
     */
    public function filterPluginRowMeta(array $plugin_meta, $plugin_file)
    {
        if ($plugin_file !== 'cision-block/bootstrap.php') {
            return $plugin_meta;
        }

        $plugin_meta[] = sprintf(
            '<a target="_blank" href="%1$s"><span class="dashicons dashicons-star-filled" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            'https://www.buymeacoffee.com/cyclonecode',
            esc_html_x('Sponsor', 'verb', 'cision-block')
        );
        $plugin_meta[] = sprintf(
            '<a target="_blank" href="%1$s"><span class="dashicons dashicons-thumbs-up" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            'https://wordpress.org/support/plugin/cision-block/reviews/?rate=5#new-post',
            esc_html_x('Rate', 'verb', 'cision-block')
        );
        $plugin_meta[] = sprintf(
            '<a target="_blank" href="%1$s"><span class="dashicons dashicons-editor-help" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            'https://wordpress.org/support/plugin/cision-block/#new-topic-0',
            esc_html_x('Support', 'verb', 'cision-block')
        );

        return $plugin_meta;
    }

    /**
     * Check if we need to update.
     */
    protected function checkForUpdate()
    {
        if (version_compare($this->settings->get('version') ?: '0.0.1', Frontend::VERSION, '<')) {
            // Rename old settings field.
            $this->settings->rename('source', 'source_uid');

            // Remove old sort_by field.
            $this->settings->remove('sort_by');

            $defaults = $this->settings->getDefaults();

            // Set defaults.
            foreach ($defaults as $key => $value) {
                $this->settings->add($key, $value);
            }

            // Setup notifications
            $defaults['notes'] = array(
                'pro' => array(
                    'id' => 3,
                    'weight' => 1,
                    'persistent' => false,
                    'time' => 0,
                    'type' => 'warning',
                    'name' => 'pro',
                    'callback' => 'addPremiumNotice',
                    'dismissed' => false,
                    'dismissible' => true,
                ),
                'review' => array(
                    'id' => 1,
                    'weight' => 1,
                    'persistent' => false,
                    'time' => 0,
                    'type' => 'info',
                    'name' => 'review',
                    'callback' => 'addReviewNotice',
                    'dismissed' => false,
                    'dismissible' => true,
                ),
                'support' => array(
                    'id' => 2,
                    'weight' => 2,
                    'persistent' => true,
                    'time' => 0,
                    'type' => 'warning',
                    'name' => 'support',
                    'callback' => 'addSupportNotice',
                    'dismissed' => true,
                    'dismissible' => true,
                )
            );
            $notes = $this->settings->get('notes');

            // Special handling for persistent notes.
            foreach ($defaults['notes'] as $id => $note) {
                if ($note['persistent'] && isset($notes[$id])) {
                    $defaults['notes'][$id]['dismissed'] = $notes[$id]['dismissed'];
                }
            }
            $this->settings->set('notes', $defaults['notes']);

            // Handle our view_mode
            if (version_compare($this->settings->get('version'), '1.5.4', '<')) {
                $regulatory = $this->settings->get('is_regulatory');
                if ($regulatory) {
                    $this->settings->set('view_mode', Settings::DISPLAY_MODE_REGULATORY);
                }
                $this->settings->remove('is_regulatory');
            }

            // Store updated settings.
            $this->settings
                ->set('version', Frontend::VERSION)
                ->save();
        }
    }

    /**
     * Check if any updates needs to be performed.
     */
    public static function activate()
    {
        global $wp_version;
        if (version_compare(PHP_VERSION, Settings::MIN_PHP_VERSION, '<')) {
            deactivate_plugins('cision-block');
            wp_die(__(sprintf('Unsupported PHP version. Minimum supported version is %s.', Settings::MIN_PHP_VERSION), 'cision-block'));
        }
        if (version_compare($wp_version, Settings::MIN_WP_VERSION, '<')) {
            deactivate_plugins('cision-block');
            wp_die(__(sprintf('Unsupported Wordpress version. Minimum supported version is %s.', Settings::MIN_WP_VERSION), 'cision-block'));
        }
    }

    /**
     * Add menu item for plugin.
     */
    public function addMenu()
    {
        add_submenu_page(
            self::PARENT_MENU_SLUG,
            __('Cision Block', 'cision-block'),
            __('Cision Block', 'cision-block'),
            $this->capability,
            self::MENU_SLUG,
            array($this, 'displaySettingsPage')
        );
    }

    /**
     * Registers styles and scripts.
     */
    public function registerStyles()
    {
        wp_enqueue_style(
            'cision-block-admin',
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array(),
            Frontend::VERSION
        );
        wp_enqueue_script(
            'cision-block-admin',
            plugin_dir_url(__FILE__) . 'js/cision-block-admin.js',
            array('jquery'),
            Frontend::VERSION,
            true
        );
        wp_localize_script('cision-block-admin', 'data', array(
            '_nonce' => wp_create_nonce('cision_block_dismiss_notice'),
        ));
    }

    /**
     * Handle form data for configuration pages.
     */
    public function saveSettings()
    {
        $tab = '';

        // Validate so user has correct privileges.
        if (!current_user_can($this->capability)) {
            die(__('You are not allowed to perform this action.', 'cision-block'));
        }
        // Verify nonce and referer.
        check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce');

        $settings = Frontend::verifySettings($_POST, $this->settings);

        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-settings', FILTER_UNSAFE_RAW)) {
            $tab = 'settings';
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-permalinks', FILTER_UNSAFE_RAW)) {
            // Make sure we flush the rewrite rules.
            set_transient('cision_block_flush_rewrite_rules', 1);
            $tab = 'permalinks';
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-filters', FILTER_UNSAFE_RAW)) {
            $tab = 'filters';
        }
        $this->settings
            ->setFromArray($settings)
            ->save();
        Frontend::clearCache();

        // Check if we should activate the support notification.
        if (($notice = $this->getNoticeByName('support')) && $notice['time'] === 0) {
            $this->resetNotice($notice['id']);
        }
        wp_safe_redirect(add_query_arg(array(
            'page' => self::MENU_SLUG,
            'tab' => $tab,
        ), self::PARENT_MENU_SLUG));
    }

    /**
     * Returns an array of available image styles.
     *
     * @return array
     */
    public static function getImageStyles()
    {
        return array(
            'DownloadUrl' => array(
                'label' => __('Original Image', 'cision-block'),
                'class' => 'image-original',
            ),
            'UrlTo100x100ArResized' => array(
                'label' => __('100x100 Resized', 'cision-block'),
                'class' => 'image-100x100-resized',
            ),
            'UrlTo200x200ArResized' => array(
                'label' => __('200x200 Resized', 'cision-block'),
                'class' => 'image-200x200-resized',
            ),
            'UrlTo400x400ArResized' => array(
                'label' => __('400x400 Resized', 'cision-block'),
                'class' => 'image-400x400-resized',
            ),
            'UrlTo800x800ArResized' => array(
                'label' => __('800x800 Resized', 'cision-block'),
                'class' => 'image-800x800-resized',
            ),
            'UrlTo100x100Thumbnail' => array(
                'label' => __('100x100 Thumbnail', 'cision-block'),
                'class' => 'image-100x100-thumbnail',
            ),
            'UrlTo200x200Thumbnail' => array(
                'label' => __('200x200 Thumbnail', 'cision-block'),
                'class' => 'image-200x200-thumbnail',
            ),
        );
    }

    /**
     * Return a list of languages.
     *
     * @return array
     */
    public function getLanguages()
    {
        $languages = [
            'ar' => __('Arabic', 'cision-block'),
            'bs' => __('Bosnian', 'cision-block'),
            'bg' => __('Bulgarian', 'cision-block'),
            'cs' => __('Czech', 'cision-block'),
            'zh' => __('Chinese', 'cision-block'),
            'hr' => __('Croatian', 'cision-block'),
            'da' => __('Danish', 'cision-block'),
            'de' => __('German', 'cision-block'),
            'nl' => __('Dutch', 'cision-block'),
            'en' => __('English', 'cision-block'),
            'es' => __('Spanish', 'cision-block'),
            'et' => __('Estonian', 'cision-block'),
            'fr' => __('French', 'cision-block'),
            'el' => __('Greek', 'cision-block'),
            'hu' => __('Hungarian', 'cision-block'),
            'is' => __('Icelandic', 'cision-block'),
            'it' => __('Italian', 'cision-block'),
            'ja' => __('Japanese', 'cision-block'),
            'ko' => __('Korean', 'cision-block'),
            'lv' => __('Latvian', 'cision-block'),
            'lt' => __('Lithuanian', 'cision-block'),
            'no' => __('Norwegian', 'cision-block'),
            'pl' => __('Polish', 'cision-block'),
            'pt' => __('Portuguese', 'cision-block'),
            'ro' => __('Romanian', 'cision-block'),
            'ru' => __('Russian', 'cision-block'),
            'sr' => __('Serbian', 'cision-block'),
            'sk' => __('Slovakian', 'cision-block'),
            'sl' => __('Slovenian', 'cision-block'),
            'fi' => __('Finnish', 'cision-block'),
            'sv' => __('Swedish', 'cision-block'),
            'tr' => __('Turkish', 'cision-block'),
        ];
        asort($languages);
        return $languages;
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
            'settings' => __('General Settings', 'cision-block'),
            'permalinks' => __('Permalinks', 'cision-block'),
            'filters' => __('Filters', 'cision-block'),
            'status' => __('Status', 'cision-block'),
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
