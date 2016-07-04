<?php

namespace Davamigo\HttpClient\CurlHttpClient;

use Davamigo\HttpClient\Domain\HttpClient as HttpClientInterface;
use Davamigo\HttpClient\Domain\HttpException;
use Davamigo\HttpClient\Domain\HttpRequest as HttpRequestInterface;
use Davamigo\HttpClient\Domain\HttpResponse as HttpResponseInterface;

/**
 * HTTP generic client using cURL
 *
 * @package Davamigo\HttpClient\CurlHttpClient
 * @author davamigo@gmail.com
 */
class CurlHttpClient implements HttpClientInterface
{
    /** @var string The user agent to use in the calls */
    protected $userAgent = null;

    /** @var CurlProxyInterface The cURL proxy */
    protected $curlProxy;

    /**
     * CurlHttpClient constructor.
     *
     * @param CurlProxyInterface $curlProxy
     */
    public function __construct(CurlProxyInterface $curlProxy = null)
    {
        $this->curlProxy = $curlProxy ?: new CurlProxy();
    }

    /**
     * Returns the client internal data (usually the real client object).
     *
     * @return object|array
     */
    public function getClientData()
    {
        return $this->curlProxy;
    }

    /**
     * Set the User-Agent header to be used on all requests from the client
     *
     * @param string $userAgent User agent string
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Get the default User-Agent string
     *
     * @return string
     */
    public function getDefaultUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Create a GET request for the client
     *
     * @param string|array $uri Resource URI
     * @param array        $headers HTTP headers
     * @param array        $options Options to apply to the request.
     * @return HttpRequestInterface
     */
    public function get($uri = null, $headers = null, $options = array())
    {
        return $this->createRequest($uri, 'GET', $headers, null, $options);
    }

    /**
     * Create a HEAD request for the client
     *
     * @param string|array $uri Resource URI
     * @param array        $headers HTTP headers
     * @param array        $options Options to apply to the request
     * @return HttpRequestInterface
     */
    public function head($uri = null, $headers = null, array $options = array())
    {
        return $this->createRequest($uri, 'HEAD', $headers, null, $options);
    }

    /**
     * Create a POST request for the client
     *
     * @param string|array $uri Resource URI
     * @param array        $headers HTTP headers
     * @param array|string $postBody POST body. Can be a string or associative array of POST fields
     * @param array        $options Options to apply to the request
     * @return HttpRequestInterface
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        return $this->createRequest($uri, 'POST', $headers, $postBody, $options);
    }

    /**
     * Create a PUT request for the client
     *
     * @param string|array    $uri Resource URI
     * @param array           $headers HTTP headers
     * @param string|resource $body Body to send in the request
     * @param array           $options Options to apply to the request
     * @return HttpRequestInterface
     */
    public function put($uri = null, $headers = null, $body = null, array $options = array())
    {
        return $this->createRequest($uri, 'PUT', $headers, $body, $options);
    }

    /**
     * Create a PATCH request for the client
     *
     * @param string|array    $uri Resource URI
     * @param array           $headers HTTP headers
     * @param string|resource $body Body to send in the request
     * @param array           $options Options to apply to the request
     * @return HttpRequestInterface
     */
    public function patch($uri = null, $headers = null, $body = null, array $options = array())
    {
        return $this->createRequest($uri, 'PATCH', $headers, $body, $options);
    }

    /**
     * Create a DELETE request for the client
     *
     * @param string|array    $uri Resource URI
     * @param array           $headers HTTP headers
     * @param string|resource $body Body to send in the request
     * @param array           $options Options to apply to the request
     * @return HttpRequestInterface
     */
    public function delete($uri = null, $headers = null, $body = null, array $options = array())
    {
        return $this->createRequest($uri, 'DELETE', $headers, $body, $options);
    }

    /**
     * Create an OPTIONS request for the client
     *
     * @param string|array $uri Resource URI
     * @param array        $options Options to apply to the request
     * @return HttpRequestInterface
     */
    public function options($uri = null, array $options = array())
    {
        return $this->createRequest($uri, 'OPTIONS', null, null, $options);
    }

    /**
     * Sends a single request or an array of requests in parallel
     *
     * @param HttpRequestInterface|HttpRequestInterface[] $requests One or more request objects to send
     * @return HttpResponseInterface|HttpResponseInterface[] Returns a single response or an array of response objects
     * @throws HttpException on a request error
     */
    public function send($requests)
    {
        if ($requests instanceof HttpRequestInterface) {
            return $requests->send();
        } elseif (is_array($requests)) {
            $result = array();
            foreach ($requests as $request) {
                if ($request instanceof HttpRequestInterface) {
                    $result[] = $request->send();
                } else {
                    throw new HttpException(
                        'HttpClient: Invalid request: ' .
                        (is_object($request) ? get_class($request) : gettype($request))
                    );
                }
            }
            return $result;

        } else {
            throw new HttpException(
                'HttpClient: Invalid request: ' .
                (is_object($requests) ? get_class($requests) : gettype($requests))
            );
        }
    }

    /**
     * @param string|array          $uri     Resource URI
     * @param string                $method  HTTP method (ie: GET)
     * @param array                 $headers HTTP headers
     * @param string|array|resource $body    Body to send in the request
     * @param array                 $options Options to apply to the request.
     * @return CurlHttpRequest
     * @throws HttpException
     */
    public function createRequest($uri, $method = 'GET', $headers = null, $body = null, array $options = array())
    {
        $handler = $this->curlProxy->init();

        $request = new CurlHttpRequest($handler, $uri, $this->curlProxy);

        if ($uri) {
            $request->setOpt(CURLOPT_URL, $uri);
        }

        switch (strtolower($method)) {
            case 'get':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_POST, false);
                break;

            case 'head':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
                $request->setOpt(CURLOPT_NOBODY, true);
                break;

            case 'post':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_POST, true);
                $request->setOpt(CURLOPT_POSTFIELDS, $body);
                break;

            case 'put':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
                $request->setOpt(CURLOPT_POSTFIELDS, $body);
                break;

            case 'patch':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
                $request->setOpt(CURLOPT_POSTFIELDS, $body);
                break;

            case 'delete':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            case 'options':
                $request->setOpt(CURLOPT_RETURNTRANSFER, true);
                $request->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
                break;

            default:
                throw new HttpException('HttpClient: Method ' . $method . ' not implemented yet!');
        }

        // Add custom headers to the request
        if ($headers && is_array($headers) && !empty($headers)) {
            foreach ($headers as $header => $value) {
                $request->setOpt(CURLOPT_HTTPHEADER, array($header . ': ' . $value));
            }
        }

        // Ensure the header option is present and true
        if (!$options || !is_array($options) || empty($options)) {
            $options = array();
        }
        $options[CURLOPT_HEADER] = true;
        if (isset($options['header'])) {
            unset($options['header']);
        }

        // Add the options to the request
        foreach ($options as $option => $value) {
            $request->setOpt($option, $value);
        }

        // Add the user agent if necessary
        if ($this->userAgent) {
            $request->setOpt(CURLOPT_USERAGENT, $this->userAgent);
        }

        return $request;
    }
}
