<?php
namespace CisionBlock;

use CisionBlock\Common\Singleton;
use CisionBlock\Config\Settings;
use CisionBlock\Widget\CisionBlockWidget;

class CisionBlockAdmin extends Singleton
{
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
        'image_style' => CISION_BLOCK_DEFAULT_IMAGE_STYLE,
        'use_https' => false,
        'date_format' => CISION_BLOCK_DEFAULT_DATE_FORMAT,
        'is_regulatory' => false,
        'language' => '',
        'readmore' => CISION_BLOCK_DEFAULT_READMORE_TEXT,
        'items_per_page' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
        'cache_expire' => CISION_BLOCK_DEFAULT_CACHE_LIFETIME,
        'types' => array(CISION_BLOCK_DEFAULT_FEED_TYPE),
        'version' => CISION_BLOCK_PLUGIN_VERSION,
    );

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
    * @var string $capability
    */
    private $capability = 'manage_options';

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
        $this->settings = new Settings(CISION_BLOCK_SETTINGS_OPTION);

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
        if ($file == 'cision-block/bootstrap.php')
            array_unshift( $links, $settings_link);

        return $links;
    }

    /**
     * Check if any updates needs to be performed.
     */
    public static function activate()
    {
        // Load settings.
        $settings = new Settings(CISION_BLOCK_SETTINGS_OPTION);

        if (version_compare($settings->get('version'), CISION_BLOCK_PLUGIN_VERSION, '<')) {
            // Rename old settings field.
            $settings->rename('source', 'source_uid');

            // Remove old sort_by field.
            $settings->remove('sort_by');

            // Set defaults.
            foreach (self::$default_settings as $key => $value) {
                $settings->add($key, $value);
            }

            $settings->version = CISION_BLOCK_PLUGIN_VERSION;

            // Store updated settings.
            $settings->save();
        }
    }

    /**
     * Add menu item for plugin.
     */
    public function addMenu()
    {
        add_submenu_page(
            'options-general.php',
            __('Cision Block Settings', CISION_BLOCK_TEXTDOMAIN),
            __('Cision Block Settings', CISION_BLOCK_TEXTDOMAIN),
            $this->capability,
            'cision-block',
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
                    'cision-block-count',
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
                    'cision-block-cache-expire',
                    FILTER_SANITIZE_NUMBER_INT
                );
                $this->settings->source_uid = filter_input(
                    INPUT_POST,
                    'cision-block-source-uid',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->types = filter_input(
                    INPUT_POST,
                    'cision-block-feed-types',
                    FILTER_SANITIZE_STRING,
                    FILTER_REQUIRE_ARRAY
                );
                $this->settings->language = filter_input(
                    INPUT_POST,
                    'cision-block-language',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->readmore = filter_input(
                    INPUT_POST,
                    'cision-block-readmore',
                    FILTER_SANITIZE_STRING
                );
                // Force lowercase language code.
                $this->settings->language = strtolower($this->settings->language);
                $this->settings->start_date = filter_input(
                    INPUT_POST,
                    'cision-block-start-date',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->end_date = filter_input(
                    INPUT_POST,
                    'cision-block-end-date',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->date_format = filter_input(
                    INPUT_POST,
                    'cision-block-date-format',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->tags = filter_input(
                    INPUT_POST,
                    'cision-block-tags',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->categories = filter_input(
                    INPUT_POST,
                    'cision-block-categories',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->categories = trim(strtolower($this->settings->categories));
                $this->settings->is_regulatory = filter_input(
                    INPUT_POST,
                    'cision-block-is-regulatory',
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->use_https = filter_input(
                    INPUT_POST,
                    'cision-block-use-https',
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->image_style = filter_input(
                    INPUT_POST,
                    'cision-block-image-style',
                    FILTER_SANITIZE_STRING
                );

                $this->settings->items_per_page = filter_input(
                    INPUT_POST,
                    'cision-block-items-per-page',
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'default' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
                            'min_range' => 0,
                            'max_range' => CISION_BLOCK_MAX_ITEMS_PER_PAGE,
                        ),
                    )
                );

                CisionBlock::getInstance()->clearCache();

                return $this->settings->save();
            }
        }
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-block-import-settings', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', CISION_BLOCK_TEXTDOMAIN));
            }

            // Verify nonce and referer.
            if (check_admin_referer('cision-block-settings-action', 'cision-block-settings-nonce')) {
                // Filter and sanitize form values.
                $jsonString = filter_input(
                    INPUT_POST,
                    'cision-block-json-markup',
                    FILTER_SANITIZE_STRING
                );
                $jsonString = str_replace(array("&#34;"), array("\""), $jsonString);
                $settings = json_decode($jsonString);

                // Filter and sanitize json values.
                $this->settings->count = filter_var(
                    $settings->count,
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'default' => CISION_BLOCK_DEFAULT_ITEM_COUNT,
                            'min_range' => 1,
                            'max_range' => CISION_BLOCK_MAX_ITEMS_PER_FEED,
                        ),
                    )
                );
                $this->settings->cache_expire = filter_var(
                    $settings->cache_expire,
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'default' => CISION_BLOCK_DEFAULT_CACHE_LIFETIME,
                            'min_range' => 0,
                        ),
                    )
                );
                $this->settings->source_uid = filter_var(
                    $settings->source_uid,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->types = filter_var(
                    $settings->types,
                    FILTER_SANITIZE_STRING,
                    FILTER_REQUIRE_ARRAY
                );
                if (!is_array($this->settings->types)) {
                    $this->settings->types = array();
                }
                $this->settings->language = filter_var(
                    $settings->language,
                    FILTER_SANITIZE_STRING
                );
                // Force lowercase language code.
                $this->settings->language = strtolower($this->settings->language);
                $this->settings->readmore = filter_var(
                    $settings->readmore,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->start_date = filter_var(
                    $settings->start_date,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->end_date = filter_var(
                    $settings->end_date,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->date_format = filter_var(
                    $settings->date_format,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->tags = filter_var(
                    $settings->tags,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->categories = filter_var(
                    $settings->categories,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->is_regulatory = filter_var(
                    $settings->is_regulatory,
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->use_https = filter_var(
                    $settings->use_https,
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->image_style = filter_var(
                    $settings->image_style,
                    FILTER_SANITIZE_STRING
                );
                $this->settings->items_per_page = filter_var(
                    $settings->items_per_page,
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'default' => CISION_BLOCK_DEFAULT_ITEMS_PER_PAGE,
                            'min_range' => 0,
                            'max_range' => CISION_BLOCK_MAX_ITEMS_PER_PAGE,
                        ),
                    )
                );

                return $this->settings->save();
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
     * Generates options for each image style.
     *
     * @return string
     */
    private function getImageStylesHtml()
    {
        $output = '';
        $image_styles = $this->getImageStyles();
        foreach ($image_styles as $key => $style) {
            $output .= '<option value="' . $key . '"' .
            ($key == $this->settings->image_style ? ' selected="selected"' : '') .
            '>' . $style['label'] . '</option>';
        }

        return $output;
    }

    /**
     * Display the settings page.
     */
    public function displaySettingsPage()
    {
        $feed_types = CisionBlock::getInstance()->getFeedTypes();
        $type_options = '';
        foreach ($feed_types as $key => $value) {
            $type_options .= '<option value="' . $key . '"' .
                ($this->settings->get('types') && in_array($key, $this->settings->get('types')) ? ' selected="selected"' : '') .
                '>' . $value . '</option>';
        }

        $output  = '<div class="wrap">';
        $output .= '<h1>' . __('Cision Block Settings', CISION_BLOCK_TEXTDOMAIN) . '</h1>';
        $output .= '<form action="" method="POST">';
        $output .= wp_nonce_field('cision-block-settings-action', 'cision-block-settings-nonce');
        $output .= '<table class="form-table">';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-count">' . __('Number of feed items', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="number" min="1" max="' . CISION_BLOCK_MAX_ITEMS_PER_FEED . '" name="cision-block-count" value="' . intval($this->settings->get('count')) . '" />';
        $output .= '<p class="description">' . __('The maximum number of items in the feed.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-source-uid">' . __('Cision Feed source id', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="text" class="regular-text" name="cision-block-source-uid" value="' . sanitize_text_field($this->settings->get('source_uid')) . '"/>';
        $output .= '<p class="description">' . __('A valid unique JSON identifier for your cision feed.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-feed-types">' . __('Type of feed items', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<select class="regular-text" name="cision-block-feed-types[]" multiple>';
        $output .= $type_options;
        $output .= '</select>';
        $output .= '<p class="description">' . __('Type of feed items to include.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-start-date">' . __('Start date', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="date" name="cision-block-start-date" value="' . $this->settings->get('start_date') . '" />';
        $output .= '<p class="description">' . __('Defines the start date of the date interval the press releases and/or reports are collected from. The format is 2001-12-31.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-end-date">' . __('End date', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="date" name="cision-block-end-date" value="' . $this->settings->get('end_date') . '" />';
        $output .= '<p class="description">' . __('Defines the end date of the date interval the press releases and/or reports are collected from. The format is 2001-12-31.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-tags">' . __('Tags', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="text" class="regular-text" name="cision-block-tags" value="' . $this->settings->get('tags') . '">';
        $output .= '<p class="description">' . __('Defines a filter on tags, this will return releases with these tags. One or several 
        tags can be provided separated with a comma.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-categories">' . __('Categories', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="text" class="regular-text" name="cision-block-categories" value="' . $this->settings->get('categories') . '">';
        $output .= '<p class="description">' . __('Defines a filter on categories, this will return releases with connected to these categories. One or several 
        categories can be provided separated with a comma.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-items-per-page">' . __('Items per page', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="number" min="0" max="' . CISION_BLOCK_MAX_ITEMS_PER_PAGE . '" name="cision-block-items-per-page" value="' . $this->settings->get('items_per_page') . '" />';
        $output .= '<p class="description">' . __('Number of items on each page (set to 0 to disable).', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-language">' . __('Language', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="text" name="cision-block-language" value="' . $this->settings->get('language') . '" />';
        $output .= '<p class="description">' . __('The language code for each feed item. For example \'en\' for english.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-readmore">' . __('Read more text', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="text" name="cision-block-readmore" value="' . $this->settings->get('readmore') . '" />';
        $output .= '<p class="description">' . __('The \'Read more\' button text. If this value is empty then the button will not be visible.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-is-regulatory">' . __('Regulatory', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="checkbox" name="cision-block-is-regulatory"' . ($this->settings->get('is_regulatory') ? ' checked="checked"' : '') . '" />';
        $output .= '<p class="description">' . __('Only retrieve regulatory items.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-date-format">' . __('Date format', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="text" name="cision-block-date-format" value="' . $this->settings->get('date_format') . '" />';
        $output .= '<p class="description">' . __('The format to use for dates.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '<p class="description">' . __('You can read more about the date/time formats: ', CISION_BLOCK_TEXTDOMAIN) . '<a target="_blank" href="http://php.net/manual/en/datetime.formats.php">' . __('Here', CISION_BLOCK_TEXTDOMAIN) . '</a></p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-image-style">' . __('Image style', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<select name="cision-block-image-style">';
        $output .= '<option value="">' . __('Select', CISION_BLOCK_TEXTDOMAIN) . '</option>';
        $output .= $this->getImageStylesHtml();
        $output .= '</select>';
        $output .= '<p class="description">' . __('The image format to use. If not set no images will be displayed.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-use-https">' . __('Use https', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="checkbox" name="cision-block-use-https"' . ($this->settings->get('use_https') ? ' checked="checked"' : '') . '" />';
        $output .= '<p class="description">' . __('Ensures that all images is handled over https.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-cache-expire">' . __('Cache lifetime', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<input type="number" min="0" name="cision-block-cache-expire" value="' . $this->settings->get('cache_expire') . '" />';
        $output .= '<p class="description">' . __('The cache lifetime.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<th scope="row">';
        $output .= '<label for="cision-block-json-markup">' . __('Json configuration', CISION_BLOCK_TEXTDOMAIN) . '</label>';
        $output .= '</th>';
        $output .= '<td>';
        $output .= '<textarea cols="60" rows="10" name="cision-block-json-markup">' . $this->settings->toJson() . '</textarea>';
        $output .= '<p class="description">' . __('The settings in json format.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        //$output .= '<p class="description">' . __('You can import settings from this textarea by clicking the \'Import settings\' button below.', CISION_BLOCK_TEXTDOMAIN) . '</p>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '</table>';
        //$output .= get_submit_button(__('Import settings', CISION_BLOCK_TEXTDOMAIN), 'secondary', 'cision-block-import-settings');
        $output .= get_submit_button(__('Save settings', CISION_BLOCK_TEXTDOMAIN), 'primary', 'cision-block-settings');
        $output .= '</form>';
        echo $output . '</div>';
    }
}
