<?php

namespace CisionBlock\Cision;

use stdClass;

class Feed implements \Iterator
{
    /**
     * @var int
     */
    public $PageIndex;

    /**
     * @var int
     */
    public $PageSize;

    /**
     * @var int
     */
    public $TotalFoundReleases;

    /**
     * @var int
     */
    public $TotalFoundMedias;

    /**
     * @var string
     */
    public $ReleaseFeedIdentifier;

    /**
     * @var string
     */
    public $MediaFeedIdentifier;

    /**
     * @var string
     */
    public $Title;

    /**
     * @var string
     */
    public $Author;

    /**
     * @var string
     */
    public $DatePackaged;

    /**
     * @var FeedItem[]
     */
    public $Releases = array();

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * Feed constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->PageIndex = (int) $data->PageIndex;
        $this->PageSize = (int) $data->PageSize;
        $this->TotalFoundReleases = (int) $data->TotalFoundReleases;
        $this->TotalFoundMedias = (int) $data->TotalFoundMedias;
        $this->ReleaseFeedIdentifier = sanitize_text_field($data->ReleaseFeedIdentifier);
        $this->MediaFeedIdentifier = sanitize_text_field($data->MediaFeedIdentifier);
        $this->Title = sanitize_text_field($data->Title);
        $this->Author = sanitize_text_field($data->Author);
        $this->DatePackaged = strtotime($data->DatePackaged);
        foreach ($data->Releases as $feedItem) {
            $this->Releases[] = new FeedItem($feedItem);
        }
    }

    /**
     * @inheridoc
     */
    public function valid()
    {
        return $this->position < count($this->Releases);
    }

    /**
     * @inheridoc
     */
    public function current()
    {
        return $this->Releases[$this->position];
    }

    /**
     * @inheridoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheridoc
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @inheridoc
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
