<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Language;

use Hubzero\Language\Transliterate\Latin;
use Hubzero\Base\Obj;
use Hubzero\Facades\Lang;

/**
 * Allows for quoting in language .ini files.
 */
if (!defined('_QQ_')) {
    define('_QQ_', '"');
}

/**
 * Languages/translation handler class
 *
 * Inspired by Joomla's JLanguage class
 */
class Translator extends Obj
{
    /**
     * List of languages
     *
     * @public array
     */
    protected static $languages = array();

    /**
     * javascript strings
     *
     * @public array
     */
    protected static $jsStrings = array();

    /**
     * Application client
     *
     * @public string
     */
    protected $client = 'site';

    /**
     * Debug language, If true, highlights if string isn't found.
     *
     * @public boolean
     */
    protected $debug = false;

    /**
     * The default language, used when a language file in the requested language does not exist.
     *
     * @public string
     */
    protected $default = 'en-GB';

    /**
     * An array of orphaned text.
     *
     * @public array
     */
    protected $orphans = array();

    /**
     * Array holding the language metadata.
     *
     * @public array
     */
    protected $metadata = null;

    /**
     * Array holding the language locale or boolean null if none.
     *
     * @public array|boolean
     */
    protected $locale = null;

    /**
     * The language to load.
     *
     * @public string
     */
    protected $lang = null;

    /**
     * A nested array of language files that have been loaded
     *
     * @public array
     */
    protected $paths = array();

    /**
     * List of language files that are in error state
     *
     * @public array
     */
    protected $errorfiles = array();

    /**
     * Translations
     *
     * @public array
     */
    protected $strings = null;

    /**
     * An array of used text, used during debugging.
     *
     * @public array
     */
    protected $used = array();

    /**
     * An array used to store overrides.
     *
     * @public array
     */
    protected $override = array();

    /**
     * Whether overrides have been merged into strings.
     *
     * @public boolean
     */
    protected $overridesMerged = false;

    /**
     * Name of the transliterator function for this language.
     *
     * @public string
     */
    protected $transliterator = null;

    /**
     * A list of callback functions for this language.
     *
     * @public array
     */
    protected $callbacks = array(
        'pluralSuffixes'       => null,
        'ignoredSearchWords'   => null,
        'lowerLimitSearchWord' => null,
        'upperLimitSearchWord' => null,
        'searchDisplayedCharactersNumber' => null,
    );

    /**
     * Constructor activating the default information of the language.
     *
     * @param   string   $lang   The language
     * @param   boolean  $debug  Indicates if language debugging is enabled.
     * @return  void
     */
    public function __construct($lang = null, $debug = false, $client = 'site')
    {
        $this->strings = array();

        if ($lang == null) {
            $lang = $this->default;
        }

        $this->client = $client;
        $this->setLanguage($lang);
        $this->setDebug($debug);

        // App language path (new flat structure)
        $appLangPath = PATH_APP . "/language/" . strtolower($client) . "/$lang";

        // System overrides — loaded first, lower priority
        $this->loadOverride("$appLangPath/$lang.override.ini");

        // Template overrides (early, from config) — highest priority
        // Uses the standard tpl_ file as the global override for all strings
        $templateName = \Hubzero\Facades\App::get('config')->get(strtolower($client) . '_template');
        if ($templateName) {
            $tplFile = PATH_APP
                . "/templates/$templateName/language/$lang/$lang.tpl_$templateName.ini";
            $this->loadOverride($tplFile);
        }

        // Look for a language specific localise class.
        // Supports both PSR-compliant namespaced classes (Bootstrap\X\Language\EnGBLocalise)
        // and legacy global classes (en_GBLocalise) for backward compatibility with PATH_APP overrides.
        $classLegacy = str_replace('-', '_', $lang . 'Localise');
        $classCamel = implode('', array_map('ucfirst', explode('-', $lang))) . 'Localise';
        $ns = 'Bootstrap\\' . ucfirst($client) . '\\Language\\';
        $class = $classLegacy;
        $paths = array();

        // App localise path
        $paths[0] = "$appLangPath/$lang.localise.php";
        // Core paths (ucfirst is PSR-4 convention)
        $paths[1] = PATH_CORE . "/bootstrap/" . strtolower($client) . "/language/$lang/$lang.localise.php";
        $paths[2] = PATH_CORE . "/bootstrap/" . ucfirst($client) . "/language/$lang/$lang.localise.php";

        ksort($paths);
        $path = reset($paths);

        while (!class_exists($class) && !class_exists($ns . $classCamel) && $path) {
            if (file_exists($path)) {
                require_once $path;
            }
            $path = next($paths);
        }

        // Prefer the PSR-compliant namespaced class if available
        if (class_exists($ns . $classCamel)) {
            $class = $ns . $classCamel;
        }

        if (class_exists($class)) {
            // Class exists. Try to find
            // -a transliterate method,
            // -a getPluralSuffixes method,
            // -a getIgnoredSearchWords method
            // -a getLowerLimitSearchWord method
            // -a getUpperLimitSearchWord method
            // -a getSearchDisplayCharactersNumber method
            if (method_exists($class, 'transliterate')) {
                $this->transliterator = array($class, 'transliterate');
            }

            foreach ($this->callbacks as $callback => $value) {
                if (!$callback) {
                    continue;
                }

                $method = 'get' . ucfirst($callback);

                if (method_exists($class, $method)) {
                    $this->callbacks[$callback] = array($class, $method);
                }
            }
        }

        $this->load('', PATH_APP) || $this->load('', PATH_CORE);
    }

