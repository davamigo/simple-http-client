<?php

namespace Davamigo\HttpClient\Domain;

/**
 * Generic HTTP body interface
 *
 * @package Davamigo\HttpClient\Domain
 * @author davamigo@gmail.com
 */
interface HttpBody
{
    /**
     * Returns the body internal data (usually the real body object).
     *
     * @return object|array
     */
    public function getBodyData();

    /**
     * Convert the body to a string if possible
     *
     * @return string
     */
    public function __toString();
}
