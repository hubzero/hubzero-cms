<?php

/**
 * @package framework
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Helpers;

use Hubzero\Facades\Route;
use Hubzero\Facades\Component;

/**
 * Instantiate and return a form field for autocompleting some value
 */
class Converter
{
    public static function convert($html)
    {
        $params = Component::params('com_redirect');
        $whitelist = isset($params["delay_whitelist"]) ? explode(",", $params["delay_whitelist"]) : array();
        $blacklist = isset($params["delay_blacklist"]) ? explode(",", $params["delay_blacklist"]) : array();

        return preg_replace_callback(
            '/<a\s+[^>]*href=[\'"]([^\'"]+)[\'"][^>]*>/i',
            function ($matches) use ($whitelist, $blacklist) {
                $originalUrl = $matches[1];

                // Skip anchor-only or javascript links
                if (strpos($originalUrl, '#') === 0 || stripos($originalUrl, 'javascript:') === 0) {
                    return $matches[0];
                }
                $host = parse_url($originalUrl);
                if (!$host || !isset($host['host'])) {
                    $newTag =  $matches[0];
                } elseif (strpos($host['host'], $_SERVER['HTTP_HOST']) !== false) {
                    $newTag =  $matches[0];
                } elseif (in_array($host['host'], $whitelist) !== false) {
                    $newTag =  $matches[0];
                } elseif (in_array($host['host'], $blacklist) !== false) {
                    $newTag = preg_replace(
                        '/href=[\'"][^\'"]+[\'"]/i',
                        'href="/" ',
                        $matches[0]
                    );
                } else {
                    $newHref = Converter::encode($originalUrl);
                    // Replace original href with encoded version, add rel="noreferrer"
                    $newTag = preg_replace(
                        '/href=[\'"][^\'"]+[\'"]/i',
                        'href="' . $newHref . '" rel="noreferrer nofollow noopener"',
                        $matches[0]
                    );
                }
                    return $newTag;
            },
            $html
        );
    }

    public static function encode($originalUrl)
    {
        $encoded = urlencode(base64_encode($originalUrl));
        $newHref = Route::url('index.php?option=com_redirect&id=' . $encoded);
        return $newHref;
    }
}