    /**
     * Returns a language object.
     *
     * @param   string   $lang   The language to use.
     * @param   boolean  $debug  The debug mode.
     * @return  object   The Language object.
     */
    public static function getInstance($lang, $debug = false)
    {
        if (!isset(self::$languages[$lang . $debug])) {
            $language = new self($lang, $debug);

            self::$languages[$lang . $debug] = $language;

            // Check if Language was instantiated with a null $lang param;
            // if so, retrieve the language code from the object and store
            // the instance with the language code as well
            if (is_null($lang)) {
                self::$languages[$language->getLanguage() . $debug] = $language;
            }
        }

        return self::$languages[$lang . $debug];
    }

    /**
     * Translate function, mimics the php gettext (alias _) function.
     *
     * The function checks if $jsSafe is true, then if $interpretBackslashes is true.
     *
     * @param   string   $string                The string to translate
     * @param   boolean  $jsSafe                Make the result javascript safe
     * @param   boolean  $interpretBackSlashes  Interpret \t and \n
     * @return  string   The translation of the string
     */
    public function translate($string, $jsSafe = false, $interpretBackSlashes = true)
    {
        // Detect empty string
        if ($string == '') {
            return '';
        }

        // Deferred override merge — apply overrides once after all regular strings are loaded
        if (!$this->overridesMerged && !empty($this->override)) {
            $this->strings = array_merge($this->strings, $this->override);
            $this->overridesMerged = true;
        }

        $key = strtoupper($string);

        if (isset($this->strings[$key])) {
            $string = $this->debug ? '**' . $this->strings[$key] . '**' : $this->strings[$key];

            // Store debug information
            if ($this->debug) {
                $caller = $this->getCallerInfo();

                if (!array_key_exists($key, $this->used)) {
                    $this->used[$key] = array();
                }

                $this->used[$key][] = $caller;
            }
        } else {
            if ($this->debug) {
                $caller = $this->getCallerInfo();
                $caller['string'] = $string;

                if (!array_key_exists($key, $this->orphans)) {
                    $this->orphans[$key] = array();
                }

                $this->orphans[$key][] = $caller;

                $string = '??' . $string . '??';
            }
        }

        if ($jsSafe) {
            // Javascript filter
            $string = addslashes($string);
        } elseif ($interpretBackSlashes) {
            // Interpret \n and \t characters
            $string = str_replace(array('\\\\', '\t', '\n'), array("\\", "\t", "\n"), $string);
        }

        return $string;
    }

    /**
     * Transliterate function
     *
     * This method processes a string and replaces all accented UTF-8 characters by unaccented
     * ASCII-7 "equivalents".
     *
     * @param   string  $string  The string to transliterate.
     * @return  string  The transliteration of the string.
     */
    public function transliterate($string)
    {
        if ($this->transliterator !== null) {
            return call_user_func($this->transliterator, $string);
        }

        $string = Latin::toAscii($string);
        $string = strtolower($string);

        return $string;
    }

    /**
     * Getter for transliteration function
     *
     * @return  string  Function name or the actual function for PHP 5.3.
     */
    public function getTransliterator()
    {
        return $this->transliterator;
    }

    /**
     * Set the transliteration function.
     *
     * @param   mixed  $function  Function name (string) or the actual function for PHP 5.3 (function).
     * @return  mixed
     */
    public function setTransliterator($function)
    {
        $this->transliterator = $function;

        return $this;
    }

    /**
     * Returns an array of suffixes for plural rules.
     *
     * @param   integer  $count  The count number the rule is for.
     * @return  array    The array of suffixes.
     */
    public function getPluralSuffixes($count)
    {
        if ($this->callbacks['pluralSuffixes'] !== null) {
            return call_user_func($this->callbacks['pluralSuffixes'], $count);
        } else {
            return array((string) $count);
        }
    }

    /**
     * Getter for pluralSuffixesCallback function.
     *
     * @return  mixed  Function name (string) or the actual function for PHP 5.3 (function).
     */
    public function getPluralSuffixesCallback()
    {
        return $this->callbacks['pluralSuffixes'];
    }

    /**
     * Set the pluralSuffixes function.
     *
     * @param   mixed  $function  Function name (string) or actual function for PHP 5.3 (function)
     * @return  mixed  Function name or the actual function for PHP 5.3.
     */
    public function setPluralSuffixesCallback($function)
    {
        $this->callbacks['pluralSuffixes'] = $function;

        return $this;
    }

