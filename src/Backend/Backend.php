<?php

namespace CisionBlock\Backend;

use CisionBlock\Frontend\Frontend;
use CisionBlock\Plugin\Common\Singleton;
use CisionBlock\Settings\Settings;
use CisionBlock\Trait\AddonTrait;
use CisionBlock\Widget\Widget;

class Backend extends Singleton
{
    use AddonTrait;

    const PARENT_MENU_SLUG = 'options-general.php';
    const MENU_SLUG = 'cision-block';
    const SLACK_LINK = 'https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ';
    const PHONE_LINK = 'tel:+46767013987';
    const EMAIL_ADDRESS = 'cisionblock@gmail.com';
    const BUY_ME_A_COFFEE_LINK = 'https://www.buymeacoffee.com/cyclonecode';
    const REVIEW_LINK = 'https://wordpress.org/support/plugin/cision-block/reviews/?rate=5#new-post';
    const SUPPORT_LINK = 'https://wordpress.org/support/plugin/cision-block/#new-topic-0';

    /**
     *
     * @var Settings
     */
    private Settings $settings;

    /**
     *
     * @var Widget
     */
    private Widget $widget;

    /**
     * @var string $capability
     */
    private string $capability = 'manage_options';

    /**
     * @var string $currentTab
     */
    private string $currentTab = '';

    /**
     *
     */
    public function init(): void
    {
        // Allow people to change what capability is required to use this plugin.
        $this->capability = apply_filters('cision_block_cap', $this->capability);

        $this->addActions();
        $this->addFilters();
        $this->settings = new Settings(Frontend::SETTINGS_NAME);
        $this->checkForUpdate();

        // Add support for addons.
        do_action('cision-block/register/addon', [$this]);

        add_action('admin_menu', function () {
            $this->setTabs();
        });

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
    public function addActions(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('in_admin_header', [$this, 'addHeader']);
        add_action('admin_post_cision_block_save_settings', [$this, 'saveSettings']);
        add_action('admin_enqueue_scripts', [$this, 'registerStyles']);
        add_action('cision_block_admin_notices', [$this, 'renderNotices']);
        add_action('wp_ajax_cision_block_dismiss_notice', [$this, 'doDismissNotice']);
    }

    /**
     * Add filters.
     */
    public function addFilters(): void
    {
        add_filter('plugin_action_links', [$this, 'addActionLinks'], 10, 2);
        add_filter('plugin_row_meta', [$this, 'filterPluginRowMeta'], 10, 2);
    }

    /**
     * Marks a notification as dismissed.
     *
     * @param string $id
     * @return bool
     */
    private function dismissNotice(string $id): bool
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
    public function resetNotice(string $id): bool
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
    public function getNoticeByName(string $name)
    {
        $notes = $this->settings->get('notes');
        return isset($notes[$name]) ? $notes[$name] : null;
    }

    /**
     * Render any notifications.
     */
    public function renderNotices(): void
    {
        foreach ($this->settings->get('notes') as $note) {
            if (is_callable([$this, $note['callback']]) && (!$note['dismissed'] || (!$note['persistent'] && time() - $note['time'] > 30 * 24 * 60 * 60))) {
                ?>
                <div id="note-<?php echo $note['id']; ?>" class="cision-block-notice notice-<?php echo $note['type']; ?> notice<?php echo ($note['dismissible'] ? ' is-dismissible' : ''); ?> inline">
                <?php echo call_user_func([$this, $note['callback']]); ?>
                </div>
                <?php
            }
        }
    }

    /**
     * Ajax handler for dismissing notifications.
     */
    public function doDismissNotice(): void
    {
        check_ajax_referer('cision_block_dismiss_notice');
        if (!current_user_can('administrator')) {
            wp_send_json_error(__('You are not allowed to perform this action.', 'cision-block'));
            return;
        }
        if (!filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
            wp_send_json_error(__('No valid notification id supplied.', 'cision-block'));
            return;
        }
        if (!$this->dismissNotice($_POST['id'])) {
            wp_send_json_error(__('Notification could not be found.', 'cision-block'));
            return;
        }
        wp_send_json_success();
    }

    /**
     * Adds premium admin notification.
     */
    public function addPremiumNotice(): void
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
                <li><?php _e('Cron module.', 'cision-block'); ?></li>
                <li><?php _e('Insider module.', 'cision-block'); ?></li>
                <li><?php _e('Link Back module.', 'cision-block'); ?></li>
                <li><?php _e('Media module.', 'cision-block'); ?></li>
                <li><?php _e('Push module.', 'cision-block'); ?></li>
                <li><?php _e('Share Calculator module.', 'cision-block'); ?></li>
                <li><?php _e('Sharegraph module.', 'cision-block'); ?></li>
                <li><?php _e('Shareholder module.', 'cision-block'); ?></li>
                <li><?php _e('Subscription module.', 'cision-block'); ?></li>
                <li><?php _e('Ticker module.', 'cision-block'); ?></li>
                <li><?php _e('Translation module.', 'cision-block'); ?></li>
            </ul>
        </div>
        <p><?php echo sprintf(__('To get more information about the Pro version, please send me an email at <a href="mailto:%s?subject=%s" target="_blank" rel="noopener noreferrer">%s</a> or give me a <a href="%s">call</a>, you can also contact me at my <a href="%s" target="_blank" rel="noopener noreferrer">slack channel</a>.', 'cision-block'), self::EMAIL_ADDRESS, 'Cision%20Block%20Pro', self::EMAIL_ADDRESS, self::PHONE_LINK, self::SLACK_LINK); ?></p>
        <?php
    }

