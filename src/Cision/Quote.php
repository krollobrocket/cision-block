<?php

namespace CisionBlock\Cision;

use stdClass;

class Quote
{
    /**
     * @var string
     */
    public $Text;

    /**
     * @var string
     */
    public $Author;

    /**
     * Quote constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->Text = isset($data->Text) ? sanitize_text_field($data->Text) : '';
        $this->Author = isset($data->Author) ? esc_url_raw($data->Author) : '';
    }
}
