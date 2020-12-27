<?php

namespace CisionBlock\Cision;

use stdClass;

class Ticker
{
    /**
     * @var string
     */
    public $Symbol;

    /**
     * @var string
     */
    public $ISIN;

    /**
     * @var bool
     */
    public $PrimaryListing;

    /**
     * @var bool
     */
    public $IsShare;

    /**
     * @var string
     */
    public $MarketPlaceSymbol;

    /**
     * @var string
     */
    public $MarketPlaceName;

    /**
     * @var string
     */
    public $MarketPlaceBloombergCode;

    /**
     * @var string
     */
    public $MarketPlaceMarketWireCode;

    /**
     * @var bool
     */
    public $MarketPlaceIsRegulated;

    /**
     * @var string
     */
    public $MarketPlaceCountryCode;

    /**
     * Ticker constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->Symbol = isset($data->Symbol) ? sanitize_text_field($data->Symbol) : '';
        $this->ISIN = isset($data->ISIN) ? sanitize_text_field($data->ISIN) : '';
        $this->PrimaryListing = isset($data->PrimaryListing) ? boolval($data->PrimaryListing) : '';
        $this->IsShare = isset($data->IsShare) ? boolval($data->IsShare) : false;
        $this->MarketPlaceSymbol = isset($data->MarketPlaceSymbol) ? sanitize_text_field($data->MarketPlaceSymbol) : '';
        $this->MarketPlaceName = isset($data->MarketPlaceName) ? sanitize_text_field($data->MarketPlaceName) : '';
        $this->MarketPlaceBloombergCode = isset($data->MarketPlaceBloombergCode) ? sanitize_text_field($data->MarketPlaceBloombergCode) : '';
        $this->MarketPlaceMarketWireCode = isset($data->MarketPlaceMarketWireCode) ? sanitize_text_field($data->MarketPlaceMarketWireCode) : '';
        $this->MarketPlaceIsRegulated = isset($data->MarketPlaceIsRegulated) ? boolval($data->MarketPlaceIsRegulated) : false;
        $this->MarketPlaceCountryCode = isset($data->MarketPlaceCountryCode) ? sanitize_text_field($data->MarketPlaceCountryCode) : '';
    }
}
