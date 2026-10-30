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
     * A path of "-" reads the answers from standard input, so that they can
     * be handed to muse from a file it could not open itself: one that
     * belongs to someone else, or that is outside its container.
     *
     * @param   string  $path   Path to a JSON answer file, or "-"
     * @param   string  $error  Set to the reason when this returns false
     * @return  bool
     **/
    public static function load($path, &$error = null)
    {
        if ($path === '-') {
            $path = 'standard input';
            $json = stream_get_contents(STDIN);
        } elseif (!is_file($path) || !is_readable($path)) {
            $error = 'Answer file not found: ' . $path;
            return false;
        } else {
            $json = file_get_contents($path);
        }

        $answers = json_decode((string) $json, true);

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
     * The environment variables that can stand in for an answer file
     *
     * @var  array
     */
    private const ENVIRONMENT = [
        'site' => [
            'sitename'  => 'HUB_SITENAME',
            'mailfrom'  => 'HUB_MAILFROM',
            'live_site' => 'HUB_LIVE_SITE',
            'offset'    => 'HUB_TIMEZONE',
        ],
        'database' => [
            'host'     => 'HUB_DB_HOST',
            'port'     => 'HUB_DB_PORT',
            'database' => 'HUB_DB_NAME',
            'username' => 'HUB_DB_USER',
            'password' => 'HUB_DB_PASSWORD',
            'prefix'   => 'HUB_DB_PREFIX',
        ],
        'admin' => [
            'name'     => 'HUB_ADMIN_NAME',
            'username' => 'HUB_ADMIN_USERNAME',
            'email'    => 'HUB_ADMIN_EMAIL',
            'password' => 'HUB_ADMIN_PASSWORD',
        ],
    ];

    /**
     * Read the answers from environment variables
     *
     * A container is configured through its environment rather than a file.
     * Each variable answers the question its answer-file key does, and a
     * variable left unset leaves that answer to its default.
     *
     * @return  void
     **/
    public static function fromEnvironment()
    {
        $answers = [];

        foreach (self::ENVIRONMENT as $section => $keys) {
            foreach ($keys as $key => $variable) {
                $value = getenv($variable);

                if ($value !== false && $value !== '') {
                    $answers[$section][$key] = $value;
                }
            }
        }

        $sample = getenv('HUB_SAMPLE_DATA');

        if ($sample !== false) {
            $answers['sample'] = $sample;
        }

        self::set($answers);
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
     * Whether the install should load sample data
     *
     * Written as either a switch or the name of a data set, so that naming
     * one reads as a request for it.
     *
     * @return  bool
     **/
    public static function wantsSampleData()
    {
        $sample = self::option('sample', false);

        if (is_string($sample)) {
            return !in_array(strtolower($sample), ['', 'no', 'none', 'false', 'off'], true);
        }

        return (bool) $sample;
    }
}
