<?php

namespace Davamigo\HttpClient\CurlHttpClient;

use Davamigo\HttpClient\Domain\HttpBody;
use Davamigo\HttpClient\Domain\HttpException;
use Davamigo\HttpClient\Domain\HttpResponse as HttpResponseInterface;

/**
 * HTTP generic response using cURL
 *
 * @package Davamigo\HttpClient\CurlHttpClient
 * @author davamigo@gmail.com
 */
class CurlHttpResponse implements HttpResponseInterface
{
    /**
     * The body of the response (retrieved by curl_exec)
     *
     * @var mixed $body
     */
    private $body = null;

    /**
     * The header of the response (retrieved by curl_exec)
     *
     * @var string $header
     */
    private $header = null;

    /**
     * The information of the response (retrieved by curl_getinfo)
     *
     * @var array $info
     */
    private $info = null;

    /**
     * Indexes of the $this->info and options to use in $this->getInfo
     *
     * @var array
     */
    private static $opt = array(
        'url'                       => CURLINFO_EFFECTIVE_URL,
        'http_code'                 => CURLINFO_HTTP_CODE,
        'filetime'                  => CURLINFO_FILETIME,
        'total_time'                => CURLINFO_TOTAL_TIME,
        'namelookup_time'           => CURLINFO_NAMELOOKUP_TIME,
        'connect_time'              => CURLINFO_CONNECT_TIME,
        'pretransfer_time'          => CURLINFO_PRETRANSFER_TIME,
        'starttransfer_time'        => CURLINFO_STARTTRANSFER_TIME,
        'redirect_count'            => CURLINFO_REDIRECT_COUNT,
        'redirect_time'             => CURLINFO_REDIRECT_TIME,
        'size_upload'               => CURLINFO_SIZE_UPLOAD,
        'size_download'             => CURLINFO_SIZE_DOWNLOAD,
        'speed_download'            => CURLINFO_SPEED_DOWNLOAD,
        'speed_upload'              => CURLINFO_SPEED_UPLOAD,
        'header_size'               => CURLINFO_HEADER_SIZE,
        'certinfo'                  => CURLINFO_HEADER_OUT,
        'request_size'              => CURLINFO_REQUEST_SIZE,
        'ssl_verify_result'         => CURLINFO_SSL_VERIFYRESULT,
        'download_content_length'   => CURLINFO_CONTENT_LENGTH_DOWNLOAD,
        'upload_content_length'     => CURLINFO_CONTENT_LENGTH_UPLOAD,
        'content_type'              => CURLINFO_CONTENT_TYPE,
        'redirect_url'              => CURLINFO_REDIRECT_URL
    );

    /**
     * Array of reason phrases and their corresponding status codes
     *
     * @var array
     */
    private static $status = array(
        // 1xx Informational
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Checkpoint', // Unofficial

        // 2xx Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        // 3xx Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        // 4xx Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        420 => 'Method Failure', // Unofficial
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unassigned',
        426 => 'Upgrade required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        440 => 'Login Timeout', // Unofficial
        444 => 'No Response', // Unofficial
        450 => 'Blocked by Windows Parental Controls', // Unofficial
        451 => 'Unavailable for Legal Reasons', // Unofficial
        495 => 'SSL Certificate Error', // Unofficial
        496 => 'SSL Certificate Required', // Unofficial
        497 => 'HTTP Request Sent to HTTPS Port', // Unofficial
        499 => 'Request has been forbidden by antivirus', // Unofficial

        // 5xx Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded', // Unofficial
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        520 => 'Unknown Error', // Unofficial
        521 => 'Web Server Is Down', // Unofficial
        522 => 'Connection Timed Out', // Unofficial
        523 => 'Origin Is Unreachable', // Unofficial
        524 => 'Timeout Occurred', // Unofficial
        525 => 'SSL Handshake Failed', // Unofficial
        526 => 'Invalid SSL Certificate', // Unofficial
        530 => 'Site is frozen' // Unofficial
    );

    /** The default key used by getHeader when no header key found */
    const DEFAULT_HEADER_KEY = 'default!';

    /**
     * CurlHttpResponse constructor.
     *
     * @param string $response
     * @param array  $info
     */
    public function __construct($response, $info)
    {
        $headerSize = 0;
        if (isset($info['header_size']) && substr($response, 0, 4) == 'HTTP') {
            $headerSize = $info['header_size'];
        }

        if (!$headerSize) {
            $header = null;
            $body = $response;
        } else {
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
        }

        $this->body   = $body;
        $this->header = $header;
        $this->info   = $info;
    }

    /**
     * Returns the response internal data (usually the real response object).
     *
     * @return object|array
     */
    public function getResponseData()
    {
        return $this->getMessage();
    }

    /**
     * Get the response body
     *
     * @param bool $asString Set to TRUE to return a string of the body rather than a full body object
     *
     * @return HttpBody|string
     */
    public function getBody($asString = false)
    {
        return $this->body;
    }

    /**
     * Get the entire response as a string
     *
     * @return string
     */
    public function getMessage()
    {
        if (!$this->header) {
            return $this->body;
        }

        return $this->header . $this->body;
    }

