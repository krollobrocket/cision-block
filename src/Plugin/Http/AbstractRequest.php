<?php

namespace CisionBlock\Plugin\Http;

abstract class AbstractRequest implements RequestInterface
{
    protected $headers = array();

    /**
     * Set HTTP headers
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Set a HTTP header
     *
     * @param string $name
     * @param $value
     */
    public function addHeader(string $name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Remove a HTTP header
     * @param string $name
     */
    public function removeHeader(string $name)
    {
        unset($this->headers[$name]);
    }

    /**
     * Performs a HEAD request
     *
     * @param string $url
     * @param array $args
     * @return mixed|null
     */
    public function head(string $url, array $args = array())
    {
        return $this->execute($url, array('method' => self::VERB_HEAD) + $args);
    }

    /**
     * Performs a GET request.
     *
     * @param string $url
     * @param array $args
     * @return mixed|null
     */
    public function get(string $url, array $args = array())
    {
        return $this->execute($url, array('method' => self::VERB_GET) + $args);
    }

    /**
     * Performs a POST request.
     *
     * @param string $url
     * @param array $args
     * @return mixed|null
     */
    public function post(string $url, array $args = array())
    {
        return $this->execute($url, array('method' => self::VERB_POST) + $args);
    }

    /**
     * Performs a PUT request
     *
     * @param string $url
     * @param array $args
     * @return mixed|null
     */
    public function put(string $url, array $args = array())
    {
        return $this->execute($url, array('method' => self::VERB_PUT) + $args);
    }

    /**
     * Performs a PATCH request
     *
     * @param string $url
     * @param array $args
     * @return mixed|null
     */
    public function patch(string $url, array $args = array())
    {
        return $this->execute($url, array('method' => self::VERB_PATCH) + $args);
    }
}
