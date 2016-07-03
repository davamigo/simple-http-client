<?php

namespace Davamigo\HttpClient\CurlHttpClient;

/**
 * Proxy to use the cURL PHP functions
 *
 * @package Davamigo\HttpClient\CurlHttpClient
 * @author davamigo@gmail.com
 * @codeCoverageIgnore
 */
class CurlProxy implements CurlProxyInterface
{
    /**
     * Initialize a cURL session
     *
     * @param string $url [optional]
     * @return resource a cURL handle on success, false on errors.
     */
    public function init($url = null)
    {
        return @curl_init($url);
    }

    /**
     * Set an option for a cURL transfer
     *
     * @param resource $ch
     * @param int $option The CURLOPT_XXX option to set.
     * @param mixed $value The value to be set on option.
     * @return bool true on success or false on failure.
     */
    public function setopt($ch, $option, $value)
    {
        return @curl_setopt($ch, $option, $value);
    }

    /**
     * Perform a cURL session
     *
     * @param resource $ch
     * @return mixed true on success or false on failure.
     */
    public function exec($ch)
    {
        return @curl_exec($ch);
    }

    /**
     * Get information regarding a specific transfer
     *
     * @param resource $ch
     * @param int $opt [optional]
     * @return mixed If opt is given, returns its value as a string. Otherwise, returns an associative array.
     */
    public function getinfo($ch, $opt = null)
    {
        if (!$opt) {
            return @curl_getinfo($ch);
        } else {
            return @curl_getinfo($ch, $opt);
        }
    }

    /**
     * Return the last error number
     *
     * @param resource $ch
     * @return int the error number or 0 (zero) if no error occurred.
     */
    public function errno($ch)
    {
        return @curl_errno($ch);
    }

    /**
     * Return a string containing the last error for the current session
     *
     * @param resource $ch
     * @return string the error message or '' (the empty string) if no error occurred.
     */
    function error($ch)
    {
        return @curl_error($ch);
    }

    /**
     * Close a cURL session
     *
     * @param resource $ch
     * @return void
     */
    function close($ch)
    {
        @curl_close($ch);
    }
}
