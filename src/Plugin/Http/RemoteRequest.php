<?php

namespace CisionBlock\Plugin\Http;

class RemoteRequest extends AbstractRequest
{
    /**
     * Performs a remote request.
     * @param string $url
     * @param array $args
     * @return false|string
     * @throws \Exception
     */
    public function execute($url, array $args = array())
    {
        $defaults = array(
            'method' => self::VERB_GET,
            'timeout' => self::CURL_TIMEOUT,
            'headers' => $this->headers,
        );
        $args = array_merge($defaults, $args);
        $response = wp_safe_remote_request($url, $args);
        if (!is_wp_error($response) && in_array(wp_remote_retrieve_response_code($response), array(200, 201))) {
            $result = new Response(
                wp_remote_retrieve_body($response),
                wp_remote_retrieve_headers($response),
                wp_remote_retrieve_response_code($response)
            );
        } else {
            throw new \Exception(
                wp_remote_retrieve_response_message($response),
                wp_remote_retrieve_response_code($response)
            );
        }
        return $result;
    }
}
