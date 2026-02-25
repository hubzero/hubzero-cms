<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * IP address class
 */
class Dns
{
    /**
     * Get FQDN from the config
     *
     * @return string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    private static function _getConfig()
    {
        return \Hubzero\Facades\Config::get('app.fqdn', '');
    }

    /**
     * Get array of domains of FQDN
     *
     * @return array
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    private static function _getConfigArray()
    {
        return explode('.', self::_getConfig());
    }

    /**
     * Get hostname from FQDN
     *
     * @return string
     */
    public static function hostname()
    {
        $arr = self::_getConfigArray();
        if (empty($arr)) {
            return '';
        }
        return self::_getConfigArray()[0];
    }

    /**
     * Get top level domain
     *
     * @return string
     */
    public static function tld()
    {
        $arr = self::_getConfigArray();
        if (empty($arr)) {
            return '';
        }
        $tld = end($arr);
        return $tld;
    }

    /**
     * Get FQDN
     *
     * @return string
     */
    public static function fqdn()
    {
        return self::_getConfig();
    }

    /**
     * Get parent domain
     *
     * @return string
     */
    public static function domain()
    {
        $arr = self::_getConfigArray();
        array_shift($arr);
        if (is_null($arr)) {
            $arr = [];
        }

        $domain = implode('.', $arr);
        return $domain;
    }

    /**
     * Get subdomains *excluding* the hostname and TLD
     *
     * @return array
     */
    public static function subdomains()
    {
        $domains = self::_getConfigArray();
        array_pop($domains);
        // Config contained only a single domain (".com")
        if (is_null($domains)) {
            $domains = [];
            return $domains;
        }

        // Config contained no subdomains ("example.com")
        array_shift($domains);
        if (is_null($domains)) {
            $domains = [];
            return $domains;
        }

        return $domains;
    }
}
