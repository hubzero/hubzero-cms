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
        $this->root = $root ?: __DIR__ . DIRECTORY_SEPARATOR . 'Packs';

        $core = defined('PATH_CORE') ? PATH_CORE : dirname(dirname(dirname(__DIR__)));

        $this->fixtures = $fixtures
            ?: $core . '/bootstrap/Install/sampledata/' . $this->name;
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
        return $this->fixtures;
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
     * @param   string  $root  Where the packs live
     * @return  array   Pack objects, by name
     */
    public static function all($root = null)
    {
        $root = $root ?: __DIR__ . DIRECTORY_SEPARATOR . 'Packs';
        $packs = [];

        foreach (glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $directory) {
            $pack = new self(basename($directory), $root);
            $packs[$pack->name()] = $pack;
        }

        ksort($packs);

        return $packs;
    }
}
