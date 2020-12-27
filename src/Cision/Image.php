<?php

namespace CisionBlock\Cision;

use stdClass;

class Image extends File
{
    /**
     * @var string
     */
    public $DownloadUrl;

    /**
     * @var string
     */
    public $Photographer;

    /**
     * @var string
     */
    public $UrlTo100x100Thumbnail;

    /**
     * @var string
     */
    public $UrlTo100x100ArResized;

    /**
     * @var string
     */
    public $UrlTo200x200Thumbnail;

    /**
     * @var string
     */
    public $UrlTo200x200ArResized;

    /**
     * @var string
     */
    public $UrlTo400x400ArResized;

    /**
     * @var string
     */
    public $UrlTo800x800ArResized;

    /**
     * Image constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        // TODO: Notice that this will set Url and IsMain which is not used.
        parent::__construct($data);
        $this->DownloadUrl = isset($data->DownloadUrl) ? esc_url_raw($data->DownloadUrl) : '';
        $this->Photographer = isset($data->Photographer) ? sanitize_text_field($data->Photographer) : '';
        $this->UrlTo100x100Thumbnail = isset($data->UrlTo100x100Thumbnail) ? esc_url_raw($data->UrlTo100x100Thumbnail) : '';
        $this->UrlTo100x100ArResized = isset($data->UrlTo100x100ArResized) ? esc_url_raw($data->UrlTo100x100ArResized) : '';
        $this->UrlTo200x200Thumbnail = isset($data->UrlTo200x200Thumbnail) ? esc_url_raw($data->UrlTo200x200Thumbnail) : '';
        $this->UrlTo200x200ArResized = isset($data->UrlTo200x200ArResized) ? esc_url_raw($data->UrlTo200x200ArResized) : '';
        $this->UrlTo400x400ArResized = isset($data->UrlTo400x400ArResized) ? esc_url_raw($data->UrlTo400x400ArResized) : '';
        $this->UrlTo800x800ArResized = isset($data->UrlTo800x800ArResized) ? esc_url_raw($data->UrlTo800x800ArResized) : '';
    }
}
