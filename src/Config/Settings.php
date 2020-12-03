<?php

namespace CisionBlock\Config;

use CisionBlock\Frontend\Frontend;

class Settings extends Base\Settings
{

    /**
    * Name of configuration option.
    *
    * @var string
    */
    private $optionName = '';

    /**
    * An array of settings.
    *
    * @var array
    */
    public $settings = array();

    const DEFAULT_ITEMS_PER_PAGE = 0;
    const DEFAULT_ITEM_COUNT = 50;
    const DEFAULT_CACHE_LIFETIME = 60 * 5;
    const DEFAULT_DATE_FORMAT = 'd-m-Y';
    const DEFAULT_IMAGE_STYLE = 'DownloadUrl';
    const MAX_ITEMS_PER_FEED =  100;
    const MAX_ITEMS_PER_PAGE = 100;
    const DEFAULT_FEED_TYPE = 'PRM';
    const DEFAULT_PAGE_INDEX = 1;
    const DEFAULT_LANGUAGE = '';
    const DEFAULT_READ_MORE_TEXT = 'Read more';
    const DISPLAY_MODE_ALL = 1;
    const DISPLAY_MODE_REGULATORY = 2;
    const DISPLAY_MODE_NON_REGULATORY = 3;
    const DEFAULT_DISPLAY_MODE = self::DISPLAY_MODE_ALL;
    const DEFAULT_MARK_REGULATORY_TEXT = 'Regulatory pressrelease';
    const DEFAULT_MARK_NON_REGULATORY_TEXT = 'Non-regulatory pressrelease';
    const DEFAULT_FILTER_ALL_TEXT = 'Show all';
    const DEFAULT_FILTER_REGULATORY_TEXT = 'Regulatory';
    const DEFAULT_FILTER_NON_REGULATORY_TEXT = 'Non-regulatory';
    const TEXTDOMAIN = 'cision-block';

    /**
     * @var array $default_settings
     */
    private $defaultSettings = array(
        'count' => self::DEFAULT_ITEM_COUNT,
        'source_uid' => '',
        'tags' => '',
        'categories' => '',
        'start_date' => '',
        'end_date' => '',
        'mark_regulatory' => false,
        'regulatory_text' => self::DEFAULT_MARK_REGULATORY_TEXT,
        'non_regulatory_text' => self::DEFAULT_MARK_NON_REGULATORY_TEXT,
        'show_filters' => false,
        'filter_all_text' => self::DEFAULT_FILTER_ALL_TEXT,
        'filter_regulatory_text' => self::DEFAULT_FILTER_REGULATORY_TEXT,
        'filter_non_regulatory_text' => self::DEFAULT_FILTER_NON_REGULATORY_TEXT,
        'search_term' => '',
        'image_style' => self::DEFAULT_IMAGE_STYLE,
        'use_https' => false,
        'date_format' => self::DEFAULT_DATE_FORMAT,
        'view_mode' => self::DEFAULT_DISPLAY_MODE,
        'language' => '',
        'readmore' => self::DEFAULT_READ_MORE_TEXT,
        'items_per_page' => self::DEFAULT_ITEMS_PER_PAGE,
        'cache_expire' => self::DEFAULT_CACHE_LIFETIME,
        'types' => array(self::DEFAULT_FEED_TYPE),
        'internal_links' => false,
        'base_slug' => 'cision',
        'version' => Frontend::VERSION,
    );

    /**
     * @return mixed|null
     */
    public function getDefaults()
    {
        return $this->defaultSettings;
    }
}
