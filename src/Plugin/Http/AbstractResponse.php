<?php

namespace CisionBlock\Plugin\Http;

abstract class AbstractResponse implements ResponseInterface, \JsonSerializable
{
    /** @var array */
    private $headers;

    /** @var string */
    private $body;

    /** @var int */
    private $httpCode;

    public function __construct($body, $headers, $httpCode)
    {
        $this->body = $body;
        $this->headers = array_change_key_case($headers);
        $this->httpCode = $httpCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        $name = strtolower($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function toJSON()
    {
        return json_decode($this->body);
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->body;
    }
}
