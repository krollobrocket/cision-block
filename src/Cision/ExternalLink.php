<?php

namespace CisionBlock\Cision;

use stdClass;

class ExternalLink
{
    /**
     * @var string
     */
    public $Title;

    /**
     * @var string
     */
    public $Url;

    /**
     * ExternalLink constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->Title = isset($data->Title) ? sanitize_text_field($data->Title) : '';
        $this->Url = isset($data->Url) ? esc_url_raw($data->Url) : '';
    }
}
