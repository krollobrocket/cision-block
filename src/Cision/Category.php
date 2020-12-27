<?php

namespace CisionBlock\Cision;

use stdClass;

class Category
{
    /**
     * @var string
     */
    public $Code;

    /**
     * @var string
     */
    public $Name;

    /**
     * Category constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->Code = isset($data->Code) ? sanitize_text_field($data->Code) : '';
        $this->Name = isset($data->Name) ? sanitize_text_field($data->Name) : '';
    }
}
