<?php

namespace Davamigo\HttpClient\CurlHttpClient;

use Davamigo\HttpClient\Domain\HttpException;
use Davamigo\HttpClient\Domain\HttpRequest as HttpRequestInterface;
use Davamigo\HttpClient\Domain\HttpResponse as HttpResponseInterface;

// Define constant CURLOPT_CLOSEFUNCTION, deprecated since 7.15.5
if (!defined('CURLOPT_CLOSEFUNCTION')) {
    define('CURLOPT_CLOSEFUNCTION', null);
}

// Define constant CURLOPT_FTPASCII, deprecated since 7.15.5
if (!defined('CURLOPT_FTPASCII')) {
    define('CURLOPT_FTPASCII', null);
}

// Define constant CURLOPT_HTTPREQUEST, deprecated since 7.15.5
if (!defined('CURLOPT_HTTPREQUEST')) {
    define('CURLOPT_HTTPREQUEST', null);
}

// Define constant CURLOPT_MUTE, deprecated since 7.15.5
if (!defined('CURLOPT_MUTE')) {
    define('CURLOPT_MUTE', null);
}

// Define constant CURLOPT_NOTHING, deprecated since 7.11.0
if (!defined('CURLOPT_NOTHING')) {
    define('CURLOPT_NOTHING', null);
}

// Define constant CURLOPT_PASSWDDATA, deprecated since 7.15.5
if (!defined('CURLOPT_PASSWDDATA')) {
    define('CURLOPT_PASSWDDATA', null);
}

// Define constant CURLOPT_PASSWDFUNCTION, deprecated since 7.15.5
if (!defined('CURLOPT_PASSWDFUNCTION')) {
    define('CURLOPT_PASSWDFUNCTION', null);
}

// Define constant CURLOPT_PASV_HOST, deprecated since 7.15.5
if (!defined('CURLOPT_PASV_HOST')) {
    define('CURLOPT_PASV_HOST', null);
}

// Define constant CURLOPT_SAFE_UPLOAD, deprecated since unknown
if (!defined('CURLOPT_SAFE_UPLOAD')) {
    define('CURLOPT_SAFE_UPLOAD', null);
}

// Define constant CURLOPT_SOURCE_HOST, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_HOST')) {
    define('CURLOPT_SOURCE_HOST', null);
}

// Define constant CURLOPT_SOURCE_PATH, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_PATH')) {
    define('CURLOPT_SOURCE_PATH', null);
}

// Define constant CURLOPT_SOURCE_PORT, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_PORT')) {
    define('CURLOPT_SOURCE_PORT', null);
}

// Define constant CURLOPT_SOURCE_POSTQUOTE, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_POSTQUOTE')) {
    define('CURLOPT_SOURCE_POSTQUOTE', null);
}

// Define constant CURLOPT_SOURCE_PREQUOTE, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_PREQUOTE')) {
    define('CURLOPT_SOURCE_PREQUOTE', null);
}

// Define constant CURLOPT_SOURCE_QUOTE, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_QUOTE')) {
    define('CURLOPT_SOURCE_QUOTE', null);
}

// Define constant CURLOPT_SOURCE_URL, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_URL')) {
    define('CURLOPT_SOURCE_URL', null);
}

