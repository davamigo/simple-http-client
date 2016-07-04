<?php

namespace Davamigo\HttpClient\Domain;

/**
 * Client interface for send HTTP requests
 *
 * @package Davamigo\HttpClient\Domain
 * @author davamigo@gmail.com
 */
interface HttpClient
{
    /**
     * Returns the client internal data (usually the real client object).
     *
     * @return object|array
     */
    public function getClientData();

    /**
     * Create a GET request for the client
     *
     * @param string|array $uri     Resource URI
     * @param array        $headers HTTP headers
     * @param array        $options Options to apply to the request.
     * @return HttpRequest
     */
    public function get($uri = null, $headers = null, $options = array());


    /**
     * Create a HEAD request for the client
     *
     * @param string|array $uri     Resource URI
     * @param array        $headers HTTP headers
     * @param array        $options Options to apply to the request
     * @return HttpRequest
     */
    public function head($uri = null, $headers = null, array $options = array());

    /**
     * Create a POST request for the client
     *
     * @param string|array $uri      Resource URI
     * @param array        $headers  HTTP headers
     * @param array|string $postBody POST body. Can be a string or associative array of POST fields
     * @param array        $options  Options to apply to the request
     * @return HttpRequest
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array());

    /**
     * Create a PUT request for the client
     *
     * @param string|array    $uri     Resource URI
     * @param array           $headers HTTP headers
     * @param string|resource $body    Body to send in the request
     * @param array           $options Options to apply to the request
     * @return HttpRequest
     */
    public function put($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Create a PATCH request for the client
     *
     * @param string|array    $uri     Resource URI
     * @param array           $headers HTTP headers
     * @param string|resource $body    Body to send in the request
     * @param array           $options Options to apply to the request
     * @return HttpRequest
     */
    public function patch($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Create a DELETE request for the client
     *
     * @param string|array    $uri     Resource URI
     * @param array           $headers HTTP headers
     * @param string|resource $body    Body to send in the request
     * @param array           $options Options to apply to the request
     * @return HttpRequest
     */
    public function delete($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Create an OPTIONS request for the client
     *
     * @param string|array $uri     Resource URI
     * @param array        $options Options to apply to the request
     * @return HttpRequest
     */
    public function options($uri = null, array $options = array());

    /**
     * Sends a single request or an array of requests in parallel
     *
     * @param HttpRequest[]|HttpRequest $requests One or more HttpRequest objects to send
     * @return HttpRequest|array Returns a single Response or an array of Response objects
     * @throws HttpException on a request error
     */
    public function send($requests);

    /**
     * Set the User-Agent header to be used on all requests from the client
     *
     * @param string $userAgent User agent string
     * @return $this
     */
    public function setUserAgent($userAgent);

    /**
     * Get the default User-Agent string
     *
     * @return string
     */
    public function getDefaultUserAgent();
}
