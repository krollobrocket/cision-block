<?php

namespace CisionBlock\Plugin\Http;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * Performs a HEAD request
     *
     * @param string $url
     * @param array $args
     * @return mixed|null
     */
    public function head($url, array $args = array())
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
    public function get($url, array $args = array())
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
    public function post($url, array $args = array())
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
    public function put($url, array $args = array())
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
    public function patch($url, array $args = array())
    {
        return $this->execute($url, array('method' => self::VERB_PATCH) + $args);
    }
}
