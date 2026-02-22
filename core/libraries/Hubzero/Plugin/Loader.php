<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

use Hubzero\Events\Dispatcher;
use Hubzero\Events\DispatcherInterface;
use Hubzero\Events\LoaderInterface;
use Hubzero\Config\Registry;
use Exception;
use stdClass;
use User;
use Lang;
use App;

/**
 * Plugin loader
 *
 * Inspired, in part, by Joomla's JPluginHelper class
 */
class Loader implements LoaderInterface
{
    /**
     * A persistent cache of the loaded plugins.
     *
     * @var  array
     */
    protected static $plugins = null;

    /**
     * Get the event name.
     *
     * @return  string  The event name.
     */
    public function getName()
    {
        return 'plugins';
    }

    /**
     * Get the plugin data of a specific type if no specific plugin is specified
     * otherwise only the specific plugin data is returned.
     *
     * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string  $plugin  The plugin name.
     * @return  mixed   An array of plugin data objects, or a plugin data object.
     */
    public function loadListeners($type)
    {
        $results = array();
        $plugins = (array) $this->byType($type);

        foreach ($plugins as $p) {
            if ($result = $this->init($p)) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Get the params for a specific plugin
     *
     * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string  $plugin  The plugin name.
     * @return  object
     */
    public function params($type, $plugin)
    {
        $result = $this->byType($type, $plugin);

        if (!$result || empty($result)) {
            $result = new stdClass();
            $result->params = '';
        }

        if (is_string($result->params)) {
            $result->params = new Registry($result->params);
        }

        return $result->params;
    }

    /**
     * Get the plugin data of a specific type if no specific plugin is specified
     * otherwise only the specific plugin data is returned.
     *
     * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string  $plugin  The plugin name.
     * @return  mixed   An array of plugin data objects, or a plugin data object.
     */
    public function byType($type, $plugin = null)
    {
        $result = array();

        foreach ($this->all() as $p) {
            // Is this the right plugin?
            if ($p->type == $type) {
                if ($plugin) {
                    if ($p->name == $plugin) {
                        $result = $p;
                        break;
                    }
                } else {
                    $result[] = $p;
                }
            }
        }

        return $result;
    }

    /**
     * Checks if a plugin is enabled.
     *
     * @param   string   $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string   $plugin  The plugin name.
     * @return  boolean
     */
    public function isEnabled($type, $plugin = null)
    {
        $result = $this->byType($type, $plugin);

        return (!empty($result));
    }

    /**
     * Checks if a plugin is enabled.
     *
     * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string  $plugin  The plugin name.
     * @return  string
     */
    public function path($type, $plugin = null)
    {
        static $paths = array();

        if (!isset($paths[$type . $plugin])) {
            $paths[$type . $plugin] = '';

            $p = DS . 'plugins' . DS . $type . ($plugin ? DS . $plugin : '');

            foreach (array(PATH_APP, PATH_CORE) as $base) {
                if (is_dir($base . $p)) {
                    $paths[$type . $plugin] = $base . $p;
                    break;
                }
            }
        }

        return $paths[$type . $plugin];
    }

    /**
     * Loads all the plugin files for a particular type if no specific plugin is specified
     * otherwise only the specific plugin is loaded.
     *
     * @param   string   $type        The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string   $plugin      The plugin name.
     * @param   boolean  $autocreate  Autocreate the plugin.
     * @param   object   $dispatcher  Optionally allows the plugin to use a different dispatcher.
     * @return  boolean  True on success.
     */
    public function import($type, $plugin = null, $autocreate = true, $dispatcher = null)
    {
        static $loaded = array();

        // check for the default args, if so we can optimise cheaply
        $defaults = false;
        if (is_null($plugin) && $autocreate == true && is_null($dispatcher)) {
            $defaults = true;
        }

        if (!isset($loaded[$type]) || !$defaults) {
            $results = null;

            // Makes sure we have an event dispatcher
            if (!($dispatcher instanceof DispatcherInterface)) {
                $dispatcher = App::get('dispatcher');
            }

            // Get the specified plugin(s).
            foreach ($this->all() as $plug) {
                if ($plug->type == $type && ($plugin === null || $plug->name == $plugin)) {
                    if ($p = $this->init($plug, $autocreate)) { //, $dispatcher))
                        $dispatcher->addListener($p);
                    }
                    $results = true;
                }
            }

            // Bail out early if we're not using default args
            if (!$defaults) {
                return $results;
            }
            $loaded[$type] = $results;
        }

        return $loaded[$type];
    }

    /**
     * Loads the plugin file.
     *
     * @param   object   $plugin  The plugin data.
     * @return  boolean  True on success.
     */
    protected function init($plugin, $autocreate = true, $dispatcher = null)
    {
        $plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
        $plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

        $nsType = self::toNamespacePart($plugin->type);
        $nsName = self::toNamespacePart($plugin->name);

        // Namespaced: \Plugins\{Type}\{Name}\{Name} (preferred)
        $classNameN = 'Plugins\\' . $nsType . '\\' . $nsName . '\\' . $nsName;
        // Legacy: plg{type}{name} (BC — case-insensitive, covers PlgTypeName too)
        $classNameL = 'plg' . $plugin->type . $plugin->name;

        // Include the file only if neither class is already defined.
        // Use class_exists(..., false) to avoid triggering the autoloader here:
        // if the autoloader were invoked and successfully loaded the class, the
        // outer condition would become false and we'd skip instantiation entirely.
        if (!class_exists($classNameN, false) && !class_exists($classNameL, false)) {
            $path = $this->path($plugin->type, $plugin->name) . DS . $plugin->name . '.php';

            if (file_exists($path)) {
                require_once $path;
            }
        }

        if ($autocreate) {
            // Try namespaced first, then legacy
            foreach (array($classNameN, $classNameL) as $className) {
                if (!class_exists($className)) {
                    continue;
                }

                // BC alias: register legacy name so old references still resolve
                if ($className === $classNameN && !class_exists($classNameL, false)) {
                    class_alias($classNameN, $classNameL);
                }

                // Makes sure we have an event dispatcher
                if (!($dispatcher instanceof DispatcherInterface)) {
                    $dispatcher = new Dispatcher();
                }

                // Instantiate and register the plugin.
                return new $className($dispatcher, (array) $plugin);
            }
        }

        return null;
    }

    /**
     * Loads the published plugins.
     *
     * @return  array  An array of published plugins
     */
    public function all()
    {
        if (self::$plugins !== null) {
            return self::$plugins;
        }

        if (!App::has('cache.store') || !($cache = App::get('cache.store'))) {
            $cache = new \Hubzero\Cache\Storage\None();
        }

        $levels = implode(',', User::getAuthorisedViewLevels());

        if (!(self::$plugins = $cache->get('com_plugins.' . $levels))) {
            $db = App::get('db');

            $query = $db->getQuery()
                ->select('folder', 'type')
                ->select('element', 'name')
                ->select('protected')
                ->select('params')
                ->from('#__extensions')
                ->where('enabled', '>=', 1)
                ->whereEquals('type', 'plugin')
                ->where('state', '>=', 0)
                ->whereIn('access', User::getAuthorisedViewLevels())
                ->order('ordering', 'asc');

            self::$plugins = $db->setQuery($query->toString())->loadObjectList();

            if ($error = $db->getErrorMsg()) {
                throw new Exception($error, 500);
            }

            $cache->put('com_plugins.' . $levels, self::$plugins, App::get('config')->get('cachetime', 15));
        }

        return self::$plugins;
    }

    /**
     * Loads the language file for a plugin
     *
     * @param   string   $extension  Plugin name
     * @param   string   $basePath   Path to load from
     * @return  boolean
     */
    public function language($extension, $basePath = PATH_CORE)
    {
        return App::get('language')->load(strtolower($extension), $basePath);
    }

    /**
     * Names that need special handling for valid PHP identifiers.
     *
     * Covers PHP reserved words (default) and names with invalid
     * characters that can't be handled by simple PascalCase conversion.
     *
     * @var array<string, string>
     */
    private static $nameOverrides = [
        'default' => 'DefaultHandler',
    ];

    /**
     * Convert a plugin type or name to a valid PHP identifier.
     *
     * Handles hyphens (editors-xtd -> EditorsXtd) and reserved words
     * (default -> DefaultHandler).
     *
     * @param  string  $name  Directory name (e.g. 'system', 'editors-xtd', 'default')
     * @return string  Valid PHP identifier (e.g. 'System', 'EditorsXtd', 'DefaultHandler')
     */
    private static function toNamespacePart(string $name): string
    {
        if (isset(self::$nameOverrides[$name])) {
            return self::$nameOverrides[$name];
        }

        // Convert hyphens to PascalCase: editors-xtd -> EditorsXtd
        return implode('', array_map('ucfirst', explode('-', $name)));
    }
}
