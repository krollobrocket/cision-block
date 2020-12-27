<?php

namespace CisionBlock\Cision;

use stdClass;

class FeedItem
{
    /**
     * @var int
     */
    public $Id;

    /**
     * @var string
     */
    public $EncryptedId;

    /**
     * @var bool
     */
    public $IsRegulatory;

    /**
     * @var bool
     */
    public $SuppressImageOnCisionWire;

    /**
     * @var string
     */
    public $PublishDate;

    /**
     * @var string
     */
    public $LastChangeDate;

    /**
     * @var string
     */
    public $Title;

    /**
     * @var string
     */
    public $Intro;

    /**
     * @var string
     */
    public $Body;

    /**
     * @var string
     */
    public $Complete;

    /**
     * @var string
     */
    public $Contact;

    /**
     * @var string
     */
    public $CompanyInformation;

    /**
     * @var string
     */
    public $Header;

    /**
     * @var int
     */
    public $MainJobId;

    /**
     * @var int
     */
    public $SourceId;

    /**
     * @var bool
     */
    public $SourceIsListed;

    /**
     * @var string
     */
    public $SourceName;

    /**
     * @var string optional
     */
    public $SeOrganizationNumber;

    /**
     * @var string
     */
    public $LogoUrl;

    /**
     * @var string
     */
    public $HtmlCompanyInformation;

    /**
     * @var string
     */
    public $HtmlContact;

    /**
     * @var string
     */
    public $HtmlTitle;

    /**
     * @var string
     */
    public $HtmlHeader;

    /**
     * @var string
     */
    public $HtmlIntro;

    /**
     * @var string
     */
    public $HtmlBody;

    /**
     * @var string
     */
    public $IptcCode;

    /**
     * @var string
     */
    public $InformationType;

    /**
     * @var string
     */
    public $LanguageCode;

    /**
     * @var string
     */
    public $CountryCode;

    /**
     * @var string
     */
    public $CanonicalUrl;

    /**
     * @var string
     */
    public $CisionWireUrl;

    /**
     * @var string
     */
    public $RawHtmlUrl;

    /**
     * @var LanguageVersion[]
     */
    public $LanguageVersions = array();

    /**
     * @var string[]
     */
    public $Keywords = array();

    /**
     * @var Category[]
     */
    public $Categories = array();

    /**
     * @var Quote[]
     */
    public $Quotes = array();

    /**
     * @var string[]
     */
    public $QuickFacts = array();

    /**
     * @var ExternalLink[]
     */
    public $ExternalLinks = array();

    /**
     * @var EmbeddedItem[]
     */
    public $EmbeddedItems = array();

    /**
     * @var Video[]
     */
    public $Videos = array();

    /**
     * @var File[]
     */
    public $Files = array();

    /**
     * @var Image[]
     */
    public $Images = array();

    /**
     * @var Ticker[]
     */
    public $Tickers = array();

    /**
     * Custom data
     *
     * @var string
     */
    public $LinkTarget = '_blank';

