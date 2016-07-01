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
     * Send the request
     *
     * @return HttpResponse
     * @throws HttpException on a request error
     */
    public function send();
}
