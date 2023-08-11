<?php

namespace CisionBlock\Plugin\Http;

class RemoteRequest extends AbstractRequest
{
    /**
     * Performs a remote request.
     * @param string $url
     * @param array $args
     * @return Response
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
            $headers = wp_remote_retrieve_headers($response);
            if (!is_array($headers)) {
                $headers = $headers->getAll();
            }
            $result = new Response(
                wp_remote_retrieve_body($response),
                $headers,
                wp_remote_retrieve_response_code($response)
            );
        } else {
            $code = wp_remote_retrieve_response_code($response);
            /** @var \WP_Error|array $response */
            throw new \Exception(
                ($response instanceof \WP_Error ? $response->get_error_message() : ''),
                $code ? $code : 500
            );
        }
        return $result;
    }
}