    /**
     * Returns an array of ignored search words
     *
     * @return  array  The array of ignored search words.
     */
    public function getIgnoredSearchWords()
    {
        if ($this->callbacks['ignoredSearchWords'] !== null) {
            return call_user_func($this->callbacks['ignoredSearchWords']);
        }

        return array();
    }

    /**
     * Getter for ignoredSearchWordsCallback function.
     *
     * @return  mixed  Function name (string) or the actual function for PHP 5.3 (function).
     */
    public function getIgnoredSearchWordsCallback()
    {
        return $this->callbacks['ignoredSearchWords'];
    }

    /**
     * Setter for the ignoredSearchWordsCallback function
     *
     * @param   mixed  $function  Function name (string) or actual function for PHP 5.3 (function)
     * @return  mixed  Function name (string) or the actual function for PHP 5.3 (function)
     */
    public function setIgnoredSearchWordsCallback($function)
    {
        $this->callbacks['ignoredSearchWords'] = $function;

        return $this;
    }

    /**
     * Returns a lower limit integer for length of search words
     *
     * @return  integer  The lower limit integer for length of search words (3 if no value was set
     * for a specific language).
     */
    public function getLowerLimitSearchWord()
    {
        if ($this->callbacks['lowerLimitSearchWord'] !== null) {
            return call_user_func($this->callbacks['lowerLimitSearchWord']);
        }

        return 3;
    }

    /**
     * Getter for lowerLimitSearchWordCallback function
     *
     * @return  mixed  Function name (string) or the actual function for PHP 5.3 (function).
     */
    public function getLowerLimitSearchWordCallback()
    {
        return $this->callbacks['lowerLimitSearchWord'];
    }

    /**
     * Setter for the lowerLimitSearchWordCallback function.
     *
     * @param   mixed  $function  Function name (string) or actual function for PHP 5.3 (function)
     * @return  string|function   Function name or the actual function for PHP 5.3.
     */
    public function setLowerLimitSearchWordCallback($function)
    {
        $this->callbacks['lowerLimitSearchWord'] = $function;

        return $this;
    }

    /**
     * Returns an upper limit integer for length of search words
     *
     * @return  integer  The upper limit integer for length of search words (20 if no value was set
     * for a specific language).
     */
    public function getUpperLimitSearchWord()
    {
        if ($this->callbacks['upperLimitSearchWord'] !== null) {
            return call_user_func($this->callbacks['upperLimitSearchWord']);
        }

        return 20;
    }

    /**
     * Getter for upperLimitSearchWordCallback function
     *
     * @return  string|function  Function name or the actual function for PHP 5.3.
     */
    public function getUpperLimitSearchWordCallback()
    {
        return $this->callbacks['upperLimitSearchWord'];
    }

    /**
     * Setter for the upperLimitSearchWordCallback function
     *
     * @param   string  $function  The name of the callback function.
     * @return  mixed   Function name (string) or the actual function for PHP 5.3 (function).
     */
    public function setUpperLimitSearchWordCallback($function)
    {
        $this->callbacks['upperLimitSearchWord'] = $function;

        return $this;
    }

    /**
     * Returns the number of characters displayed in search results.
     *
     * @return  integer  The number of characters displayed (200 if no value was set for a specific language).
     */
    public function getSearchDisplayedCharactersNumber()
    {
        if ($this->callbacks['searchDisplayedCharactersNumber'] !== null) {
            return call_user_func($this->callbacks['searchDisplayedCharactersNumber']);
        }

        return 200;
    }

    /**
     * Getter for searchDisplayedCharactersNumberCallback function
     *
     * @return  mixed  Function name or the actual function for PHP 5.3.
     */
    public function getSearchDisplayedCharactersNumberCallback()
    {
        return $this->callbacks['searchDisplayedCharactersNumber'];
    }

    /**
     * Setter for the searchDisplayedCharactersNumberCallback function.
     *
     * @param   string  $function  The name of the callback.
     * @return  mixed   Function name (string) or the actual function for PHP 5.3 (function).
     */
    public function setSearchDisplayedCharactersNumberCallback($function)
    {
        $this->callbacks['searchDisplayedCharactersNumber'] = $function;

        return $this;
    }

    /**
     * Checks if a language exists.
     *
     * This is a simple, quick check for the directory that should contain language files for the given user.
     *
     * @param   string   $lang      Language to check.
     * @param   string   $basePath  Optional path to check.
     * @return  boolean  True if the language exists.
     */
    public static function exists($lang, $basePath = PATH_APP)
    {
        static $paths = array();

        // Return false if no language was specified
        if (!$lang) {
            return false;
        }

        $path = $basePath . DS . 'language' . DS . $lang;

        // Return previous check results if it exists
        if (isset($paths[$path])) {
            return $paths[$path];
        }

        // Check if the language exists
        $paths[$path] = is_dir($path);

        return $paths[$path];
    }

