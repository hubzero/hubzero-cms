<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Sampledata;

/**
 * A named set of sample content
 *
 * A pack is a directory of steps and a directory of fixtures. What it builds
 * is the sum of its steps, run in the order their file names sort, so the
 * order is visible in the listing rather than written down somewhere else.
 **/
class Pack
{
    /**
     * The pack's name
     *
     * @var  string
     */
    protected $name;

    /**
     * Where the packs live
     *
     * @var  string
     */
    protected $root;

    /**
     * Where the fixtures live
     *
     * @var  string
     */
    protected $fixtures;

    /**
     * Open a pack
     *
     * @param   string  $name      The pack to open
     * @param   string  $root      Where the packs live
     * @param   string  $fixtures  Where the fixtures live
     * @return  void
     */
    public function __construct($name, $root = null, $fixtures = null)
    {
        $this->name = strtolower(preg_replace('/[^A-Za-z0-9_-]/', '', (string) $name));
        $this->root = $root ?: self::rootFor($this->name);
        $this->fixtures = $fixtures;
    }

    /**
     * Every place a pack might live
     *
     * A pack need not be part of this software. A hub keeps its own in its
     * app directory, and a pack maintained elsewhere is named in the
     * HUBZERO_SAMPLEDATA environment variable, one directory per entry,
     * separated the way PATH is.
     *
     * @return  array
     **/
    public static function roots()
    {
        $roots = [__DIR__ . DIRECTORY_SEPARATOR . 'Packs'];

        if (defined('PATH_APP')) {
            $roots[] = PATH_APP . DIRECTORY_SEPARATOR . 'sampledata';
        }

        $named = getenv('HUBZERO_SAMPLEDATA');

        if ($named) {
            foreach (explode(PATH_SEPARATOR, $named) as $directory) {
                $directory = trim($directory);

                if ($directory !== '') {
                    $roots[] = rtrim($directory, DIRECTORY_SEPARATOR);
                }
            }
        }

        return $roots;
    }

    /**
     * Where a pack of this name is, if it is anywhere
     *
     * @param   string  $name  The pack to look for
     * @return  string  The directory holding it, or the first place looked
     **/
    protected static function rootFor($name)
    {
        $roots = self::roots();

        foreach ($roots as $root) {
            if (is_dir($root . DIRECTORY_SEPARATOR . ucfirst($name))) {
                return $root;
            }
        }

        return $roots[0];
    }

    /**
     * The pack's name
     *
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Whether there is a pack by this name
     *
     * @return  bool
     */
    public function exists()
    {
        return $this->name !== '' && is_dir($this->directory());
    }

    /**
     * Where this pack's steps live
     *
     * @return  string
     */
    public function directory()
    {
        return $this->root . DIRECTORY_SEPARATOR . ucfirst($this->name);
    }

    /**
     * Where this pack's fixtures live
     *
     * @return  string
     */
    public function fixtures()
    {
        if ($this->fixtures) {
            return $this->fixtures;
        }

        // A pack keeps its fixtures with it; the ones that ship here kept
        // theirs with the rest of the install files, so both are understood.
        $beside = $this->directory() . DIRECTORY_SEPARATOR . 'fixtures';

        if (is_dir($beside)) {
            return $beside;
        }

        $core = defined('PATH_CORE') ? PATH_CORE : dirname(dirname(dirname(__DIR__)));

        return $core . '/bootstrap/Install/sampledata/' . $this->name;
    }

    /**
     * The pack's steps, in the order they run
     *
     * @param   callable|null  $reporter  Passed to each step
     * @return  array  Step objects
     */
    public function steps(?callable $reporter = null)
    {
        if (!$this->exists()) {
            return [];
        }

        $files = glob($this->directory() . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files);

        $namespace = __NAMESPACE__ . '\\Packs\\' . ucfirst($this->name) . '\\';
        $steps     = [];

        foreach ($files as $file) {
            $class = $namespace . basename($file, '.php');

            if (!class_exists($class)) {
                require_once $file;
            }

            if (!class_exists($class) || !is_subclass_of($class, Step::class)) {
                continue;
            }

            $steps[] = new $class($this->fixtures(), $reporter);
        }

        return $steps;
    }

    /**
     * Every pack there is
     *
     * @param   string  $root  Where to look, or everywhere if not given
     * @return  array   Pack objects, by name
     */
    public static function all($root = null)
    {
        $roots = $root ? [$root] : self::roots();
        $packs = [];

        foreach ($roots as $one) {
            foreach (glob($one . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $directory) {
                $pack = new self(basename($directory), $one);

                if (!isset($packs[$pack->name()])) {
                    $packs[$pack->name()] = $pack;
                }
            }
        }

        ksort($packs);

        return $packs;
    }
}