    /**
     * FeedItem constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->Id = (int) $data->Id;
        $this->EncryptedId = isset($data->EncryptedId) ? sanitize_text_field($data->EncryptedId) : '';
        $this->IsRegulatory = isset($data->IsRegulatory) ? boolval($data->IsRegulatory) : false;
        $this->SuppressImageOnCisionWire = isset($data->SuppressImageOnCisionWire) ? boolval($data->SuppressImageOnCisionWire) : false;
        $this->PublishDate = isset($data->PublishDate) ? strtotime($data->PublishDate) : time();
        $this->LastChangeDate = isset($data->LastChangeDate) ? strtotime($data->LastChangeDate) : time();
        $this->Title = isset($data->Title) ? sanitize_text_field($data->Title) : '';
        $this->Intro = isset($data->Intro) ? sanitize_text_field($data->Intro) : '';
        $this->Body = isset($data->Body) ? sanitize_text_field($data->Body) : '';
        $this->Complete = isset($data->Complete) ? sanitize_text_field($data->Complete) : '';
        $this->Contact = isset($data->Contact) ? sanitize_text_field($data->Contact) : '';
        $this->CompanyInformation = isset($data->CompanyInformation) ? sanitize_text_field($data->CompanyInformation) : '';
        $this->Header = isset($data->Header) ? sanitize_text_field($data->Header) : '';
        $this->MainJobId = (int) $data->MainJobId;
        $this->SourceId = (int) $data->SourceId;
        $this->SourceIsListed = isset($data->SourceIsListed) ? boolval($data->SourceIsListed) : false;
        $this->SourceName = isset($data->SourceName) ? sanitize_text_field($data->SourceName) : '';
        $this->SeOrganizationNumber = isset($data->SeOrganizationNumber) ? sanitize_text_field($data->SeOrganizationNumber) : '';
        $this->LogoUrl = isset($data->LogoUrl) ? esc_url_raw($data->LogoUrl) : '';
        $this->HtmlCompanyInformation = isset($data->HtmlCompanyInformation) ? sanitize_text_field($data->HtmlCompanyInformation) : '';
        $this->HtmlContact = isset($data->HtmlContact) ? sanitize_text_field($data->HtmlContact) : '';
        $this->HtmlTitle = isset($data->HtmlTitle) ? sanitize_text_field($data->HtmlTitle) : '';
        $this->HtmlHeader = isset($data->HtmlHeader) ? sanitize_text_field($data->HtmlHeader) : '';
        $this->HtmlIntro = isset($data->HtmlIntro) ? sanitize_text_field($data->HtmlIntro) : '';
        $this->HtmlBody = isset($data->HtmlBody) ? sanitize_text_field($data->HtmlBody) : '';
        $this->IptcCode = isset($data->IptcCode) ? sanitize_text_field($data->IptcCode) : '';
        $this->InformationType = isset($data->InformationType) ? sanitize_text_field($data->InformationType) : '';
        $this->LanguageCode = isset($data->LanguageCode) ? sanitize_text_field($data->LanguageCode) : '';
        $this->CountryCode = isset($data->CountryCode) ? sanitize_text_field($data->CountryCode) : '';
        $this->CanonicalUrl = isset($data->CanonicalUrl) ? esc_url_raw($data->CanonicalUrl) : '';
        // TODO: Again, why is this empty when publishing a release through Cision?
        $this->CisionWireUrl = isset($data->CisionWireUrl) ? esc_url_raw($data->CisionWireUrl) : '';
        $this->RawHtmlUrl = isset($data->RawHtmlUrl) ? sanitize_text_field($data->RawHtmlUrl) : '';
        $this->LanguageVersions = array_map(function ($object) {
            return new LanguageVersion($object);
        }, $data->LanguageVersions);
        $this->Keywords = array_map(function ($str) {
            return sanitize_text_field($str);
        }, $data->Keywords);
        $this->Categories = array_map(function ($object) {
            return new Category($object);
        }, $data->Categories);
        $this->Quotes = array_map(function ($object) {
            return new Quote($object);
        }, $data->Quotes);
        $this->QuickFacts = array_map(function ($str) {
            return sanitize_text_field($str);
        }, $data->QuickFacts);
        $this->ExternalLinks = array_map(function ($object) {
            return new ExternalLink($object);
        }, $data->ExternalLinks);
        // TODO: Seems we get an error publishing in Cision if we have an embedded item
        $this->EmbeddedItems = array_map(function ($object) {
            return new EmbeddedItem($object);
        }, $data->EmbeddedItems);
        // TODO: We need to be able to test videos, seems like you need to connect a
        //  youtube account in Cision and I am not able to do this.
        $this->Videos = array_map(function ($object) {
            return new Video($object);
        }, $data->Videos);
        $this->Files = array_map(function ($object) {
            return new File($object);
        }, $data->Files);
        $this->Images = array_map(function ($object) {
            return new Image($object);
        }, $data->Images);
//        PRINT '<PRE>';
//        VAR_DUMP($data->EmbeddedItems);
//        exiT;
        $this->Tickers = array_map(function ($object) {
            return new Ticker($object);
        }, $data->Tickers);
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->Images;
    }

    /**
     * @param string $CisionWireUrl
     */
    public function setCisionWireUrl($CisionWireUrl)
    {
        $this->CisionWireUrl = $CisionWireUrl;
    }

    /**
     * @param $target
     */
    public function setLinkTarget($target)
    {
        $this->LinkTarget = $target;
    }

    /**
     * @param $language
     * @return bool
     */
    public function hasLanguage($language)
    {
        return $this->LanguageCode === $language;
    }

    /**
     * @param array $categories
     * @return bool
     */
    public function hasCategory(array $categories)
    {
        foreach ($this->Categories as $category) {
            if (in_array(strtolower($category->Name), $categories)) {
                return true;
            }
        }
    }

    /**
     * @param array $tags
     * @return bool
     */
    public function hasTag(array $tags)
    {
        foreach ($this->Keywords as $keyword) {
            if (in_array(strtolower($keyword), $tags)) {
                return true;
            }
        }
    }

    /**
     * @param array $types
     * @return bool
     */
    public function hasInformationType(array $types)
    {
        return in_array($this->InformationType, $types);
    }
}
