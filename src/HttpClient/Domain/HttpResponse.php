<?php

namespace Davamigo\HttpClient\Domain;

/**
 * Generic HTTP response interface
 *
 * @package Davamigo\HttpClient\Domain
 * @author davamigo@gmail.com
 */
interface HttpResponse
{
    /** Contants: HTTP response codes */
    const HTTP_CODE_CONTINUE                        = 100;
    const HTTP_CODE_SWITCHING_PROTOCOLS             = 101;
    const HTTP_CODE_PROCESSING                      = 102;

    const HTTP_CODE_OK                              = 200;
    const HTTP_CODE_CREATED                         = 201;
    const HTTP_CODE_ACCEPTED                        = 202;
    const HTTP_CODE_NON_AUTHORITATIVE_INFORMATION   = 203;
    const HTTP_CODE_NO_CONTENT                      = 204;
    const HTTP_CODE_RESET_CONTENT                   = 205;
    const HTTP_CODE_PARTIAL_CONTENT                 = 206;
    const HTTP_CODE_MULTI_STATUS                    = 207;
    const HTTP_CODE_ALREADY_REPORTED                = 208;
    const HTTP_CODE_IM_USED                         = 226;

    const HTTP_CODE_MULTIPLE_CHOICES                = 300;
    const HTTP_CODE_MOVED_PERMANENTLY               = 301;
    const HTTP_CODE_FOUND                           = 302;
    const HTTP_CODE_SEE_OTHER                       = 303;
    const HTTP_CODE_NOT_MODIFIED                    = 304;
    const HTTP_CODE_USE_PROXY                       = 305;
    const HTTP_CODE_TEMPORARY_REDIRECT              = 307;
    const HTTP_CODE_PERMANENT_REDIRECT              = 308;

    const HTTP_CODE_BAD_REQUEST                     = 400;
    const HTTP_CODE_UNAUTHORIZED                    = 401;
    const HTTP_CODE_PAYMENT_REQUIRED                = 402;
    const HTTP_CODE_FORBIDDEN                       = 403;
    const HTTP_CODE_NOT_FOUND                       = 404;
    const HTTP_CODE_METHOD_NOT_ALLOWED              = 405;
    const HTTP_CODE_NOT_ACEPTABLE                   = 406;
    const HTTP_CODE_PROXY_AUTHETICATION_REQUIRED    = 407;
    const HTTP_CODE_REQUEST_TIMEOUT                 = 408;
    const HTTP_CODE_CONFLICT                        = 409;
    const HTTP_CODE_GONE                            = 410;
    const HTTP_CODE_LENGTH_REQUIRED                 = 411;
    const HTTP_CODE_PRECONDITION_FAILED             = 412;
    const HTTP_CODE_REQUEST_ENTITY_TOO_LARGE        = 413;
    const HTTP_CODE_REQUEST_URI_TOO_LONG            = 414;
    const HTTP_CODE_UNSUPPORTED_MEDIA_TYPE          = 415;
    const HTTP_CODE_REQUEST_RANGE_NOT_SATISFIABLE   = 416;
    const HTTP_CODE_EXPECTATION_FAILED              = 417;
    const HTTP_CODE_UNPROCESSABLE_ENTITY            = 422;
    const HTTP_CODE_LOCKED                          = 423;
    const HTTP_CODE_FAILED_DEPENDENCY               = 424;
    const HTTP_CODE_RESERVED_FOR_WEBDAV             = 425;
    const HTTP_CODE_UPGRADE_REQUIRED                = 426;
    const HTTP_CODE_PRECONDITION_REQUIRED           = 428;
    const HTTP_CODE_TOO_MANY_REQUESTS               = 429;
    const HTTP_CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    const HTTP_CODE_INTERNAL_SERVER_ERROR           = 500;
    const HTTP_CODE_NOT_IMPLEMENTED                 = 501;
    const HTTP_CODE_BAD_GATEWAY                     = 502;
    const HTTP_CODE_SERVICE_UNAVAILABLE             = 503;
    const HTTP_CODE_GATEWAY_TIMEOUT                 = 504;
    const HTTP_CODE_HTTP_VERSION_NOT_SUPPORTED      = 505;
    const HTTP_CODE_VARIANT_ALSO_NEGOTIATES         = 506;
    const HTTP_CODE_INSUFFICIENT_STORAGE            = 507;
    const HTTP_CODE_LOOP_DETECTED                   = 508;
    const HTTP_CODE_NOT_EXTEDNDED                   = 510;
    const HTTP_CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /** Contants: Header keys */
    const HEADER_ACCEPT_RANGES          = 'Accept-Ranges';
    const HEADER_AGE                    = 'Age';
    const HEADER_ALLOW                  = 'Allow';
    const HEADER_CACHE_CONTROL          = 'Cache-Control';
    const HEADER_CONNECTION             = 'Connection';
    const HEADER_CONTENT_ENCODING       = 'Content-Encoding';
    const HEADER_CONTENT_LANGUAGE       = 'Content-Language';
    const HEADER_CONTENT_LENGTH         = 'Content-Length';
    const HEADER_CONTENT_LOCATION       = 'Content-Location';
    const HEADER_CONTENT_DISPOSITION    = 'Content-Disposition';
    const HEADER_CONTENT_MD5            = 'Content-MD5';
    const HEADER_CONTENT_RANGE          = 'Content-Range';
    const HEADER_CONTENT_TYPE           = 'Content-Type';
    const HEADER_DATE                   = 'Date';
    const HEADER_E_TAG                  = 'ETag';
    const HEADER_EXPIRES                = 'Expires';
    const HEADER_LAST_MODIFIED          = 'Last-Modified';
    const HEADER_LOCATION               = 'Location';
    const HEADER_PRAGMA                 = 'Pragma';
    const HEADER_PROXY_AUTHETICATE      = 'Proxy-Authenticate';
    const HEADER_RETRY_AFTER            = 'Retry-After';
    const HEADER_SERVER                 = 'Server';
    const HEADER_SET_COOKIE             = 'Set-Cookie';
    const HEADER_TRAILER                = 'Trailer';
    const HEADER_TRANSFER_ENCODING      = 'Transfer-Encoding';
    const HEADER_VARY                   = 'Vary';
    const HEADER_VIA                    = 'Via';
    const HEADER_WARNING                = 'Warning';
    const HEADER_WWW_AUTHENTICATE       = 'WWW-Authenticate';

