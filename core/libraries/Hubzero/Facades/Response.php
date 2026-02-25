<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Response facade
 *
 * @method static object  compress(bool $value)
 * @method static object  header(string $key, mixed $values, bool $replace = true)
 * @method static object  send(bool $flush = false)
 * @method static object  setContent(string $content)
 * @method static string  getContent()
 * @method static object  setStatusCode(int $code, string $text = null)
 * @method static int     getStatusCode()
 * @method static object  setCharset(string $charset)
 * @method static string  getCharset()
 *
 * @codeCoverageIgnore
 */
class Response extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'response';
    }
}
