<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility\Tests;

use Hubzero\Utility\Sanitize;

/**
 * Exposes the purifier's element whitelist without the application
 *
 * Sanitize::html() reads the request and the cache directory from the
 * application; the elements it adds are what these tests are about.
 */
class SanitizeWhitelist extends Sanitize
{
    /**
     * Purify HTML with the elements Sanitize adds
     *
     * @param   string  $html
     * @return  string
     */
    public static function purify($html)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);

        $definition = $config->getHTMLDefinition(true);
        static::addElementsToHtmlPurifierWhitelist($definition);
        static::addAttributesToHtmlPurifierWhitelist($definition);

        return trim((new \HTMLPurifier($config))->purify($html));
    }
}