// Define constant CURLOPT_SOURCE_USERPWD, deprecated since 7.15.5
if (!defined('CURLOPT_SOURCE_USERPWD')) {
    define('CURLOPT_SOURCE_USERPWD', null);
}

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
    protected $curlProxy;

    /** @var array Valid global options for curl_setopt() */
    static public $opt = array(
        'any'                       => CURLAUTH_ANY,
        'any_safe'                  => CURLAUTH_ANYSAFE,
        'autoreferer'               => CURLOPT_AUTOREFERER,
        'binary_transfer'           => CURLOPT_BINARYTRANSFER,
        'buffer_size'               => CURLOPT_BUFFERSIZE,
        'ca_info'                   => CURLOPT_CAINFO,
        'ca_path'                   => CURLOPT_CAPATH,
        'cert_info'                 => CURLOPT_CERTINFO,
        'connect_timeout'           => CURLOPT_CONNECTTIMEOUT,
        'connect_timeout_ms'        => CURLOPT_CONNECTTIMEOUT_MS,
        'cookie'                    => CURLOPT_COOKIE,
        'cookie_file'               => CURLOPT_COOKIEFILE,
        'cookie_jar'                => CURLOPT_COOKIEJAR,
        'cookie_session'            => CURLOPT_COOKIESESSION,
        'close_function'            => CURLOPT_CLOSEFUNCTION,
        'crlf'                      => CURLOPT_CRLF,
        'custom_request'            => CURLOPT_CUSTOMREQUEST,
        'dns_cache_tiemeout'        => CURLOPT_DNS_CACHE_TIMEOUT,
        'dns_use_global_cache'      => CURLOPT_DNS_USE_GLOBAL_CACHE,
        'egdsocket'                 => CURLOPT_EGDSOCKET,
        'enconding'                 => CURLOPT_ENCODING,
        'fail_on_error'             => CURLOPT_FAILONERROR,
        'file'                      => CURLOPT_FILE,
        'file_time'                 => CURLOPT_FILETIME,
        'follow_location'           => CURLOPT_FOLLOWLOCATION,
        'forbid_reuse'              => CURLOPT_FORBID_REUSE,
        'fresh_connect'             => CURLOPT_FRESH_CONNECT,
        'ftp_append'                => CURLOPT_FTPAPPEND,
        'ftp_ascii'                 => CURLOPT_FTPASCII,
        'ftp_create_missing_dirs'   => CURLOPT_FTP_CREATE_MISSING_DIRS,
        'ftp_list_only'             => CURLOPT_FTPLISTONLY,
        'ftp_port'                  => CURLOPT_FTPPORT,
        'ftp_ssl_auth'              => CURLOPT_FTPSSLAUTH,
        'ftp_use_eprt'              => CURLOPT_FTP_USE_EPRT,
        'ftp_use_epsv'              => CURLOPT_FTP_USE_EPSV,
        'header'                    => CURLOPT_HEADER,
        'header_function'           => CURLOPT_HEADERFUNCTION,
        'headerout'                 => CURLINFO_HEADER_OUT,
        'http_200_aliases'          => CURLOPT_HTTP200ALIASES,
        'http_auth'                 => CURLOPT_HTTPAUTH,
        'http_get'                  => CURLOPT_HTTPGET,
        'http_header'               => CURLOPT_HTTPHEADER,
        'http_proxy_tunnel'         => CURLOPT_HTTPPROXYTUNNEL,
        'http_request'              => CURLOPT_HTTPREQUEST,
        'http_version'              => CURLOPT_HTTP_VERSION,
        'in_file'                   => CURLOPT_INFILE,
        'in_file_size'              => CURLOPT_INFILESIZE,
        'ip_resolve'                => CURLOPT_IPRESOLVE,
        'interface'                 => CURLOPT_INTERFACE,
        'key_passwd'                => CURLOPT_KEYPASSWD,
        'krb4_level'                => CURLOPT_KRB4LEVEL,
        'low_speed_limit'           => CURLOPT_LOW_SPEED_LIMIT,
        'low_speed_time'            => CURLOPT_LOW_SPEED_TIME,
        'max_connects'              => CURLOPT_MAXCONNECTS,
        'max_recv_speed_large'      => CURLOPT_MAX_RECV_SPEED_LARGE,
        'max_redirs'                => CURLOPT_MAXREDIRS,
        'max_send_speed_large'      => CURLOPT_MAX_SEND_SPEED_LARGE,
        'mute'                      => CURLOPT_MUTE,
        'netrc'                     => CURLOPT_NETRC,
        'no_body'                   => CURLOPT_NOBODY,
        'no_progress'               => CURLOPT_NOPROGRESS,
        'no_signal'                 => CURLOPT_NOSIGNAL,
        'nothing'                   => CURLOPT_NOTHING,
        'passwd_data'               => CURLOPT_PASSWDDATA,
        'passwd_function'           => CURLOPT_PASSWDFUNCTION,
        'pasv_host'                 => CURLOPT_PASV_HOST,
        'port'                      => CURLOPT_PORT,
        'post'                      => CURLOPT_POST,
        'post_fields'               => CURLOPT_POSTFIELDS,
        'post_quote'                => CURLOPT_POSTQUOTE,
        'progress_function'         => CURLOPT_PROGRESSFUNCTION,
        'protocols'                 => CURLOPT_PROTOCOLS,
        'proxy'                     => CURLOPT_PROXY,
        'proxy_auth'                => CURLOPT_PROXYAUTH,
        'proxy_port'                => CURLOPT_PROXYPORT,
        'proxy_type'                => CURLOPT_PROXYTYPE,
        'proxy_user_pwd'            => CURLOPT_PROXYUSERPWD,
        'put'                       => CURLOPT_PUT,
        'quote'                     => CURLOPT_QUOTE,
        'random_file'               => CURLOPT_RANDOM_FILE,
        'range'                     => CURLOPT_RANGE,
        'read_function'             => CURLOPT_READFUNCTION,
        'redir_protocols'           => CURLOPT_REDIR_PROTOCOLS,
        'referer'                   => CURLOPT_REFERER,
        'return_transfer'           => CURLOPT_RETURNTRANSFER,
        'resume_from'               => CURLOPT_RESUME_FROM,
        'safe_upload'               => CURLOPT_SAFE_UPLOAD,
        'source_host'               => CURLOPT_SOURCE_HOST,
        'source_path'               => CURLOPT_SOURCE_PATH,
        'source_port'               => CURLOPT_SOURCE_PORT,
        'source_post_quote'         => CURLOPT_SOURCE_POSTQUOTE,
        'source_pre_quote'          => CURLOPT_SOURCE_PREQUOTE,
        'source_quote'              => CURLOPT_SOURCE_QUOTE,
        'source_url'                => CURLOPT_SOURCE_URL,
        'source_user_pwd'           => CURLOPT_SOURCE_USERPWD,
        'ssl_cert'                  => CURLOPT_SSLCERT,
        'ssl_cert_passwd'           => CURLOPT_SSLCERTPASSWD,
        'ssl_cert_type'             => CURLOPT_SSLCERTTYPE,
        'ssl_cipher_list'           => CURLOPT_SSL_CIPHER_LIST,
        'ssl_engine'                => CURLOPT_SSLENGINE,
        'ssl_engine_default'        => CURLOPT_SSLENGINE_DEFAULT,
        'ssl_key'                   => CURLOPT_SSLKEY,
        'ssl_key_passwd'            => CURLOPT_SSLKEYPASSWD,
        'ssl_key_type'              => CURLOPT_SSLKEYTYPE,
        'ssl_verify_host'           => CURLOPT_SSL_VERIFYHOST,
        'ssl_verify_peer'           => CURLOPT_SSL_VERIFYPEER,
        'ssl_version'               => CURLOPT_SSLVERSION,
        'stderror'                  => CURLOPT_STDERR,
        'tcp_no_delay'              => CURLOPT_TCP_NODELAY,
        'time_condition'            => CURLOPT_TIMECONDITION,
        'time_value'                => CURLOPT_TIMEVALUE,
        'timeout'                   => CURLOPT_TIMEOUT,
        'timeout_ms'                => CURLOPT_TIMEOUT_MS,
        'transfer_text'             => CURLOPT_TRANSFERTEXT,
        'unrestricted_auth'         => CURLOPT_UNRESTRICTED_AUTH,
        'upload'                    => CURLOPT_UPLOAD,
        'url'                       => CURLOPT_URL,
        'user_agent'                => CURLOPT_USERAGENT,
        'user_pwd'                  => CURLOPT_USERPWD,
        'verbose'                   => CURLOPT_VERBOSE,
        'write_function'            => CURLOPT_WRITEFUNCTION,
        'write_header'              => CURLOPT_WRITEHEADER,
    );

    /**
     * CurlHttpRequest constructor
     *
     * @param resource           $handler
     * @param string|null        $uri
     * @param CurlProxyInterface $curlProxy
     */
    public function __construct($handler, $uri = null, CurlProxyInterface $curlProxy = null)
    {
        $this->handler = $handler;
        $this->uri = $uri;
        $this->curlProxy = $curlProxy ?: new CurlProxy();
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
     * @throws HttpException
     */
    public function setOpt($option, $value)
    {
        if (is_numeric($option)) {
            $key = $option;
        } elseif (isset(self::$opt[$option])) {
            $key = self::$opt[$option];
        } else {
            throw new HttpException('HttpClient: Invalid cURL option ' . $option);
        }

        return $this->curlProxy->setopt($this->handler, $key, $value);
    }

    /**
     * Send the request
     *
     * @return HttpResponseInterface
     * @throws HttpException on a request error
     */
    public function send()
    {
        $response = $this->curlProxy->exec($this->handler);
        if (false === $response) {
            $msg = 'HttpClient: Can\'t load ' . $this->uri;
            if ($this->curlProxy->errno($this->handler)) {
                $msg .= ' - ' . $this->curlProxy->error($this->handler);
            }
            $this->curlProxy->close($this->handler);
            throw new HttpException($msg);
        }

        $info = $this->curlProxy->getinfo($this->handler);
        if (false === $info) {
            $msg = 'HttpClient: Error getting info from cURL';
            if ($this->curlProxy->errno($this->handler)) {
                $msg .= ' - ' . $this->curlProxy->error($this->handler);
            }
            $this->curlProxy->close($this->handler);
            throw new HttpException($msg);
        }

        $this->curlProxy->close($this->handler);

        return new CurlHttpResponse($response, $info);
    }
}
