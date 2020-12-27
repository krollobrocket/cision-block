<?php

namespace CisionBlock\Cision;

use stdClass;

class EmbeddedItem extends File
{
    /**
     * @var string
     */
    public $EmbedCode;

    /**
     * @var string
     */
    public $EmbedHost;

    /**
     * @var string
     */
    public $ThumbnailUrl;

    /**
     * EmbeddedItem constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        // TODO: Notice that this will set Url, FileName and IsMain which is not used.
        parent::__construct($data);
        $this->EmbedCode = isset($data->EmbedCode) ? sanitize_text_field($data->EmbedCode) : '';
        $this->EmbedHost = isset($data->EmbedHost) ? sanitize_text_field($data->EmbedHost) : '';
        $this->ThumbnailUrl = isset($data->ThumbnailUrl) ? esc_url_raw($data->ThumbnailUrl) : '';
    }
}