    /**
     * Returns the response internal data (usually the real response object).
     *
     * @return object|array
     */
    public function getResponseData();

    /**
     * Get the response body
     *
     * @param bool $asString Set to TRUE to return a string of the body rather than a full body object
     *
     * @return HttpBody|string
     */
    public function getBody($asString = false);

    /**
     * Get the entire response as a string
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get a cURL transfer information
     *
     * @param string $key A single statistic to check
     * @return array|string|null Returns all stats if no key is set, a single stat if a key is set, or null if a key
     *                           is set and not found
     */
    public function getInfo($key = null);

    /**
     * Get the protocol used for the response (e.g. HTTP)
     *
     * @return string
     * @throws HttpException
     */
    public function getProtocol();

    /**
     * Get the HTTP protocol version
     *
     * @return string
     * @throws HttpException
     */
    public function getProtocolVersion();

    /**
     * Get the response status code
     *
     * @return integer
     * @throws HttpException
     */
    public function getStatusCode();

    /**
     * Get the response reason phrase: a human readable version of the numeric status code
     *
     * @return string
     * @throws HttpException
     */
    public function getReasonPhrase();

    /**
     * Get the the raw message headers as a string
     *
     * @return string
     */
    public function getRawHeaders();

    /**
     * Get all the headers as an array
     *
     * @return array
     */
    public function getHeaderLines();

    /**
     * Get a single header value
     *
     * @param string $header The name of the header
     * @return string|null Returns the value if the header found, or null if the header not found
     */
    public function getHeader($header);

    /**
     * Checks if HTTP Status code is Information (1xx)
     *
     * @return bool
     */
    public function isInformational();

    /**
     * Checks if HTTP Status code is Successful (2xx | 304)
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Checks if HTTP Status code is a Redirect (3xx)
     *
     * @return bool
     */
    public function isRedirect();

    /**
     * Checks if HTTP Status code is a Client Error (4xx)
     *
     * @return bool
     */
    public function isClientError();

    /**
     * Checks if HTTP Status code is Server Error (5xx)
     *
     * @return bool
     */
    public function isServerError();

    /**
     * Checks if HTTP Status code is Server OR Client Error (4xx or 5xx)
     *
     * @return boolean
     */
    public function isError();
}
