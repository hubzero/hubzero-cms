<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Language helper facade
 *
 * @method static string txt(string $string)
 * @method static string txts(string $string, int $n)
 * @method static bool   load(string $extension = 'hubzero', string $basePath = '', string $lang = null, bool $reload = false, bool $default = true)
 * @method static bool   exists(string $lang, string $basePath = '')
 * @method static bool   hasKey(string $string)
 * @method static string translate(string $string, bool $jsSafe = false, bool $interpretBackSlashes = true)
 * @method static array  script(string $string = null, bool $jsSafe = false, bool $interpretBackSlashes = true)
 * @method static mixed  get(string $property, mixed $default = null)
 * @method static string getTag()
 * @method static string getName()
 * @method static string getDefault()
 * @method static string getLanguage()
 * @method static bool   isRTL()
 * @method static array  getPaths(string $extension = null)
 * @method static array  getOrphans()
 * @method static array  getKnownLanguages(string $basePath = '')
 * @method static string detect()
 *
 * @codeCoverageIgnore
 */
class Lang extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'language';
    }
}