    /**
     * Adds review admin notification.
     */
    public function addReviewNotice(): void
    {
        ?>
        <h3><?php _e('Thank you for using Cision Block!', 'cision-block'); ?></h3>
        <p><?php echo sprintf(__('If you use and enjoy Cision Block, I would be really happy if you could give it a positive review at <a href="%s" target="_blank" rel="noopener noreferrer">Wordpress.org</a>.', 'cision-block'), self::REVIEW_LINK); ?><br />
        <?php _e('Doing this would help me keeping the plugin free and up to date.', 'cision-block'); ?><br />
        <?php _e('Also, if you would like to support me you can always buy me a cup of coffee at:', 'cision-block'); ?> <?php echo sprintf('<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', self::BUY_ME_A_COFFEE_LINK, self::BUY_ME_A_COFFEE_LINK); ?></p>
        <p><?php _e('Thank you very much!', 'cision-block'); ?></p>
        <?php
    }

    /**
     * Adds support admin notification.
     */
    public function addSupportNotice(): void
    {
        ?>
        <h3><?php _e('Do you have any feedback or need support?', 'cision-block'); ?></h3>
        <p><?php echo sprintf(__('If you have any request for improvement or just need some help. Do not hesitate to open a ticket in the <a href="%s" target="_blank">support section</a>.', 'cision-block'), self::SUPPORT_LINK); ?><br />
        <?php echo sprintf(__('I can also be reached by email at <a href="mailto:%s?subject=%s" target="_blank" rel="noopener noreferrer">%s</a>', 'cision-block'), self::EMAIL_ADDRESS, 'Cision%20Block', self::EMAIL_ADDRESS); ?><br />
        <?php echo sprintf(__('There is also a slack channel that you can <a href="%s" target="_blank" rel="noopener noreferrer">join</a>.', 'cision-block'), self::SLACK_LINK); ?></p>
        <p><?php _e('I hope you will have an awesome day!', 'cision-block'); ?></p>
        <?php
    }

