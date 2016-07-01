<?php

namespace Davamigo\HttpClient\CurlHttpClient;

use Davamigo\HttpClient\Domain\HttpException;
use Davamigo\HttpClient\Domain\HttpRequest as HttpRequestInterface;
use Davamigo\HttpClient\Domain\HttpResponse as HttpResponseInterface;

/**
 * HTTP generic request using cURL
 *
 * @package Davamigo\HttpClient\CurlHttpClient
 * @author davamigo@gmail.com
 */
class CurlHttpRequest implements HttpRequestInterface
{
    /** @var resource */
    protected $handler;

    /** @var string */
    protected $uri;

    /**
     * CurlHttpRequest constructor
     *
     * @param resource $handler
     * @param string   $uri
     */
    public function __construct($handler, $uri)
    {
        $this->handler = $handler;
        $this->uri = $uri;
    }

    /**
     * Returns the request internal data (usually the real request object).
     *
     * @return object|array
     */
    public function getRequestData()
    {
        return $this->handler;
    }

    /**
     * Send the request
     *
     * @return HttpResponseInterface
     * @throws HttpException on a request error
     */
    public function send()
    {
        $response = curl_exec($this->handler);
        if (false === $response) {
            $msg = 'HttpClient: Can\'t load ' . $this->uri;
            if (curl_errno($this->handler)) {
                $msg .= ' - ' . curl_error($this->handler);
            }
            curl_close($this->handler);
            throw new HttpException($msg);
        }

        $info = curl_getinfo($this->handler);
        if (false === $info) {
            $msg = 'HttpClient: Error getting info from cURL';
            if (curl_errno($this->handler)) {
                $msg .= ' - ' . curl_error($this->handler);
            }
            curl_close($this->handler);
            throw new HttpException($msg);
        }

        curl_close($this->handler);

        return new CurlHttpResponse($response, $info);
    }
}
