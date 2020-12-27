<?php

namespace CisionBlock\Cision;

use stdClass;

class File
{
    /**
     * UTC DateTime string
     * @var string
     */
    public $CreateDate;

    /**
     * @var string
     */
    public $Description;

    /**
     * @var string
     */
    public $Title;

    /**
     * @var bool
     */
    public $IsMain;

    /**
     * @var string
     */
    public $MediaType;

    /**
     * @var string
     */
    public $Url;

    /**
     * @var string
     */
    public $FileName;

    /**
     * @var array
     */
    public $Keywords = array();

    /**
     * File constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->CreateDate = isset($data->CreateDate) ? strtotime($data->CreateDate) : time();
        $this->Description = isset($data->Description) ? sanitize_text_field($data->Description) : '';
        $this->Title = isset($data->Title) ? sanitize_text_field($data->Title) : '';
        $this->IsMain = isset($data->IsMain) ? boolval($data->IsMain) : false;
        $this->MediaType = isset($data->MediaType) ? sanitize_text_field($data->MediaType) : '';
        $this->Url = isset($data->Url) ? esc_url_raw($data->Url) : '';
        $this->FileName = isset($data->FileName) ? sanitize_text_field($data->FileName) : '';
        $this->Keywords = array_map(function ($str) {
            return sanitize_text_field($str);
        }, $data->Keywords);
    }
}