    /**
     * Loads a single language file and appends the results to the existing strings
     *
     * @param   string   $extension  The extension for which a language file should be loaded.
     * @param   string   $basePath   The basepath to use.
     * @param   string   $lang       The language to load, default null for the current language.
     * @param   boolean  $reload     Flag that will force a language to be reloaded if set to true.
     * @param   boolean  $default    Flag that force the default language to be loaded if the current does not exist.
     * @return  boolean  True if the file has successfully loaded.
     */
    public function load($extension = 'hubzero', $basePath = PATH_APP, $lang = null, $reload = false, $default = true)
    {
        // Load the default language first if we're not debugging and a non-default language is requested to be loaded
        // with $default set to true
        if (!\Hubzero\Facades\App::get('config')->get('debug_lang') && ($lang != $this->default) && $default) {
            $this->load($extension, $basePath, $this->default, false, true);
        }

        if (!$lang) {
            $lang = $this->lang;
        }

        if ($basePath == PATH_APP) {
            $path = PATH_APP . DS . 'language' . DS . strtolower($this->client) . DS . $lang;
        } elseif ($basePath == PATH_CORE) {
            $basePath .= DS . 'bootstrap' . DS . $this->client;
            $path = self::getLanguagePath($basePath, $lang);
        } else {
            $path = self::getLanguagePath($basePath, $lang);
        }

        $internal = $extension == 'hubzero' || $extension == '';
        $filename = $internal ? $lang : $lang . '.' . $extension;
        $filename = "$path/$filename.ini";

        $result = false;

        if (isset($this->paths[$extension][$filename]) && !$reload) {
            // This file has already been tested for loading.
            $result = $this->paths[$extension][$filename];
        } else {
            // Load the language file
            $result = $this->loadLanguage($filename, $extension);

            // Check whether there was a problem with loading the file
            if ($result === false && $default) {
                // No strings, so either file doesn't exist or the file is invalid
                $oldFilename = $filename;

                // Check the standard file name
                $path = self::getLanguagePath($basePath, $this->default);
                $filename = $internal ? $this->default : $this->default . '.' . $extension;
                $filename = "$path/$filename.ini";

                // If the one we tried is different than the new name, try again
                if ($oldFilename != $filename) {
                    $result = $this->loadLanguage($filename, $extension, false);
                }
            }
        }

        return $result;
    }

    /**
     * Load an override file and merge its contents into the override array.
     *
     * @param   string   $filename  The override file path.
     * @return  boolean  True if new overrides were loaded.
     */
    public function loadOverride($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        $contents = $this->parse($filename);

        if (is_array($contents) && count($contents)) {
            $this->override = array_merge($this->override, $contents);
            $this->overridesMerged = false;
            return true;
        }

        return false;
    }

    /**
     * Loads a language file.
     *
     * This method will not note the successful loading of a file - use load() instead.
     *
     * @param   string   $filename   The name of the file.
     * @param   string   $extension  The name of the extension.
     * @return  boolean  True if new strings have been added to the language
     */
    protected function loadLanguage($filename, $extension = 'unknown')
    {
        $result  = false;
        $strings = false;

        if (file_exists($filename)) {
            $strings = $this->parse($filename);
        }

        if (is_array($strings) && count($strings)) {
            $this->strings = array_merge($this->strings, $strings);
            $this->overridesMerged = false;
            $result = true;
        }

        // Record the result of loading the extension's file.
        if (!isset($this->paths[$extension])) {
            $this->paths[$extension] = array();
        }

        $this->paths[$extension][$filename] = $result;

        return $result;
    }

    /**
     * Debugs a language file
     *
     * @param   string  $filename  Absolute path to the file to debug
     * @return  integer  A count of the number of parsing errors
     * @throws  \InvalidArgumentException
     */
    public function debugFile(string $filename)
    {
        // Make sure our file actually exists
        if (!is_file($filename)) {
            throw new \InvalidArgumentException(sprintf('Unable to locate file "%s" for debugging', $filename));
        }

        // Initialise variables for manually parsing the file for common errors.
        $reservedWord = array('YES', 'NO', 'NULL', 'FALSE', 'ON', 'OFF', 'NONE', 'TRUE');
        $errors       = array();

        // Open the file as a stream.
        $file = new \SplFileObject($filename);

        foreach ($file as $lineNumber => $line) {
            // Avoid BOM error as BOM is OK when using parse_ini.
            if ($lineNumber == 0) {
                $line = str_replace("\xEF\xBB\xBF", '', $line);
            }

            $line = trim($line);

            // Ignore comment lines.
            if (!strlen($line) || $line['0'] == ';') {
                continue;
            }

            // Ignore grouping tag lines, like: [group]
            if (preg_match('#^\[[^\]]*\](\s*;.*)?$#', $line)) {
                continue;
            }

            // Remove any escaped double quotes \" from the equation
            $line = str_replace('\"', '', $line);

            $realNumber = $lineNumber + 1;

            // Check for odd number of double quotes.
            if (substr_count($line, '"') % 2 != 0) {
                $errors[] = $realNumber;
                continue;
            }

            // Check that the line passes the necessary format.
            if (!preg_match('#^[A-Z][A-Z0-9_:\*\-\.]*\s*=\s*".*"(\s*;.*)?$#', $line)) {
                $errors[] = $realNumber;
                continue;
            }

            // Check that the key is not in the reserved constants list.
            $key = strtoupper(trim(substr($line, 0, strpos($line, '='))));

            if (in_array($key, $reservedWord)) {
                $errors[] = $realNumber;
            }
        }

        // Check if we encountered any errors.
        if (count($errors)) {
            $this->errorfiles[$filename] = $errors;
        }

        return count($errors);
    }

