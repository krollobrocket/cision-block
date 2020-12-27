<?php

namespace CisionBlock\Cision;

use stdClass;

class Video extends File
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
    public $ThumbnailUrl;

    /**
     * Video constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        // TODO: Notice that this will set Url and IsMain which is not used.
        parent::__construct($data);
        $this->DownloadUrl = esc_url_raw($data->DownloadUrl);
        $this->Photographer = sanitize_text_field($data->Photographer);
        $this->ThumbnailUrl = esc_url_raw($data->ThumbnailUrl);
    }
}
