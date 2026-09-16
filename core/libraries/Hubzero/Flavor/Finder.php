<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Flavor;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Finds the flavors there are, and resolves what each extends
 *
 * Flavors live in JSON or YAML files, any number to a file, each file an
 * object whose keys are flavor names. The files are read from a list of
 * directories in order of precedence: a directory the caller points at (or
 * the HUBZERO_FLAVORS environment variable names), then the hub's own
 * app/flavors, then the CMS's core/flavors. The first definition of a name
 * wins, so a hub can redefine a shipped flavor by writing one of the same
 * name; and a flavor may extend one defined anywhere in the list, so a hub's
 * flavor can start from a shipped one.
 **/
class Finder
{
    /**
     * The directories, most authoritative first
     *
     * @var  array
     */
    protected $dirs;

    /**
     * Every flavor, resolved, by name - once all() has run
     *
     * @var  array|null
     */
    protected $flavors;

    /**
     * Constructor
     *
     * @param   array  $dirs  Directories to read, most authoritative first
     */
    public function __construct(array $dirs)
    {
        $this->dirs = array_values(array_filter($dirs, 'is_dir'));
    }

    /**
     * The directories a hub's flavors come from
     *
     * @param   string|null  $extra  A directory to put ahead of the rest
     * @return  array
     */
    public static function dirs($extra = null)
    {
        $dirs = array();

        if ($extra === null || $extra === '') {
            $extra = getenv('HUBZERO_FLAVORS') ?: null;
        }

        if ($extra) {
            $dirs[] = rtrim($extra, '/\\');
        }

        if (defined('PATH_APP')) {
            $dirs[] = PATH_APP . DIRECTORY_SEPARATOR . 'flavors';
        }

        if (defined('PATH_CORE')) {
            $dirs[] = PATH_CORE . DIRECTORY_SEPARATOR . 'flavors';
        }

        return $dirs;
    }

    /**
     * A finder over the usual directories
     *
     * @param   string|null  $extra  A directory to put ahead of the rest
     * @return  Finder
     */
    public static function usual($extra = null)
    {
        return new self(self::dirs($extra));
    }

    /**
     * The directories being read
     *
     * @return  array
     */
    public function directories()
    {
        return $this->dirs;
    }

    /**
     * Every flavor, resolved, by name
     *
     * @return  array
     * @throws  RuntimeException          When a file cannot be read as flavors
     * @throws  InvalidArgumentException  When a flavor is malformed or extends in a circle
     */
    public function all()
    {
        if ($this->flavors !== null) {
            return $this->flavors;
        }

        $defined = array();

        foreach ($this->dirs as $dir) {
            $files = array();

            foreach (array('json', 'yml', 'yaml') as $extension) {
                $files = array_merge($files, glob($dir . DIRECTORY_SEPARATOR . '*.' . $extension) ?: array());
            }

            // Within a directory, files are read in name order
            sort($files);

            foreach ($files as $file) {
                foreach ($this->read($file) as $name => $data) {
                    // The first definition of a name is the one that counts
                    if (!isset($defined[$name])) {
                        $defined[$name] = new Flavor($name, $data, $file);
                    }
                }
            }
        }

        $this->flavors = array();

        foreach (array_keys($defined) as $name) {
            $this->flavors[$name] = $this->resolve($name, $defined, array());
        }

        ksort($this->flavors);

        return $this->flavors;
    }

    /**
     * One flavor, resolved, or null if there is no such flavor
     *
     * @param   string  $name
     * @return  Flavor|null
     */
    public function find($name)
    {
        $all = $this->all();

        return isset($all[$name]) ? $all[$name] : null;
    }

    /**
     * The flavors in one file, JSON or YAML by its extension
     *
     * @param   string  $file
     * @return  array   name => definition
     * @throws  RuntimeException
     */
    protected function read($file)
    {
        $why = '';

        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'json') {
            $data = json_decode((string) file_get_contents($file), true);
            $why  = json_last_error() ? json_last_error_msg() : '';
        } else {
            try {
                $data = Yaml::parseFile($file);
            } catch (ParseException $e) {
                $data = null;
                $why  = $e->getMessage();
            }
        }

        if (!is_array($data)) {
            throw new RuntimeException("{$file} does not read as flavors" . ($why ? ': ' . $why : ''));
        }

        foreach ($data as $name => $definition) {
            if (!is_string($name) || !is_array($definition)) {
                throw new RuntimeException(
                    "{$file} should be an object of flavor names, each holding that flavor's levers"
                );
            }
        }

        return $data;
    }

    /**
     * A flavor cascaded onto everything it extends
     *
     * @param   string  $name
     * @param   array   $defined  Every flavor as written
     * @param   array   $trail    The names already being resolved, to catch a circle
     * @return  Flavor
     * @throws  InvalidArgumentException
     */
    protected function resolve($name, array $defined, array $trail)
    {
        if (in_array($name, $trail)) {
            throw new InvalidArgumentException(
                'Flavors extend each other in a circle: ' . implode(' -> ', $trail) . ' -> ' . $name
            );
        }

        if (isset($this->flavors[$name])) {
            return $this->flavors[$name];
        }

        $flavor = $defined[$name];
        $parent = $flavor->parent();

        if ($parent === null) {
            return $flavor;
        }

        if (!isset($defined[$parent])) {
            throw new InvalidArgumentException(
                "Flavor '{$name}' extends '{$parent}', which is not defined anywhere in: "
                . implode(', ', $this->dirs)
            );
        }

        $trail[] = $name;

        return $flavor->onto($this->resolve($parent, $defined, $trail));
    }
}
