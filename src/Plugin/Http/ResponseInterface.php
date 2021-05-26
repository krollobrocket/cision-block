<?php

namespace CisionBlock\Plugin\Http;

interface ResponseInterface
{
    public function getHeaders();
    public function getHeader($name);
    public function getBody();
    public function toJSON();
    public function getHttpCode();
}
