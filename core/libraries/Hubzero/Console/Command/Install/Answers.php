<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

/**
 * Answers given up front so an install can run without asking anything
 *
 * The installer normally collects what it needs a question at a time. An
 * answer file supplies the same values ahead of time, which is what lets a
 * script stand up a site. Every installer step asks here first, and falls
 * back to its own prompts when nothing was loaded.
 **/
class Answers
{
    /**
     * The sections an answer file may carry
     *
     * @var  array
     */
    private const SECTIONS = ['site', 'database', 'admin'];

    /**
     * The answers, as loaded
     *
     * @var  array
     */
    private static $answers = [];

    /**
     * Whether an answer file was loaded
     *
     * @var  bool
     */
    private static $unattended = false;

    /**
     * Read an answer file
     *
     * @param   string  $path   Path to a JSON answer file
     * @param   string  $error  Set to the reason when this returns false
     * @return  bool
     **/
    public static function load($path, &$error = null)
    {
        if (!is_file($path) || !is_readable($path)) {
            $error = 'Answer file not found: ' . $path;
            return false;
        }

        $answers = json_decode(file_get_contents($path), true);

        if (!is_array($answers)) {
            $error = 'Answer file is not valid JSON: ' . $path;
            return false;
        }

        foreach (self::SECTIONS as $section) {
            if (isset($answers[$section]) && !is_array($answers[$section])) {
                $error = 'The "' . $section . '" section of ' . $path . ' must be an object.';
                return false;
            }
        }

        self::$answers    = $answers;
        self::$unattended = true;

        return true;
    }

    /**
     * Supply answers directly, without a file
     *
     * @param   array  $answers  The answers
     * @return  void
     **/
    public static function set(array $answers)
    {
        self::$answers    = $answers;
        self::$unattended = true;
    }

    /**
     * Forget any answers, putting the installer back to asking
     *
     * @return  void
     **/
    public static function reset()
    {
        self::$answers    = [];
        self::$unattended = false;
    }

    /**
     * Whether the install should run without asking anything
     *
     * @return  bool
     **/
    public static function isUnattended()
    {
        return self::$unattended;
    }

    /**
     * All the answers for one step of the install
     *
     * @param   string  $section  One of site, database or admin
     * @return  array
     **/
    public static function section($section)
    {
        return isset(self::$answers[$section]) && is_array(self::$answers[$section])
            ? self::$answers[$section]
            : [];
    }

    /**
     * A single answer that stands on its own, outside any section
     *
     * @param   string  $key      The answer to read
     * @param   mixed   $default  What to return when it was not given
     * @return  mixed
     **/
    public static function option($key, $default = null)
    {
        return array_key_exists($key, self::$answers) ? self::$answers[$key] : $default;
    }

    /**
     * The set of starting content the install should load
     *
     * Written as either the name of a set or a switch, so that naming one
     * reads as a request for it and turning it off reads as a refusal.
     *
     * @param   string  $default  The set a bare yes asks for
     * @return  string|null  The set to load, or null for none
     **/
    public static function dataSet($default = 'starter')
    {
        $sample = self::option('sample', false);

        if (is_string($sample)) {
            $sample = strtolower(trim($sample));

            if (in_array($sample, ['', 'no', 'none', 'false', 'off', 'minimal'], true)) {
                return null;
            }

            return in_array($sample, ['yes', 'true', 'on'], true) ? $default : $sample;
        }

        return $sample ? $default : null;
    }
}
