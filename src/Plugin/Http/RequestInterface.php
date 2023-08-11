<?php

namespace CisionBlock\Plugin\Http;

interface RequestInterface
{
    const CURL_TIMEOUT = 10000;
    const VERB_HEAD = 'HEAD';
    const VERB_GET = 'GET';
    const VERB_POST = 'POST';
    const VERB_PUT = 'PUT';
    const VERB_PATCH = 'PATCH';
    const VERB_OPTIONS = 'OPTIONS';

    public function head(string $url, array $args = array());
    public function get(string $url, array $args = array());
    public function post(string $url, array $args = array());
    public function put(string $url, array $args = array());
    public function patch(string $url, array $args = array());
    public function execute(string $url, array $args = array());
}
