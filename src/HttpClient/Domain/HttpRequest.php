<?php

namespace Davamigo\HttpClient\Domain;

/**
 * Generic HTTP request interface
 *
 * @package Davamigo\HttpClient\Domain
 * @author davamigo@gmail.com
 */
interface HttpRequest
{
    /**
     * Returns the request internal data (usually the real request object).
     *
     * @return object|array
     */
    public function getRequestData();

    /**
     * Set the URI of the request
     *
     * @param string|array $uri Resource URI
     * @return bool true on success or false on failure.
     */
    public function setUri($uri);

    /**
     * Get the URI of the request
     *
     * @return string
     */
    public function getUri();

    /**
     * Set an option for a cURL transfer
     *
     * @param mixed $option
     * @param mixed $value
     * @return bool true on success or false on failure.
     * @throws HttpException
     */
    public function setOpt($option, $value);

    /**
     * Send the request
     *
     * @return HttpResponse
     * @throws HttpException on a request error
     */
    public function send();
}