    /**
     * Parse strings from a language file.
     *
     * @param   string   $fileName  The language ini file path.
     * @param   boolean  $debug     If set to true debug language ini file.
     * @return  array  The strings parsed.
     * @throws  \RuntimeException On debug
     */
    public static function parseIniFile($fileName, $debug = false)
    {
        // Check if file exists.
        if (!is_file($fileName)) {
            return array();
        }

        $disabledFunctions      = explode(',', ini_get('disable_functions'));
        $isParseIniFileDisabled = in_array('parse_ini_file', array_map('trim', $disabledFunctions));

        // Capture hidden PHP errors from the parsing.
        set_error_handler(static function ($errno, $err) {
            throw new \Exception($err);
        }, E_WARNING);

        try {
            if (!function_exists('parse_ini_file') || $isParseIniFileDisabled) {
                $contents = file_get_contents($fileName);
                $strings  = parse_ini_string($contents, false, INI_SCANNER_RAW);
            } else {
                $strings = parse_ini_file($fileName, false, INI_SCANNER_RAW);
            }
        } catch (\Exception $e) {
            if ($debug) {
                throw new \RuntimeException($e->getMessage());
            }

            return array();
        } finally {
            restore_error_handler();
        }

        // Ini files are processed in the "RAW" mode of parse_ini_string, leaving escaped quotes untouched - lets
        // postprocessthem
        $strings = str_replace('\"', '"', $strings);

        return is_array($strings) ? $strings : array();
    }

    /**
     * Parses a language file.
     *
     * @param   string  $fileName  The name of the file.
     * @return  array  The array of parsed strings.
     */
    protected function parse($fileName)
    {
        try {
            $strings = $this->parseIniFile($fileName, $this->debug);
        } catch (\RuntimeException $e) {
            $strings = array();

            // Debug the ini file if needed.
            if ($this->debug && is_file($fileName)) {
                if (!$this->debugFile($fileName)) {
                    // We didn't find any errors but there's a parser warning.
                    $this->errorfiles[$fileName] = 'PHP parser errors :' . $e->getMessage();
                }
            }
        }

        return $strings;
    }

    /**
     * Get a metadata language property.
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $default   The default value.
     * @return  mixed   The value of the property.
     */
    public function get($property, $default = null)
    {
        if (isset($this->metadata[$property])) {
            return $this->metadata[$property];
        }

        return $default;
    }

    /**
     * Determine who called the translator.
     *
     * @return  array  Caller information.
     */
    protected function getCallerInfo()
    {
        // Try to determine the source if none was provided
        if (!function_exists('debug_backtrace')) {
            return null;
        }

        $backtrace = debug_backtrace();
        $info = array();

        // Search through the backtrace to our caller
        $continue = true;
        while ($continue && next($backtrace)) {
            $step  = current($backtrace);
            $class = @ $step['class'];

            // We're looking for something outside of language.php
            if ($class != '\\Hubzero\\Language\\Translator' && $class != '\\Lang') {
                $info['function'] = @ $step['function'];
                $info['class'] = $class;
                $info['step'] = prev($backtrace);

                // Determine the file and name of the file
                $info['file'] = @ $step['file'];
                $info['line'] = @ $step['line'];

                $continue = false;
            }
        }

        return $info;
    }

    /**
     * Getter for Name.
     *
     * @return  string  Official name element of the language.
     */
    public function getName()
    {
        return $this->metadata['name'];
    }

    /**
     * Get a list of language files that have been loaded.
     *
     * @param   string  $extension  An optional extension name.
     * @return  array
     */
    public function getPaths($extension = null)
    {
        if (isset($extension)) {
            if (isset($this->paths[$extension])) {
                return $this->paths[$extension];
            }

            return null;
        }

        return $this->paths;
    }

    /**
     * Get a list of language files that are in error state.
     *
     * @return  array
     */
    public function getErrorFiles()
    {
        return $this->errorfiles;
    }

    /**
     * Getter for the language tag (as defined in RFC 3066)
     *
     * @return  string  The language tag.
     */
    public function getTag()
    {
        return $this->metadata['tag'];
    }

    /**
     * Get the RTL property.
     *
     * @return  boolean  True is it an RTL language.
     */
    public function isRTL()
    {
        return $this->metadata['rtl'];
    }

    /**
     * Set the Debug property.
     *
     * @param   boolean  $debug  The debug setting.
     * @return  boolean  Previous value.
     */
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    /**
     * Get the Debug property.
     *
     * @return  boolean  True is in debug mode.
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Get the default language code.
     *
     * @return  string  Language code.
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the default language code.
     *
     * @param   string  $lang  The language code.
     * @return  string  Previous value.
     */
    public function setDefault($lang)
    {
        $this->default = $lang;

        return $this;
    }

