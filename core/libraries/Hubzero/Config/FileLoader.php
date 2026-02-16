<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

use Hubzero\Config\Exception\UnsupportedFormatException;
use Hubzero\Config\Exception\EmptyDirectoryException;

/**
 * File loader class
 */
class FileLoader
{
    /**
     * Root path for Legacy config path rewriting.
     *
     * @var  string
     */
    protected $rootPath;

    /**
     * App path for config files and Legacy config.
     *
     * @var  string
     */
    protected $appPath;

    /**
     * Create a new file configuration loader.
     *
     * @param   string  $rootPath  Root path (e.g., PATH_ROOT)
     * @param   string  $appPath   App path (e.g., PATH_APP)
     * @return  void
     */
    public function __construct($rootPath, $appPath)
    {
        $this->rootPath = $rootPath;
        $this->appPath = $appPath;
    }

    /**
     * Get the config path (derived from appPath)
     *
     * @return  string
     */
    public function getConfigPath()
    {
        return $this->appPath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Get the root path
     *
     * @return  string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Get the app path
     *
     * @return  string
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * Load the given configuration group.
     *
     * @param   string  $client
     * @return  array
     */
    public function load($client = null)
    {
        $data = array();
        $configPath = $this->getConfigPath();

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        try {
            $paths = $this->getPaths($configPath);

            if (empty($paths)) {
                throw new EmptyDirectoryException("Configuration directory: [" . $configPath . "] is empty");
            }

            foreach ($paths as $path) {
                // Get file information
                $info = pathinfo($path);
                $group = isset($info['filename']) ? strtolower($info['filename']) : '';
                $extension = isset($info['extension']) ? strtolower($info['extension']) : '';

                if (!$extension || $extension == 'html') {
                    continue;
                }

                try {
                    $data[$group] = $this->getParser($extension)->parse($path);
                } catch (UnsupportedFormatException $e) {
                    continue;
                }
            }

            if (empty($data)) {
                throw new EmptyDirectoryException("Configuration directory: [" . $configPath . "] is empty");
            }

            // If a client is specified...
            if ($client) {
                $paths = $this->getPaths($configPath . DIRECTORY_SEPARATOR . $client);

                foreach ($paths as $path) {
                    // Get file information
                    $info = pathinfo($path);
                    $group = isset($info['filename']) ? strtolower($info['filename']) : '';
                    $extension = isset($info['extension']) ? strtolower($info['extension']) : '';

                    if (!$extension || $extension == 'html') {
                        continue;
                    }

                    if (!isset($data[$group])) {
                        $data[$group] = array();
                    }

                    try {
                        $data[$group] = array_replace_recursive(
                            $data[$group],
                            $this->getParser($extension)->parse($path)
                        );
                    } catch (UnsupportedFormatException $e) {
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            $loader = new Legacy($this->rootPath, $this->rootPath, $this->appPath);

            $data = $loader->toArray();

            if (!empty($data)) {
                $loader->split();
            }
        }

        return $data;
    }

    /**
     * Gets a parser for a given file extension
     *
     * @param   string  $extension
     * @return  object
     * @throws  UnsupportedFormatException  If `$extension` is an unsupported file format
     */
    protected function getParser($extension)
    {
        $parser = null;

        $extension = strtolower($extension);

        foreach (Processor::all() as $fileParser) {
            if (in_array($extension, $fileParser->getSupportedExtensions())) {
                $parser = $fileParser;
                break;
            }
        }

        // If none exist, then throw an exception
        if ($parser === null) {
            throw new UnsupportedFormatException(sprintf('Unsupported configuration format "%s"', $extension));
        }

        return $parser;
    }

    /**
     * Checks `$path` to see if it is either an array, a directory, or a file
     *
     * @param   mixed  $path
     * @return  array
     */
    protected function getPaths($path)
    {
        $paths = array();

        // If `$path` is an array
        if (is_array($path)) {
            foreach ($path as $unverifiedPath) {
                $paths = array_merge($paths, $this->getPaths($unverifiedPath));
            }

            return $paths;
        }

        // If `$path` is a directory
        if (is_dir($path)) {
            $paths = glob($path . '/*.*');

            return $paths;
        }

        // If `$path` is a file
        if (file_exists($path)) {
            $paths[] = $path;
        }

        return $paths;
    }
}
