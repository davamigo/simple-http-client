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

    /** @var CurlProxyInterface The cURL proxy */
    protected $curl;

    /**
     * CurlHttpRequest constructor
     *
     * @param resource           $handler
     * @param string|null        $uri
     * @param CurlProxyInterface $curl
     */
    public function __construct($handler, $uri = null, CurlProxyInterface $curl = null)
    {
        $this->handler = $handler;
        $this->uri = $uri;
        $this->curl = $curl ?: new CurlProxy();
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
     * Get the URI of the request
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the URI of the request
     *
     * @param string|array $uri Resource URI
     * @return bool true on success or false on failure.
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this->setOpt(CURLOPT_URL, $this->uri);
    }

    /**
     * Set an option for a cURL transfer
     *
     * @param mixed    $option
     * @param mixed    $value
     * @return bool true on success or false on failure.
     */
    public function setOpt($option, $value)
    {
        return $this->curl->setopt($this->handler, $option, $value);
    }

    /**
     * Send the request
     *
     * @return HttpResponseInterface
     * @throws HttpException on a request error
     */
    public function send()
    {
        $response = $this->curl->exec($this->handler);
        if (false === $response) {
            $msg = 'HttpClient: Can\'t load ' . $this->uri;
            if ($this->curl->errno($this->handler)) {
                $msg .= ' - ' . $this->curl->error($this->handler);
            }
            $this->curl->close($this->handler);
            throw new HttpException($msg);
        }

        $info = $this->curl->getinfo($this->handler);
        if (false === $info) {
            $msg = 'HttpClient: Error getting info from cURL';
            if ($this->curl->errno($this->handler)) {
                $msg .= ' - ' . $this->curl->error($this->handler);
            }
            $this->curl->close($this->handler);
            throw new HttpException($msg);
        }

        $this->curl->close($this->handler);

        return new CurlHttpResponse($response, $info);
    }
}