    /**
     * Render admin header.
     */
    public function addHeader(): void
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
     * @param array $links
     * @param string $file
     *
     * @return string[]
     */
    public function addActionLinks(array $links, string $file): array
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=cision-block') . '">' . __('General Settings', 'cision-block') . '</a>';
        if ($file === 'cision-block/bootstrap.php') {
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
    public function filterPluginRowMeta(array $plugin_meta, string $plugin_file): array
    {
        if ($plugin_file !== 'cision-block/bootstrap.php') {
            return $plugin_meta;
        }

        $plugin_meta[] = sprintf(
            '<a target="_blank" href="%1$s"><span class="dashicons dashicons-star-filled" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            self::BUY_ME_A_COFFEE_LINK,
            esc_html_x('Sponsor', 'verb', 'cision-block')
        );
        $plugin_meta[] = sprintf(
            '<a target="_blank" href="%1$s"><span class="dashicons dashicons-thumbs-up" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            self::REVIEW_LINK,
            esc_html_x('Rate', 'verb', 'cision-block')
        );
        $plugin_meta[] = sprintf(
            '<a target="_blank" href="%1$s"><span class="dashicons dashicons-editor-help" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            self::SUPPORT_LINK,
            esc_html_x('Support', 'verb', 'cision-block')
        );

        return $plugin_meta;
    }

    /**
     * Check if we need to update.
     */
    protected function checkForUpdate(): void
    {
        if (version_compare($this->settings->get('version', '1.0.0'), Frontend::VERSION, '<')) {
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
            $defaults['notes'] = [
                'pro' => [
                    'id' => 3,
                    'weight' => 1,
                    'persistent' => false,
                    'time' => 0,
                    'type' => 'warning',
                    'name' => 'pro',
                    'callback' => 'addPremiumNotice',
                    'dismissed' => false,
                    'dismissible' => true,
                ],
                'review' => [
                    'id' => 1,
                    'weight' => 1,
                    'persistent' => false,
                    'time' => 0,
                    'type' => 'info',
                    'name' => 'review',
                    'callback' => 'addReviewNotice',
                    'dismissed' => false,
                    'dismissible' => true,
                ],
                'support' => [
                    'id' => 2,
                    'weight' => 2,
                    'persistent' => true,
                    'time' => 0,
                    'type' => 'warning',
                    'name' => 'support',
                    'callback' => 'addSupportNotice',
                    'dismissed' => true,
                    'dismissible' => true,
                ],
            ];
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
    public static function activate(): void
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
    public function addMenu(): void
    {
        add_submenu_page(
            self::PARENT_MENU_SLUG,
            __('Cision Block', 'cision-block'),
            __('Cision Block', 'cision-block'),
            $this->capability,
            self::MENU_SLUG,
            [$this, 'renderSettings']
        );
    }

    /**
     * Registers styles and scripts.
     */
    public function registerStyles(): void
    {
        wp_enqueue_style(
            'cision-block-admin',
            plugin_dir_url(__FILE__) . 'css/admin.css',
            [],
            Frontend::VERSION
        );
        wp_enqueue_script(
            'cision-block-admin',
            plugin_dir_url(__FILE__) . 'js/cision-block-admin.js',
            ['jquery'],
            Frontend::VERSION,
            true
        );
        wp_localize_script('cision-block-admin', 'data', [
            '_nonce' => wp_create_nonce('cision_block_dismiss_notice'),
        ]);
    }

    /**
     * Handle form data for configuration pages.
     */
    public function saveSettings(): void
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
        wp_safe_redirect(add_query_arg([
            'page' => self::MENU_SLUG,
            'tab' => $tab,
        ], self::PARENT_MENU_SLUG));
    }

    /**
     * Returns an array of available image styles.
     *
     * @return array
     */
    public static function getImageStyles(): array
    {
        return [
            'DownloadUrl' => [
                'label' => __('Original Image', 'cision-block'),
                'class' => 'image-original',
            ],
            'UrlTo100x100ArResized' => [
                'label' => __('100x100 Resized', 'cision-block'),
                'class' => 'image-100x100-resized',
            ],
            'UrlTo200x200ArResized' => [
                'label' => __('200x200 Resized', 'cision-block'),
                'class' => 'image-200x200-resized',
            ],
            'UrlTo400x400ArResized' => [
                'label' => __('400x400 Resized', 'cision-block'),
                'class' => 'image-400x400-resized',
            ],
            'UrlTo800x800ArResized' => [
                'label' => __('800x800 Resized', 'cision-block'),
                'class' => 'image-800x800-resized',
            ],
            'UrlTo100x100Thumbnail' => [
                'label' => __('100x100 Thumbnail', 'cision-block'),
                'class' => 'image-100x100-thumbnail',
            ],
            'UrlTo200x200Thumbnail' => [
                'label' => __('200x200 Thumbnail', 'cision-block'),
                'class' => 'image-200x200-thumbnail',
            ],
        ];
    }

    /**
     * Return a list of languages.
     *
     * @return string[]
     */
    public function getLanguages(): array
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
    public function setTabs(): void
    {
        $this->currentTab = isset($_GET['tab']) && in_array($_GET['tab'], array_keys($this->getTabs())) ? $_GET['tab'] : 'settings';
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        $tabs = [
            'settings' => __('General Settings', 'cision-block'),
            'permalinks' => __('Permalinks', 'cision-block'),
            'filters' => __('Filters', 'cision-block'),
        ];
        foreach ($this->addons as $key => $addon) {
            $tabs[$key] = ucfirst($addon->getModuleName());
        }
        $tabs['status'] = __('Status', 'cision-block');
        return $tabs;
    }

    /**
     * @return string
     */
    public function getCurrentTab(): string
    {
        return $this->currentTab;
    }

    /**
     * Renders tabs.
     */
    public function displayTabs(): void
    {
        include_once 'templates/tabs.php';
    }

    /**
     * Display the settings page.
     */
    public function renderSettings(): void
    {
        if (in_array($this->getCurrentTab(), array_map(function($addon) {
            return $addon->getModuleName();
        }, $this->addons))) {
            $this->addons[$this->getCurrentTab()]->renderSettings();
            return;
        }
        $template = $this->getCurrentTab() . '.php';
        include_once 'templates/' . $template;
    }
}