    /**
     * Get the list of orphaned strings if being tracked.
     *
     * @return  array  Orphaned text.
     */
    public function getOrphans()
    {
        return $this->orphans;
    }

    /**
     * Get the list of used strings.
     *
     * Used strings are those strings requested and found either as a string or a constant.
     *
     * @return  array  Used strings.
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Determines is a key exists.
     *
     * @param   string   $string  The key to check.
     * @return  boolean  True, if the key exists.
     */
    public function hasKey($string)
    {
        if (!$string) {
            return false;
        }

        // Ensure overrides are merged before checking
        if (!$this->overridesMerged && !empty($this->override)) {
            $this->strings = array_merge($this->strings, $this->override);
            $this->overridesMerged = true;
        }

        $key = strtoupper($string);

        return isset($this->strings[$key]);
    }

    /**
     * Returns a associative array holding the metadata.
     *
     * @param   string  $lang  The name of the language.
     * @return  mixed   If $lang exists return key/value pair with the language metadata, otherwise return NULL.
     */
    public static function getMetadata($lang)
    {
        $path = PATH_APP . DS . 'language' . DS . strtolower(\Hubzero\Facades\App::get('client')->name) . DS . $lang;
        $file = $lang . '.xml';

        $result = null;

        if (!is_file("$path/$file")) {
            $path = self::getLanguagePath(PATH_CORE .
                DS .
                'bootstrap' .
                DS .
                ucfirst(\Hubzero\Facades\App::get('client')->name), $lang);
        }

        if (is_file("$path/$file")) {
            $result = self::parseXMLLanguageFile("$path/$file");
        }

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * Returns a list of known languages for an area
     *
     * @param   string  $basePath  The basepath to use
     * @return  array   key/value pair with the language file and real name.
     */
    public static function getKnownLanguages($basePath = PATH_APP)
    {
        $dir = self::getLanguagePath($basePath);
        $knownLanguages = self::parseLanguageFiles($dir);

        return $knownLanguages;
    }

    /**
     * Get the path to a language
     *
     * @param   string  $basePath  The basepath to use.
     * @param   string  $language  The language tag.
     * @return  string  language related path or null.
     */
    public static function getLanguagePath($basePath = PATH_APP, $language = null)
    {
        $dir = $basePath . DS . 'language';

        if (!empty($language)) {
            $dir .= DS . $language;
        }

        return $dir;
    }

    /**
     * Get the current language code.
     *
     * @return  string  The language code
     */
    public function getLanguage()
    {
        return $this->lang;
    }

    /**
     * Set the language attributes to the given language.
     *
     * Once called, the language still needs to be loaded using JLanguage::load().
     *
     * @param   string  $lang  Language code.
     * @return  string  Previous value.
     */
    public function setLanguage($lang)
    {
        $previous = $this->lang ?? null;
        $this->lang = $lang;
        $this->metadata = $this->getMetadata($this->lang);

        // If switching languages, reload everything in the new language
        if ($previous && $previous !== $lang) {
            $this->strings = array();
            $this->override = array();
            $this->overridesMerged = false;

            // Reload base strings
            $this->load('', PATH_APP) || $this->load('', PATH_CORE);

            // Reload overrides in the new language
            $client = strtolower($this->client);
            $appLangPath = PATH_APP . "/language/$client/$lang";

            $this->loadOverride("$appLangPath/$lang.override.ini");

            $templateName = \Hubzero\Facades\App::get('config')->get($client . '_template');
            if ($templateName) {
                $tplFile = PATH_APP
                    . "/templates/$templateName/language/$lang/$lang.tpl_$templateName.ini";
                $this->loadOverride($tplFile);
            }
        }

        return $this;
    }

    /**
     * Get the language locale based on current language.
     *
     * @return  array  The locale according to the language.
     */
    public function getLocale()
    {
        if (!isset($this->locale)) {
            $locale = str_replace(' ', '', isset($this->metadata['locale']) ? $this->metadata['locale'] : '');

            if ($locale) {
                $this->locale = explode(',', $locale);
            } else {
                $this->locale = false;
            }
        }

        return $this->locale;
    }

    /**
     * Get the first day of the week for this language.
     *
     * @return  integer  The first day of the week according to the language
     */
    public function getFirstDay()
    {
        return (int) (isset($this->metadata['firstDay']) ? $this->metadata['firstDay'] : 0);
    }

    /**
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     * @return  array   Array holding the found languages as filename => real name pairs.
     */
    public static function parseLanguageFiles($dir = null)
    {
        $languages = array();

        if (is_dir($dir)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

            foreach ($iterator as $file) {
                $langs    = array();
                $fileName = $file->getFilename();

                if (!$file->isFile() || !preg_match("/^([-_A-Za-z]*)\.xml$/", $fileName)) {
                    continue;
                }

                try {
                    $metadata = self::parseXMLLanguageFile($file->getRealPath());

                    if ($metadata) {
                        $lang = str_replace('.xml', '', $fileName);
                        $langs[$lang] = $metadata;
                    }

                    $languages = array_merge($languages, $langs);
                } catch (\RuntimeException $e) {
                }
            }
        }

        return $languages;
    }

    /**
     * Parse XML file for language information.
     *
     * @param   string  $path  Path to the XML files.
     * @return  array   Array holding the found metadata as a key => value pair.
     */
    public static function parseXMLLanguageFile($path)
    {
        if (!is_readable($path)) {
            throw new \RuntimeException('File not found or not readable');
        }

        // Try to load the file
        $xml = simplexml_load_file($path);

        if (!$xml) {
            return null;
        }

        // Check that it's a metadata file
        if ((string) $xml->getName() != 'metafile') {
            return null;
        }

        $metadata = array();

        foreach ($xml->metadata->children() as $child) {
            $metadata[$child->getName()] = (string) $child;
        }

        return $metadata;
    }

    /**
     * Tries to detect the language.
     *
     * @return  string  locale or null if not found
     */
    public function detect()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $systemLangs  = $this->available();
            foreach ($browserLangs as $browserLang) {
                // Slice out the part before ; on first step, the part before - on second, place into array
                $browserLang = substr($browserLang, 0, strcspn($browserLang, ';'));
                $primary_browserLang = substr($browserLang, 0, 2);
                foreach ($systemLangs as $systemLang) {
                    // Take off 3 letters iso code languages as they can't match browsers' languages and default them
                    // toen
                    $Jinstall_lang = $systemLang->lang_code;

                    if (strlen($Jinstall_lang) < 6) {
                        if (
                            strtolower($browserLang) == strtolower(substr(
                                $systemLang->lang_code,
                                0,
                                strlen($browserLang)
                            ))
                        ) {
                            return $systemLang->lang_code;
                        } elseif ($primary_browserLang == substr($systemLang->lang_code, 0, 2)) {
                            $primaryDetectedLang = $systemLang->lang_code;
                        }
                    }
                }

                if (isset($primaryDetectedLang)) {
                    return $primaryDetectedLang;
                }
            }
        }

