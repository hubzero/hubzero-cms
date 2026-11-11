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
 * @method static string txt(string $string, mixed ...$args)
 * @method static string txts(string $string, int $n, mixed ...$args)
 * @method static string alt(string $string, string $alt, bool $jsSafe = false, bool $interpretBackSlashes = true)
 * @method static string transliterate(string $string)
 * @method static array  available(string $key = 'default')
 * @method static bool   isMultilang()
 * @method static string setLanguage(string $lang)
 * @method static array  getErrorFiles()
 * @method static array  getLanguages(string $key = null)
 * @method static string getLanguagePath(string $basePath = PATH_APP, string $language = null, string $client = null)
 * @method static bool   exists(string $lang, string $basePath = '', string $client = null)
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
 * @method static array  getKnownLanguages(string $basePath = '', string $client = null)
 * @method static string detect()
 * @method static \Hubzero\Language\Translator getInstance(string $lang, bool $debug = false)
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

    /**
     * Loads a single language file and appends its strings to the existing ones
     *
     * Declared here rather than as a tag because its signature does not fit
     * on one line, and a tag cannot be wrapped.
     *
     * @param   string       $extension  The extension whose language file to load
     * @param   string       $basePath   The base path to use
     * @param   string|null  $lang       The language to load, null for the current language
     * @param   boolean      $reload     Force the language to be reloaded
     * @param   boolean      $default    Load the default language if the current one does not exist
     * @return  boolean      True if the file was loaded
     */
    public static function load(
        $extension = 'hubzero',
        $basePath = PATH_APP,
        $lang = null,
        $reload = false,
        $default = true,
    ) {
        return static::getRoot()->load(...func_get_args());
    }
}
