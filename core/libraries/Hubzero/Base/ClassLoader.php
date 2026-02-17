<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

/**
 * Class loader for HubZero extensions and fallback for framework classes
 *
 * Handles namespace-to-directory mapping for:
 * - Components: Components\Blog\* -> components/com_blog/*
 * - Modules: Modules\Menu\* -> modules/mod_menu/*
 * - Plugins: Plugins\System\Debug\* -> plugins/system/debug/*
 *
 * Also provides fallback for Hubzero\* and Bootstrap\* if Composer misses them.
 *
 * Supports PATH_APP overrides: if an extension directory exists in PATH_APP,
 * all class lookups for that extension use only that directory.
 */
class ClassLoader
{
    /**
     * Base directories to search (PATH_APP first for override priority)
     *
     * @var array
     */
    protected static array $directories = [];

    /**
     * Namespace prefix mappings: prefix => config
     *
     * @var array
     */
    protected static array $prefixes = [];

    /**
     * Indicates if a ClassLoader has been registered.
     *
     * @var bool
     */
    protected static bool $registered = false;

    /**
     * Reverse map from namespace segments to directory names where they differ.
     *
     * Used by plugin type resolution where directory names contain characters
     * invalid in PHP identifiers (e.g. hyphens) or map to reserved words.
     *
     * @var array<string, string>
     */
    protected static array $pluginDirMap = [
        'editorsxtd'     => 'editors-xtd',
        'defaulthandler' => 'default',
    ];

    /**
     * Add a namespace prefix mapping
     *
     * @param string $prefix  Namespace prefix (with trailing backslash)
     * @param string $baseDir Base directory (PSR-4 style - prefix not repeated in path)
     * @param string $type    Optional: 'component', 'module', 'plugin' for special handling
     */
    public static function addPrefix(string $prefix, string $baseDir, string $type = 'psr4'): void
    {
        self::$prefixes[$prefix] = [
            'baseDir' => $baseDir,
            'type' => $type,
        ];
    }

    /**
     * Register autoloader with all HubZero namespace prefixes
     */
    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        // Framework classes (fallback if Composer misses)
        self::addPrefix('Hubzero\\', 'libraries/Hubzero');
        self::addPrefix('Bootstrap\\', 'bootstrap');

        // Extensions (primary handler - needs PATH_APP override support)
        self::addPrefix('Components\\', 'components', 'component');
        self::addPrefix('Modules\\', 'modules', 'module');
        self::addPrefix('Plugins\\', 'plugins', 'plugin');
        self::addPrefix('Templates\\', 'templates', 'template');

        // Migrations namespace
        self::addPrefix('Migrations\\', 'migrations', 'psr4');

