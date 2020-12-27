<?php

namespace CisionBlock\Cision;

use stdClass;

class LanguageVersion
{
    /**
     * @var string
     */
    public $Code;

    /**
     * @var int
     */
    public $ReleaseId;

    /**
     * LanguageVersion constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->Code = isset($data->Code) ? sanitize_text_field($data->Code) : '';
        $this->ReleaseId = (int) $data->ReleaseId;
    }
}
