<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

/**
 * Configuration repository
 *
 * Loads configuration from one or more base paths, each containing
 * a `config/` directory with PHP config files. Later paths override
 * earlier ones, allowing app-level overrides of core defaults.
 *
 * Usage:
 *   $config = new Repository(PATH_APP);
 *   $config = new Repository([PATH_CORE, PATH_APP]);
 *   $config->get('app.debug');
 */
class Repository extends Registry
{
    /**
     * Create a new configuration repository.
     *
     * @param   string|array  $paths  Base path(s) containing a config/ directory
     * @return  void
     */
    public function __construct($paths)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $merged = [];

        foreach ($paths as $path) {
            $loader = new FileLoader($path, $path);
            $data = $loader->load();
            $merged = array_replace_recursive($merged, $data);
        }

        parent::__construct($merged);
    }

    /**
     * Get a registry value.
     *
     * Supports dot-notation (e.g. 'app.debug') for grouped config.
     * For bare keys without a dot, searches across all config groups
     * to maintain backward compatibility with legacy config access.
     *
     * @param   string  $path     Registry path (e.g. app.debug)
     * @param   mixed   $default  Optional default value
     * @return  mixed   Value of entry or null
     */
    public function get($path, $default = null)
    {
        if (empty($path)) {
            return $default;
        }

        // Dot-notation: delegate directly to parent
        if (strpos($path, $this->separator)) {
            return parent::get($path, $default);
        }

        // Bare key: search across all config groups
        $nodes = get_object_vars($this->data);
        $found = false;

        foreach ($nodes as $n => $node) {
            if (is_array($node) && isset($node[$path])) {
                $value = $node[$path];
                $found = true;
                continue;
            }

            if (!isset($node->$path)) {
                continue;
            }

            $value = $node->$path;
            $found = true;
        }

        if (!$found || $value === null || $value === '') {
            return parent::get($path, $default);
        }

        return $value;
    }

    /**
     * Set a registry value.
     *
     * Supports dot-notation (e.g. 'app.debug') for grouped config.
     * For bare keys without a dot, finds the config group that contains
     * the key and updates it there. If the bare key matches a top-level
     * group name or is otherwise not found inside any group, delegates
     * to parent. Throws if the bare key is not found anywhere.
     *
     * @param   string  $path       Registry path (e.g. app.debug)
     * @param   mixed   $value      Value to set
     * @param   string  $separator  Optional path separator
     * @return  mixed   Previous value
     * @throws  \InvalidArgumentException  If bare key not found in any group
     */
    public function set($path, $value, $separator = null)
    {
        if (empty($separator)) {
            $separator = $this->separator;
        }

        // Dot-notation: delegate directly to parent
        if (strpos($path, $separator) !== false) {
            return parent::set($path, $value, $separator);
        }

        // If the bare key is a top-level group name, set it directly
        if (isset($this->data->$path)) {
            return parent::set($path, $value, $separator);
        }

        // Bare key: find the group that contains it
        $nodes = get_object_vars($this->data);
        $targetGroup = null;

        foreach ($nodes as $group => $node) {
            if (is_object($node) && isset($node->$path)) {
                $targetGroup = $group;
            } elseif (is_array($node) && isset($node[$path])) {
                $targetGroup = $group;
            }
        }

        if ($targetGroup === null) {
            throw new \InvalidArgumentException(
                "Config key '$path' not found in any group. Use dot-notation (e.g. 'group.$path')."
            );
        }

        return parent::set($targetGroup . $separator . $path, $value, $separator);
    }
}