    /**
     * Get a cURL transfer information
     *
     * @param int|string $key A single statistic to check
     * @return array|string|null Returns all stats if no key is set, a single stat if a key is set, or null if a key
     *                           is set and not found
     */
    public function getInfo($key = null)
    {
        if (!$key) {
            return $this->info;
        }

        $opt = array_flip(self::$opt);
        if (is_numeric($key) and isset($opt[$key])) {
            $key = $opt[$key];
        }

        if (isset($this->info[$key])) {
            return $this->info[$key];
        }

        return null;
    }

    /**
     * Get the protocol used for the response (e.g. HTTP)
     *
     * @return string
     * @throws HttpException
     */
    public function getProtocol()
    {
        $headerLines = $this->getHeaderLines();
        if (empty($headerLines)) {
            throw new HttpException('HttpClient: No response header detected!');
        }

        $statusLine = reset($headerLines);
        $pos = strpos($statusLine, ' ');
        if (false === $pos) {
            throw new HttpException('HttpClient: Invalid response header!');
        }

        $httpVersion = substr($statusLine, 0, $pos);
        $pos = strpos($httpVersion, '/');
        if (false === $pos) {
            throw new HttpException('HttpClient: Invalid response header!');
        }

        $protocol = substr($httpVersion, 0, $pos);

        return $protocol;
    }

    /**
     * Get the HTTP protocol version
     *
     * @return string
     * @throws HttpException
     */
    public function getProtocolVersion()
    {
        $headerLines = $this->getHeaderLines();
        if (empty($headerLines)) {
            throw new HttpException('HttpClient: No response header detected!');
        }

        $statusLine = reset($headerLines);
        $pos = strpos($statusLine, ' ');
        if (false === $pos) {
            throw new HttpException('HttpClient: Invalid response header!');
        }

        $httpVersion = substr($statusLine, 0, $pos);
        $pos = strpos($httpVersion, '/');
        if (false === $pos) {
            throw new HttpException('HttpClient: Invalid response header!');
        }

        $version = substr($httpVersion, $pos + 1);

        return $version;
    }

    /**
     * Get the response status code
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->getInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * Get the response reason phrase: a human readable version of the numeric status code
     *
     * @return string|null
     */
    public function getReasonPhrase()
    {
        $status = $this->getStatusCode();
        if (isset(self::$status[$status])) {
            return self::$status[$status];
        }

        return null;
    }

    /**
     * Get the the raw message headers as a string
     *
     * @return string
     */
    public function getRawHeaders()
    {
        return $this->header;
    }

    /**
     * Get all the header lines as an array
     *
     * @return array
     */
    public function getHeaderLines()
    {
        if (!$this->header) {
            return array();
        }

        $headers = explode("\r\n", $this->header);

        $result = array();
        foreach ($headers as $header) {
            if (!empty($header)) {
                $result[] = $header;
            }
        }
        return $result;
    }

    /**
     * Get a single header value
     *
     * @param string $key The name of the header (optional)
     * @return array|string|null Returns all the headers as array(key => value) if no key is set; a single header value
     *                           if the header is set; or null if the key is set and not found not found
     */
    public function getHeader($key = null)
    {
        $headers = $this->getHeaderLines();

        $result = array();
        foreach ($headers as $header) {
            $pos = strpos($header, ':');
            if (false === $pos) {
                $hkey = self::DEFAULT_HEADER_KEY;
                $hval = trim($header);
            } else {
                $hkey = strtolower(trim(substr($header, 0, $pos)));
                $hval = trim(substr($header, 1 + $pos));
            }

            if (!isset($result[$hkey])) {
                $result[$hkey] = $hval;
            } elseif (is_array($result[$hkey])) {
                $result[$hkey][] = $hval;
            } else {
                $hprev = $result[$hkey];
                $result[$hkey] = array($hprev, $hval);
            }
        }

        if (!$key) {
            return $result;
        }

        if (isset($result[$key])) {
            return $result[$key];
        }

        return null;
    }

    /**
     * Checks if HTTP Status code is Information (1xx)
     *
     * @return bool
     */
    public function isInformational()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode < 200;
    }

    /**
     * Checks if HTTP Status code is Successful (2xx | 304)
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $statusCode = $this->getStatusCode();
        return ($statusCode >= 200 && $statusCode < 300) || $statusCode == 304;
    }

    /**
     * Checks if HTTP Status code is a Redirect (3xx)
     *
     * @return bool
     */
    public function isRedirect()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode >= 300 && $statusCode < 400;
    }

    /**
     * Checks if HTTP Status code is a Client Error (4xx)
     *
     * @return bool
     */
    public function isClientError()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Checks if HTTP Status code is Server Error (5xx)
     *
     * @return bool
     */
    public function isServerError()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode >= 500 && $statusCode < 600;
    }

    /**
     * Checks if HTTP Status code is Server OR Client Error (4xx or 5xx)
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->isClientError() || $this->isServerError();
    }
}