        // Register autoloader
        static::$registered = spl_autoload_register([static::class, 'load']);
    }

    /**
     * Load a class using registered prefixes or fallback
     *
     * @param string $class Fully qualified class name
     * @return bool
     */
    public static function load(string $class): bool
    {
        // Try registered prefixes first
        foreach (self::$prefixes as $prefix => $config) {
            if (strpos($class, $prefix) === 0) {
                $result = self::loadByPrefix($class, $prefix, $config);
                if ($result) {
                    return true;
                }
            }
        }

        // Fallback to existing directory scanning behavior
        return self::loadFromDirectories($class);
    }

    /**
     * Load a class using prefix mapping
     *
     * @param string $class  Fully qualified class name
     * @param string $prefix Matched namespace prefix
     * @param array  $config Prefix configuration
     * @return bool
     */
    protected static function loadByPrefix(string $class, string $prefix, array $config): bool
    {
        $relative = substr($class, strlen($prefix));
        $parts = explode('\\', $relative);
        $base = $config['baseDir'];

        // Build paths based on type
        switch ($config['type']) {
            case 'component':
                // Components\Blog\Models\Entry -> components/com_blog/Models/Entry.php
                $name = array_shift($parts);
                $file = implode('/', $parts) . '.php';
                if (empty($file) || $file === '.php') {
                    $file = "{$name}.php";
                }
                $lname = strtolower($name);
                $extensionDirs = [
                    "{$base}/com_{$lname}",
                    "{$base}/{$lname}",
                ];
                $relativePaths = [
                    "{$base}/com_{$lname}/{$file}",
                    "{$base}/{$lname}/{$file}",
                ];
                break;

            case 'module':
                // Modules\Menu\Helper -> modules/mod_menu/Helper.php
                $name = array_shift($parts);
                $file = implode('/', $parts) . '.php';
                if (empty($file) || $file === '.php') {
                    $file = "{$name}.php";
                }
                $lname = strtolower($name);
                $extensionDirs = [
                    "{$base}/mod_{$lname}",
                    "{$base}/{$lname}",
                ];
                $relativePaths = [
                    "{$base}/mod_{$lname}/{$file}",
                    "{$base}/{$lname}/{$file}",
                ];
                break;

            case 'plugin':
                // Plugins\System\Debug\Helper -> plugins/system/debug/Helper.php
                $type = strtolower(array_shift($parts));
                $name = array_shift($parts);
                $lname = strtolower($name);

                // Reverse-map namespace segments to directory names where they differ
                $type = self::$pluginDirMap[$type] ?? $type;
                $lname = self::$pluginDirMap[$lname] ?? $lname;

                $file = implode('/', $parts) . '.php';
                if (empty($file) || $file === '.php') {
                    $file = "{$name}.php";
                }
                $extensionDirs = ["{$base}/{$type}/{$lname}"];
                $relativePaths = ["{$base}/{$type}/{$lname}/{$file}"];
                break;

            case 'template':
                // Templates\Kameleon\Helper -> templates/tpl_kameleon/Helper.php
                //                           -> templates/kameleon/Helper.php (fallback)
                $name = array_shift($parts);
                $lname = strtolower($name);
                $file = implode('/', $parts) . '.php';
                if (empty($file) || $file === '.php') {
                    $file = "{$name}.php";
                }
                $extensionDirs = [
                    "{$base}/tpl_{$lname}",
                    "{$base}/{$lname}",
                ];
                $relativePaths = [
                    "{$base}/tpl_{$lname}/{$file}",
                    "{$base}/{$lname}/{$file}",
                ];
                break;

            default:
                // PSR-4 style: namespace maps directly to path
                $path = "{$base}/" . str_replace('\\', '/', $relative) . '.php';
                $extensionDirs = [$base];
                $relativePaths = [$path];
                break;
        }

        // Build all file path variants (CamelCase + lowercase)
        $fileVariants = [];
        foreach ($relativePaths as $relativePath) {
            $fileVariants[] = $relativePath;
            $fileVariants[] = strtolower($relativePath);
        }

        // Determine which base directory owns this extension
        // Framework classes (psr4): check all directories, core last
        // Extensions: If directory exists in PATH_APP, use ONLY that
        $isFramework = ($config['type'] === 'psr4');

        // For framework, we check all directories (Composer should handle most)
        // For extensions, find the owning directory
        $owningBase = null;

        foreach (self::$directories as $baseDir) {
            foreach ($extensionDirs as $extDir) {
                if (is_dir($baseDir . '/' . $extDir)) {
                    $owningBase = $baseDir;
                    break 2;
                }
            }
        }

        // If no extension directory found, class doesn't exist
        if ($owningBase === null) {
            // For framework classes, still try all directories
            if ($isFramework) {
                foreach (self::$directories as $baseDir) {
                    foreach ($fileVariants as $path) {
                        $fullPath = $baseDir . '/' . $path;
                        if (file_exists($fullPath)) {
                            require $fullPath;
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        // Search ONLY in the owning base directory
        foreach ($fileVariants as $path) {
            $fullPath = $owningBase . '/' . $path;
            if (file_exists($fullPath)) {
                require $fullPath;
                return true;
            }
        }

        return false;
    }

    /**
     * Fallback: load from registered directories using original behavior
     *
     * @param string $class Fully qualified class name
     * @return bool
     */
    protected static function loadFromDirectories(string $class): bool
    {
        $class = static::normalizeClass($class);

        foreach (static::$directories as $directory) {
            if (file_exists($path = $directory . DIRECTORY_SEPARATOR . $class)) {
                require_once $path;
                return true;
            }

            if (file_exists($path = $directory . DIRECTORY_SEPARATOR . strtolower($class))) {
                require_once $path;
                return true;
            }
        }

        return false;
    }

    /**
     * Get the normal file name for a class.
     *
     * @param string $class
     * @return string
     */
    public static function normalizeClass(string $class): string
    {
        if ($class[0] == '\\') {
            $class = substr($class, 1);
        }

        return str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $class) . '.php';
    }

    /**
     * Add directories to the class loader.
     *
     * @param string|array $directories
     */
    public static function addDirectories($directories): void
    {
        static::$directories = array_unique(array_merge(static::$directories, (array) $directories));
    }

    /**
     * Remove directories from the class loader.
     *
     * @param string|array|null $directories
     */
    public static function removeDirectories($directories = null): void
    {
        if (is_null($directories)) {
            static::$directories = [];
        } else {
            static::$directories = array_diff(static::$directories, (array) $directories);
        }
    }

    /**
     * Gets all the directories registered with the loader.
     *
     * @return array
     */
    public static function getDirectories(): array
    {
        return static::$directories;
    }

    /**
     * Gets all registered prefixes.
     *
     * @return array
     */
    public static function getPrefixes(): array
    {
        return static::$prefixes;
    }
}
