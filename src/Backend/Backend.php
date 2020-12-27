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
            $this->settings->save();
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
            wp_die(__('Unsupported PHP version. Minimum supported version is 5.6.', Settings::TEXTDOMAIN));
        }
        if (version_compare($wp_version, Settings::MIN_WP_VERSION, '<')) {
            deactivate_plugins('cision-block');
            wp_die(__('Unsupported Wordpress version. Minimum supported version is 3.1.0.', Settings::TEXTDOMAIN));
        }
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
            Frontend::VERSION,
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
        $tab = 'settings';
        $settings = array();

        // Validate so user has correct privileges.
        if (!current_user_can($this->capability)) {
            die(__('You are not allowed to perform this action.', Settings::TEXTDOMAIN));
        }
        // Verify nonce and referer.
        if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
            // Check if a settings form is submitted.
            if (filter_input(INPUT_POST, 'cision-block-settings', FILTER_SANITIZE_STRING)) {
                $settings = Frontend::getInstance()->verifySettings($_POST);
            }
            if (filter_input(INPUT_POST, 'cision-block-permalinks', FILTER_SANITIZE_STRING)) {
                $settings = Frontend::getInstance()->verifySettings($_POST);
                // Make sure we flush the rewrite rules.
                set_transient('cision_block_flush_rewrite_rules', 1);
                $tab = 'permalinks';
            }
            if (filter_input(INPUT_POST, 'cision-block-filters', FILTER_SANITIZE_STRING)) {
                $settings = Frontend::getInstance()->verifySettings($_POST);
                $tab = 'filters';
            }
            if (filter_input(INPUT_POST, 'cision-block-import-settings')) {
                $settings = (array)json_decode(str_replace(array("\r\n", "\n"), array("", ""), stripslashes($_POST['settings'])));
                $settings = Frontend::getInstance()->verifySettings($settings);
                $tab = 'settings';
            }
            $this->settings->setFromArray($settings)->save();
            Frontend::clearCache();
        }
        wp_redirect(admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG . '&tab=' . $tab));
    }

    /**
     * Returns an array of available languages.
     *
     * @return array
     */
    public function getLanguages()
    {
        return array(
            'ab' => __('Abkhazian', Settings::TEXTDOMAIN),
            'aa' => __('Afar', Settings::TEXTDOMAIN),
            'af' => __('Afrikaans', Settings::TEXTDOMAIN),
            'ak' => __('Akan', Settings::TEXTDOMAIN),
            'sq' => __('Albanian', Settings::TEXTDOMAIN),
            'am' => __('Amharic', Settings::TEXTDOMAIN),
            'ar' => __('Arabic', Settings::TEXTDOMAIN),
            'an' => __('Aragonese', Settings::TEXTDOMAIN),
            'hy' => __('Armenian', Settings::TEXTDOMAIN),
            'as' => __('Assamese', Settings::TEXTDOMAIN),
            'av' => __('Avaric', Settings::TEXTDOMAIN),
            'ae' => __('Avestan', Settings::TEXTDOMAIN),
            'ay' => __('Aymara', Settings::TEXTDOMAIN),
            'az' => __('Azerbaijani', Settings::TEXTDOMAIN),
            'bm' => __('Bambara', Settings::TEXTDOMAIN),
            'ba' => __('Bashkir', Settings::TEXTDOMAIN),
            'eu' => __('Basque', Settings::TEXTDOMAIN),
            'be' => __('Belarusian', Settings::TEXTDOMAIN),
            'bn' => __('Bengali', Settings::TEXTDOMAIN),
            'bh' => __('Bihari languages', Settings::TEXTDOMAIN),
            'bi' => __('Bislama', Settings::TEXTDOMAIN),
            'bs' => __('Bosnian', Settings::TEXTDOMAIN),
            'br' => __('Breton', Settings::TEXTDOMAIN),
            'bg' => __('Bulgarian', Settings::TEXTDOMAIN),
            'my' => __('Burmese', Settings::TEXTDOMAIN),
            'ca' => __('Catalan, Valencian', Settings::TEXTDOMAIN),
            'km' => __('Central Khmer', Settings::TEXTDOMAIN),
            'ch' => __('Chamorro', Settings::TEXTDOMAIN),
            'ce' => __('Chechen', Settings::TEXTDOMAIN),
            'ny' => __('Chichewa, Chewa, Nyanja', Settings::TEXTDOMAIN),
            'zh' => __('Chinese', Settings::TEXTDOMAIN),
            'cu' => __('Church Slavonic, Old Bulgarian, Old Church Slavonic', Settings::TEXTDOMAIN),
            'cv' => __('Chuvash', Settings::TEXTDOMAIN),
            'kw' => __('Cornish', Settings::TEXTDOMAIN),
            'co' => __('Corsican', Settings::TEXTDOMAIN),
            'cr' => __('Cree', Settings::TEXTDOMAIN),
            'hr' => __('Croatian', Settings::TEXTDOMAIN),
            'cs' => __('Czech', Settings::TEXTDOMAIN),
            'da' => __('Danish', Settings::TEXTDOMAIN),
            'dv' => __('Divehi, Dhivehi, Maldivian', Settings::TEXTDOMAIN),
            'nl' => __('Dutch, Flemish', Settings::TEXTDOMAIN),
            'dz' => __('Dzongkha', Settings::TEXTDOMAIN),
            'en' => __('English', Settings::TEXTDOMAIN),
            'eo' => __('Esperanto', Settings::TEXTDOMAIN),
            'et' => __('Estonian', Settings::TEXTDOMAIN),
            'ee' => __('Ewe', Settings::TEXTDOMAIN),
            'fo' => __('Faroese', Settings::TEXTDOMAIN),
            'fj' => __('Fijian', Settings::TEXTDOMAIN),
            'fi' => __('Finnish', Settings::TEXTDOMAIN),
            'fr' => __('French', Settings::TEXTDOMAIN),
            'ff' => __('Fulah', Settings::TEXTDOMAIN),
            'gd' => __('Gaelic, Scottish Gaelic', Settings::TEXTDOMAIN),
            'gl' => __('Galician', Settings::TEXTDOMAIN),
            'lg' => __('Ganda', Settings::TEXTDOMAIN),
            'ka' => __('Georgian', Settings::TEXTDOMAIN),
            'de' => __('German', Settings::TEXTDOMAIN),
            'ki' => __('Gikuyu, Kikuyu', Settings::TEXTDOMAIN),
            'el' => __('Greek (Modern)', Settings::TEXTDOMAIN),
            'kl' => __('Greenlandic, Kalaallisut', Settings::TEXTDOMAIN),
            'gn' => __('Guarani', Settings::TEXTDOMAIN),
            'gu' => __('Gujarati', Settings::TEXTDOMAIN),
            'ht' => __('Haitian, Haitian Creole', Settings::TEXTDOMAIN),
            'ha' => __('Hausa', Settings::TEXTDOMAIN),
            'he' => __('Hebrew', Settings::TEXTDOMAIN),
            'hz' => __('Herero', Settings::TEXTDOMAIN),
            'hi' => __('Hindi', Settings::TEXTDOMAIN),
            'ho' => __('Hiri Motu', Settings::TEXTDOMAIN),
            'hu' => __('Hungarian', Settings::TEXTDOMAIN),
            'is' => __('Icelandic', Settings::TEXTDOMAIN),
            'io' => __('Ido', Settings::TEXTDOMAIN),
            'ig' => __('Igbo', Settings::TEXTDOMAIN),
            'id' => __('Indonesian', Settings::TEXTDOMAIN),
            'ia' => __('Interlingua (International Auxiliary Language Association)', Settings::TEXTDOMAIN),
            'ie' => __('Interlingue', Settings::TEXTDOMAIN),
            'iu' => __('Inuktitut', Settings::TEXTDOMAIN),
            'ik' => __('Inupiaq', Settings::TEXTDOMAIN),
            'ga' => __('Irish', Settings::TEXTDOMAIN),
            'it' => __('Italian', Settings::TEXTDOMAIN),
            'ja' => __('Japanese', Settings::TEXTDOMAIN),
            'jv' => __('Javanese', Settings::TEXTDOMAIN),
            'kn' => __('Kannada', Settings::TEXTDOMAIN),
            'kr' => __('Kanuri', Settings::TEXTDOMAIN),
            'ks' => __('Kashmiri', Settings::TEXTDOMAIN),
            'kk' => __('Kazakh', Settings::TEXTDOMAIN),
            'rw' => __('Kinyarwanda', Settings::TEXTDOMAIN),
            'kv' => __('Komi', Settings::TEXTDOMAIN),
            'kg' => __('Kongo', Settings::TEXTDOMAIN),
            'ko' => __('Korean', Settings::TEXTDOMAIN),
            'kj' => __('Kwanyama, Kuanyama', Settings::TEXTDOMAIN),
            'ku' => __('Kurdish', Settings::TEXTDOMAIN),
            'ky' => __('Kyrgyz', Settings::TEXTDOMAIN),
            'lo' => __('Lao', Settings::TEXTDOMAIN),
            'la' => __('Latin', Settings::TEXTDOMAIN),
            'lv' => __('Latvian', Settings::TEXTDOMAIN),
            'lb' => __('Letzeburgesch, Luxembourgish', Settings::TEXTDOMAIN),
            'li' => __('Limburgish, Limburgan, Limburger', Settings::TEXTDOMAIN),
            'ln' => __('Lingala', Settings::TEXTDOMAIN),
            'lt' => __('Lithuanian', Settings::TEXTDOMAIN),
            'lu' => __('Luba-Katanga', Settings::TEXTDOMAIN),
            'mk' => __('Macedonian', Settings::TEXTDOMAIN),
            'mg' => __('Malagasy', Settings::TEXTDOMAIN),
            'ms' => __('Malay', Settings::TEXTDOMAIN),
            'ml' => __('Malayalam', Settings::TEXTDOMAIN),
            'mt' => __('Maltese', Settings::TEXTDOMAIN),
            'gv' => __('Manx', Settings::TEXTDOMAIN),
            'mi' => __('Maori', Settings::TEXTDOMAIN),
            'mr' => __('Marathi', Settings::TEXTDOMAIN),
            'mh' => __('Marshallese', Settings::TEXTDOMAIN),
            'ro' => __('Moldovan, Moldavian, Romanian', Settings::TEXTDOMAIN),
            'mn' => __('Mongolian', Settings::TEXTDOMAIN),
            'na' => __('Nauru', Settings::TEXTDOMAIN),
            'nv' => __('Navajo, Navaho', Settings::TEXTDOMAIN),
            'nd' => __('Northern Ndebele', Settings::TEXTDOMAIN),
            'ng' => __('Ndonga', Settings::TEXTDOMAIN),
            'ne' => __('Nepali', Settings::TEXTDOMAIN),
            'se' => __('Northern Sami', Settings::TEXTDOMAIN),
            'no' => __('Norwegian', Settings::TEXTDOMAIN),
            'nb' => __('Norwegian Bokmål', Settings::TEXTDOMAIN),
            'nn' => __('Norwegian Nynorsk', Settings::TEXTDOMAIN),
            'ii' => __('Nuosu, Sichuan Yi', Settings::TEXTDOMAIN),
            'oc' => __('Occitan (post 1500)', Settings::TEXTDOMAIN),
            'oj' => __('Ojibwa', Settings::TEXTDOMAIN),
            'or' => __('Oriya', Settings::TEXTDOMAIN),
            'om' => __('Oromo', Settings::TEXTDOMAIN),
            'os' => __('Ossetian, Ossetic', Settings::TEXTDOMAIN),
            'pi' => __('Pali', Settings::TEXTDOMAIN),
            'pa' => __('Panjabi, Punjabi', Settings::TEXTDOMAIN),
            'ps' => __('Pashto, Pushto', Settings::TEXTDOMAIN),
            'fa' => __('Persian', Settings::TEXTDOMAIN),
            'pl' => __('Polish', Settings::TEXTDOMAIN),
            'pt' => __('Portuguese', Settings::TEXTDOMAIN),
            'qu' => __('Quechua', Settings::TEXTDOMAIN),
            'rm' => __('Romansh', Settings::TEXTDOMAIN),
            'rn' => __('Rundi', Settings::TEXTDOMAIN),
            'ru' => __('Russian', Settings::TEXTDOMAIN),
            'sm' => __('Samoan', Settings::TEXTDOMAIN),
            'sg' => __('Sango', Settings::TEXTDOMAIN),
            'sa' => __('Sanskrit', Settings::TEXTDOMAIN),
            'sc' => __('Sardinian', Settings::TEXTDOMAIN),
            'sr' => __('Serbian', Settings::TEXTDOMAIN),
            'sn' => __('Shona', Settings::TEXTDOMAIN),
            'sd' => __('Sindhi', Settings::TEXTDOMAIN),
            'si' => __('Sinhala, Sinhalese', Settings::TEXTDOMAIN),
            'sk' => __('Slovak', Settings::TEXTDOMAIN),
            'sl' => __('Slovenian', Settings::TEXTDOMAIN),
            'so' => __('Somali', Settings::TEXTDOMAIN),
            'st' => __('Sotho, Southern', Settings::TEXTDOMAIN),
            'nr' => __('South Ndebele', Settings::TEXTDOMAIN),
            'es' => __('Spanish, Castilian', Settings::TEXTDOMAIN),
            'su' => __('Sundanese', Settings::TEXTDOMAIN),
            'sw' => __('Swahili', Settings::TEXTDOMAIN),
            'ss' => __('Swati', Settings::TEXTDOMAIN),
            'sv' => __('Swedish', Settings::TEXTDOMAIN),
            'tl' => __('Tagalog', Settings::TEXTDOMAIN),
            'ty' => __('Tahitian', Settings::TEXTDOMAIN),
            'tg' => __('Tajik', Settings::TEXTDOMAIN),
            'ta' => __('Tamil', Settings::TEXTDOMAIN),
            'tt' => __('Tatar', Settings::TEXTDOMAIN),
            'te' => __('Telugu', Settings::TEXTDOMAIN),
            'th' => __('Thai', Settings::TEXTDOMAIN),
            'bo' => __('Tibetan', Settings::TEXTDOMAIN),
            'ti' => __('Tigrinya', Settings::TEXTDOMAIN),
            'to' => __('Tonga (Tonga Islands)', Settings::TEXTDOMAIN),
            'ts' => __('Tsonga', Settings::TEXTDOMAIN),
            'tn' => __('Tswana', Settings::TEXTDOMAIN),
            'tr' => __('Turkish', Settings::TEXTDOMAIN),
            'tk' => __('Turkmen', Settings::TEXTDOMAIN),
            'tw' => __('Twi', Settings::TEXTDOMAIN),
            'ug' => __('Uighur, Uyghur', Settings::TEXTDOMAIN),
            'uk' => __('Ukrainian', Settings::TEXTDOMAIN),
            'ur' => __('Urdu', Settings::TEXTDOMAIN),
            'uz' => __('Uzbek', Settings::TEXTDOMAIN),
            've' => __('Venda', Settings::TEXTDOMAIN),
            'vi' => __('Vietnamese', Settings::TEXTDOMAIN),
            'vo' => __('Volap_k', Settings::TEXTDOMAIN),
            'wa' => __('Walloon', Settings::TEXTDOMAIN),
            'cy' => __('Welsh', Settings::TEXTDOMAIN),
            'fy' => __('Western Frisian', Settings::TEXTDOMAIN),
            'wo' => __('Wolof', Settings::TEXTDOMAIN),
            'xh' => __('Xhosa', Settings::TEXTDOMAIN),
            'yi' => __('Yiddish', Settings::TEXTDOMAIN),
            'yo' => __('Yoruba', Settings::TEXTDOMAIN),
            'za' => __('Zhuang, Chuang', Settings::TEXTDOMAIN),
            'zu' => __('Zulu', Settings::TEXTDOMAIN),
        );
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
     * Check if we are in debug mode.
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return (int) getenv('CISION_BLOCK_DEBUG') === 1;
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