        return null;
    }

    /**
     * Get available languages
     *
     * @param   string  $key  Array key
     * @return  array   An array of published languages
     */
    public function available($key = 'default')
    {
        static $languages;

        if (empty($languages)) {
            // Installation uses available languages
            if (\Hubzero\Facades\App::get('client')->id == 2) {
                $languages[$key] = array();
                $knownLangs = self::getKnownLanguages(PATH_APP . DS . 'language' . DS . strtolower($this->client));
                foreach ($knownLangs as $metadata) {
                    // Take off 3 letters iso code languages as they can't match browsers' languages and default them
                    // toen
                    $languages[$key][] = new \Hubzero\Base\Obj(array('lang_code' => $metadata['tag']));
                }
            } else {
                $cache = \Hubzero\Facades\App::get('cache.store');
                if (!$languages = $cache->get('com_languages.languages')) {
                    $db = \Hubzero\Facades\App::get('db');
                    $query = $db->getQuery()
                        ->select('*')
                        ->from('#__languages')
                        ->whereEquals('published', 1)
                        ->order('ordering', 'asc');
                    $db->setQuery($query->toString());

                    $languages['default']   = $db->loadObjectList();
                    $languages['sef']       = array();
                    $languages['lang_code'] = array();

                    if (isset($languages['default'][0])) {
                        foreach ($languages['default'] as $lang) {
                            $languages['sef'][$lang->sef] = $lang;
                            $languages['lang_code'][$lang->lang_code] = $lang;
                        }
                    }

                    $cache->put('com_languages.languages', $languages, \Hubzero\Facades\App::get('config')->get('cachetime', 15));
                }
            }
        }

        return $languages[$key];
    }

    /**
     * Builds a list of the system languages which can be used in a select option
     *
     * @param   string   $actualLanguage  Client key for the area
     * @param   string   $basePath        Base path to use
     * @param   boolean  $caching         True if caching is used
     * @param   array    $installed       An array of arrays (text, value, selected)
     * @param   integer  $client          Client ID
     * @return  array    List of system languages
     */
    public static function getList(
        $actualLanguage,
        $basePath = PATH_APP,
        $caching = false,
        $installed = false,
        $client = null
    ) {
        $list = array();

        $langs = self::getKnownLanguages($basePath);

        if ($installed) {
            $db = \Hubzero\Facades\App::get('db');
            $query = $db->getQuery()
                ->select('element')
                ->from('#__extensions')
                ->whereEquals('type', 'language')
                ->whereEquals('state', 0)
                ->whereEquals('enabled', 1)
                ->whereEquals('client_id', (is_null($client) ? \Hubzero\Facades\App::get('client')->id : (int)$client));
            $db->setQuery($query->toString());
            $installed_languages = $db->loadObjectList('element');
        }

        foreach ($langs as $lang => $metadata) {
            if (!$installed || array_key_exists($lang, $installed_languages)) {
                $option = array();
                $option['text']  = $metadata['name'];
                $option['value'] = $lang;
                if ($lang == $actualLanguage) {
                    $option['selected'] = 'selected="selected"';
                }

                $list[] = $option;
            }
        }

        return $list;
    }

    /**
     * Translates a string into the current language.
     *
     * @param   string  $string  The string to translate.
     * @return  string  The translated string or the key is $script is true
     */
    public function txt($string)
    {
        $args  = func_get_args();
        $count = count($args);

        if ($count > 1) {
            if ($count == 2 && is_bool($args[1])) {
                return $this->translate($string, $args[1]);
            }

            if ($count == 3 && is_bool($args[1]) && is_bool($args[2])) {
                return $this->translate($string, $args[1], $args[2]);
            }

            if (is_array($args[$count - 1])) {
                $args[0] = $this->translate(
                    $string,
                    array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
                    array_key_exists(
                        'interpretBackSlashes',
                        $args[$count - 1]
                    ) ? $args[$count - 1]['interpretBackSlashes'] : true
                );
            } else {
                $args[0] = $this->translate($string);
            }
            $args[0] = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', $args[0]);

            return call_user_func_array('sprintf', $args);
        }

        return $this->translate($string);
    }

    /**
     * Translates a string into the current language.
     *
     * @param   string   $string  The format string.
     * @param   integer  $n       The number of items
     * @return  string   The translated string or the key is $script is true
     */
    public function txts($string, $n)
    {
        $args  = func_get_args();
        $count = count($args);

        if ($count > 1) {
            // Try the key from the language plural potential suffixes
            $found = false;
            $suffixes = $this->getPluralSuffixes((int) $n);
            array_unshift($suffixes, (int) $n);
            foreach ($suffixes as $suffix) {
                $key = $string . '_' . $suffix;
                if ($this->hasKey($key)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // Not found so revert to the original.
                $key = $string;
            }
            if (is_array($args[$count - 1])) {
                $args[0] = $this->translate(
                    $key,
                    array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
                    array_key_exists(
                        'interpretBackSlashes',
                        $args[$count - 1]
                    ) ? $args[$count - 1]['interpretBackSlashes'] : true
                );
            } else {
                $args[0] = $this->translate($key);
            }
            return call_user_func_array('sprintf', $args);
        } elseif ($count > 0) {
            // Default to the normal sprintf handling.
            $args[0] = $this->translate($string);
            return call_user_func_array('sprintf', $args);
        }

        return '';
    }

    /**
     * Translates a string into the current language.
     *
     * Examples:
     * <?php echo Lang::alt("JALL","language");?> it will generate a 'All' string in English but a "Toutes" string in
     * French
     * <?php echo Lang::alt("JALL","module");?> it will generate a 'All' string in English but a "Tous" string in French
     *
     * @param   string   $string                The string to translate.
     * @param   string   $alt                   The alternate option for global string
     * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
     * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     * @return  string   The translated string or the key if $script is true
     */
    public function alt($string, $alt, $jsSafe = false, $interpretBackSlashes = true)
    {
        if ($this->hasKey($string . '_' . $alt)) {
            $string = $string . '_' . $alt;
        }

        return $this->txt($string, $jsSafe, $interpretBackSlashes);
    }

    /**
     * Method to determine if the language filter plugin is enabled.
     * This works for both site and administrator.
     *
     * @return  boolean  True if site is supporting multiple languages; false otherwise.
     */
    public function isMultilang()
    {
        // Flag to avoid doing multiple database queries.
        static $tested = false;

        // Status of language filter plugin.
        static $enabled = false;

        // If being called from the front-end, we can avoid the database query.
        if (\Hubzero\Facades\App::isSite()) {
            return \Hubzero\Facades\App::get('language.filter');
        }

        // If already tested, don't test again.
        if (!$tested) {
            // Determine status of language filter plug-in.
            $db = \Hubzero\Facades\App::get('db');
            $query = $db->getQuery()
                ->select('enabled')
                ->from('#__extensions')
                ->whereEquals('type', 'plugin')
                ->whereEquals('folder', 'system')
                ->whereEquals('element', 'languagefilter');
            $db->setQuery($query->toString());

            $enabled = $db->loadResult();
            $tested  = true;
        }

        return $enabled;
    }

    /**
     * Translate a string into the current language and stores it in the JavaScript language store.
     *
     * @param   string   $string                The language key.
     * @param   boolean  $jsSafe                Ensure the output is JavaScript safe.
     * @param   boolean  $interpretBackSlashes  Interpret \t and \n.
     * @return  mixed
     */
    public function script($string = null, $jsSafe = false, $interpretBackSlashes = true)
    {
        if (is_array($jsSafe)) {
            if (array_key_exists('interpretBackSlashes', $jsSafe)) {
                $interpretBackSlashes = (bool) $jsSafe['interpretBackSlashes'];
            }

            if (array_key_exists('jsSafe', $jsSafe)) {
                $jsSafe = (bool) $jsSafe['jsSafe'];
            } else {
                $jsSafe = false;
            }
        }

        // Add the string to the array if not null.
        if ($string !== null) {
            // Normalize the key and translate the string.
            self::$jsStrings[strtoupper($string)] = $this->translate($string, $jsSafe, $interpretBackSlashes);
        }

        return self::$jsStrings;
    }
}
