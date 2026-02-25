<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Component\Router\DefaultRouter;
use Hubzero\Component\Router\Legacy;
use Hubzero\Container\Container;
use Hubzero\Config\Registry;
use ReflectionClass;
use Exception;
use stdClass;
use Hubzero\Facades\Document;

/**
 * Component helper class
 */
class Loader
{
    /**
     * The application implementation.
     *
     * @public object
     */
    protected $app;

    /**
     * The component list cache
     *
     * @public array
     */
    protected static $components = array();

    /**
     * The component router cache
     *
     * @public array
     */
    protected static $routers = array();

    /**
     * Constructor
     *
     * @param   object  $app
     * @return  void
     */
    public function __construct(Container $app)
    {
        self::$components = array();
        self::$routers    = array();

        $this->app = $app;
    }

    /**
     * Checks if the component is enabled
     *
     * @param   string   $option  The component option.
     * @param   boolean  $strict  If set and the component does not exist, false will be returned.
     * @return  boolean
     */
    public function isEnabled($option, $strict = false)
    {
        // Consider changing the default to true to prevent using component names that haven't been registered in the db
        // andare therefore invalid
        $result = $this->load($option, $strict);

        return ($result->enabled == 1);// | $this->app->isAdmin());
    }

    /**
     * Gets the parameter object for the component
     *
     * @param   string   $option  The option for the component.
     * @param   boolean  $strict  If set and the component does not exist, false will be returned
     * @return  object   A Registry object.
     */
    public function params($option, $strict = false)
    {
        return $this->load($option, $strict)->params;
    }

    /**
     * Get the base path to a component
     *
     * @param   string  $option  The name of the component.
     * @return  string
     */
    public function path($option)
    {
        $result = $this->load($option);

        if (!isset($result->path)) {
            $result->path = '';

            $paths = array(
                PATH_APP . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . substr($result->option, 4),
                PATH_APP . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $result->option,
                PATH_CORE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . substr($result->option, 4),
                PATH_CORE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $result->option
            );

            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $result->path = $path;
                    break;
                }
            }
        }

        return $result->path;
    }

    /**
     * Make sure component name follows naming conventions
     *
     * @param   string  $option  The element value for the extension
     * @return  string
     */
    public function canonical($option)
    {
        if (is_array($option)) {
            $option = implode('', $option);
        }
        // do not allow dots in component name to avoid directory traversal issues
        $option = preg_replace('/[^A-Z0-9_-]/i', '', $option ? $option : '');
        // if option became empty due to the filtering, return an empty string
        if (strlen($option) > 0) {
            if (substr($option, 0, strlen('com_')) == 'com_') {
                // if option is just the prefix, make it empty because it's invalid
                if ($option == 'com_') {
                    $option = '';
                }
                // else it's presumably a good name and return that
            } else {
                // prepend com_ to the name if it doesn't start with com_
                $option = 'com_' . $option;
            }
        }
        return $option;
    }

    /**
     * Render the component.
     *
     * @param   string  $option  The component option.
     * @param   array   $params  The component parameters
     * @return  object
     */
    public function render($option, $params = array())
    {
        $client = (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);

        $lang = $this->app['language'];

        $option = $this->canonical($option);

        if (empty($option)) {
            // Throw 404 if no component
            $this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }

        // Record the scope
        $scope = $this->app->has('scope') ? $this->app->get('scope') : null;

        // Set scope to component name
        $this->app->set('scope', $option);

        // Build the component path.
        $file   = substr($option, 4);

        // Get component path
        define('PATH_COMPONENT', $this->path($option) . DIRECTORY_SEPARATOR . $client);
        define('PATH_COMPONENT_SITE', $this->path($option) . DIRECTORY_SEPARATOR . 'site');
        define('PATH_COMPONENT_ADMINISTRATOR', $this->path($option) . DIRECTORY_SEPARATOR . 'admin');

        // Legacy compatibility
        // @TODO: Deprecate this!
        define('JPATH_COMPONENT', PATH_COMPONENT);
        define('JPATH_COMPONENT_SITE', PATH_COMPONENT_SITE);
        define('JPATH_COMPONENT_ADMINISTRATOR', PATH_COMPONENT_ADMINISTRATOR);

        $path      = PATH_COMPONENT . DIRECTORY_SEPARATOR . $file . '.php';
        $compName  = ucfirst(substr($option, 4));
        $namespace = '\\Components\\' . $compName . '\\' . ucfirst($client) . '\\' . $compName;
        $found     = false;
        $react_path = PATH_COMPONENT .
            DIRECTORY_SEPARATOR .
            'assets' .
            DIRECTORY_SEPARATOR .
            'react' .
            DIRECTORY_SEPARATOR .
            $file;

        // Make sure the component is enabled
        if ($this->isEnabled($option)) {
            // Include the CamelCase bootstrap file if it exists (Components\ is not in composer PSR-4)
            $bootstrapPath = PATH_COMPONENT . DIRECTORY_SEPARATOR . $compName . '.php';
            if (!class_exists($namespace, false) && is_file($bootstrapPath)) {
                require_once $bootstrapPath;
            }

            // Check if the bootstrap class is available (no autoload to avoid executing procedural entry files)
            if (class_exists($namespace, false)) {
                $found = true;
                $path  = $namespace;
                $type  = 'bootstrap';

                // Infer the appropriate language path and load from there
                $file  = with(new \ReflectionClass($namespace))->getFileName();
                $bits  = explode(DIRECTORY_SEPARATOR, $file);
                $local = implode(DIRECTORY_SEPARATOR, array_slice($bits, 0, -1));

                // Load local language files
                $lang->load($option, $local, null, false, true);
            } elseif (is_file($path)) {
                $found = true;
                $type  = 'path';

                // Load local language files
                $lang->load($option, PATH_COMPONENT, null, false, true);
            } elseif (is_dir($react_path)) {
                $found = true;
                $path = $react_path;
                $type = 'react';

                // Load local language files
                $lang->load($option, PATH_COMPONENT, null, false, true);
            } elseif (is_dir(PATH_COMPONENT)) {
                $found = true;
                $path = $option;
                $type = 'default';

                // Load local language files
                $lang->load($option, PATH_COMPONENT, null, false, true);
            }
        }

        // Make sure we found something
        if (!$found) {
            $this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND_OR_ENABLED'));
        }

        // Load base language file
        $lang->load($option, PATH_APP, null, false, true);

        // Handle template preview outlining.
        $contents = null;

        // Execute the component.
        $contents = $this->execute($path, $type);

        // Revert the scope
        $this->app->forget('scope');
        $this->app->set('scope', $scope);

        return $contents;
    }

    /**
     * Execute the component.
     *
     * @param   string  $path  The component path.
     * @return  string  The component output
     */
    protected function execute($path, $type = 'path')
    {
        ob_start();

        if ($type == 'path') {
            $this->executePath($path);
        } elseif ($type == 'react') {
            $this->executeReactApp($path);
        } elseif ($type == 'default') {
            $this->executeDefault($path);
        } elseif ($type == 'bootstrap') {
            $this->executeBootstrap($path);
        }

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    /**
     * Execute the component from an old path based component
     *
     * @param   string  $path  The component path
     * @return  void
     */
    protected function executePath($path)
    {
        require_once $path;
    }

    /**
     * Start component without explicit entrypoint file
     *
     * @param   string  $namespace  The namespace of the component to start
     * @return  void
     */
    private function executeDefault($option = '')
    {
        $option = (string) $option;

        if (substr($option, 0, 4) == 'com_') {
            $component = substr($option, 4);
        } else {
            $component = $option;
        }

        if (preg_match("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", $component) !== 1) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid component name [%s] requested',
                htmlspecialchars($component, ENT_HTML5)
            ), 404);
        }

        $controller = \Hubzero\Facades\Request::getCmd('controller', $component);

        if (preg_match("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", $controller) !== 1) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid controller name [%s] requested',
                htmlspecialchars($controller, ENT_HTML5)
            ), 404);
        }

        $path = PATH_COMPONENT;

        $config = array('base_path' => PATH_COMPONENT);
        $namespace = 'Components\\' . ucfirst($component) . '\Site\Controllers';
        $class = ucfirst($controller);

        if (file_exists($path . "/controllers/{$controller}.php")) {
            require_once($path . "/controllers/{$controller}.php");
        } else {
            eval("namespace {$namespace}; class {$class} extends \\Hubzero\\Component\\DefaultSiteController\n{\n}\n");
        }

        (new ($namespace . '\\' . $class)($config))->execute();
    }

    /**
     * Execute the component from a bootstrapped component class.
     *
     * The class must extend AbstractComponent and implement execute().
     *
     * @param   string  $namespace  The fully-qualified class name
     * @return  void
     */
    protected function executeBootstrap($namespace)
    {
        if (is_subclass_of($namespace, AbstractComponent::class)) {
            $option = $this->app->get('scope');
            $params = $this->params($option);
            $component = new $namespace($params);
        } else {
            $component = new $namespace();
        }

        $component->start();
    }

    /**
     * Execute the component from a React App
     *
     * @param   string  $path  The path to the react app
     * @return  void
     */
    protected function executeReactApp($path)
    {
        $index = file_get_contents($path . DS . 'build' . DS . 'index.html');

        $dom = new \DOMDocument();
        $dom->loadHTML($index);

        $bodies = $dom->getElementsByTagName('body');

        assert($bodies->length === 1);

        $heads = $dom->getElementsByTagName('head');

        assert($heads->length === 1);

        $body = $bodies->item(0);
        $head = $heads->item(0);

        foreach ($head->childNodes as $node) {
            if ($node->nodeName == 'meta') {
                if ($node->hasAttribute('charset')) {
                    Document::setCharset($node->getAttribute('charset'));
                    // Doesn't seem to change anything
                } elseif ($node->hasAttribute('name')) {
                    Document::setMetaData($node->getAttribute('name'), $node->getAttribute('content'));
                } elseif ($node->hasAttribute('http-equiv')) {
                    Document::setMetaData($node->getAttribute('http-equiv'), $node->getAttribute('content'), true);
                } elseif ($node->hasAttribute('itemprop')) {
                    // Not supported
                }
            }

            if ($node->nodeName == 'title') {
                Document::setTitle($node->nodeValue);
                // Oddly concatenated titles
            }

            if ($node->nodeName == 'link') {
                $rel = $node->getAttribute('rel');
                $href = $node->getAttribute('href');

                if ($rel == 'stylesheet') {
                    Document::addStyleSheet($href);
                } elseif ($rel == 'script') {
                    Document::addScript($href);
                } else {
                    Document::addHeadLink($href, $rel);
                }
            }

            if ($node->nodeName == 'script') {
                $defer = $node->getAttribute('defer');
                $async = $node->getAttribute('async');
                $src = $node->getAttribute('src');

                Document::addScript($src, "text/javascript", ($defer == 'defer'), ($async == 'async'));
            }
        }

        echo substr($dom->saveHtml($body), 6, -7); // remove <body></body>
    }

    /**
     * Get component router
     *
     * @param   string  $option  Name of the component
     * @param   string  $client  Client to load the router for
     * @return  object  Component router
     */
    public function router($option, $client = null, $version = null)
    {
        $option = $this->canonical($option);
        $client = ($client ? $client : $this->app['client']->alias);
        $key    = $option . $client;

        if (!isset(self::$routers[$key])) {
            $compname = ucfirst(substr($option, 4));

            $client = ucfirst($client);

            $legacy = $compname . 'Router';
            $name   = '\\Components\\' . $compname . '\\' . $client . '\\Router';

            if (!class_exists($name) && !class_exists($legacy)) {
                // Use the component routing handler if it exists
                $paths = array();
                if (!is_null($version)) {
                    $paths[] = $this->path($option) .
                        DIRECTORY_SEPARATOR .
                        strtolower($client) .
                        DIRECTORY_SEPARATOR .
                        'routerv' .
                        $version .
                        '.php';
                }
                $paths[] = $this->path($option) .
                    DIRECTORY_SEPARATOR .
                    strtolower($client) .
                    DIRECTORY_SEPARATOR .
                    'router.php';
                $paths[] = $this->path($option) . DIRECTORY_SEPARATOR . 'router.php';

                // Use the custom routing handler if it exists
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        require_once $path;
                        break;
                    }
                }
            }

            if (class_exists($name)) {
                $reflection = new ReflectionClass($name);

                if (in_array('Hubzero\\Component\\Router\\RouterInterface', $reflection->getInterfaceNames())) {
                    self::$routers[$key] = new $name();
                }
            } elseif (class_exists($legacy)) {
                $reflection = new ReflectionClass($legacy);

                if (in_array('Hubzero\\Component\\Router\\RouterInterface', $reflection->getInterfaceNames())) {
                    self::$routers[$key] = new $legacy();
                }
            }

            if (!isset(self::$routers[$key])) {
                $path = $this->path($option);
                $name = substr($option, 4);

                if (!is_file("{$path}/{$name}.php") && !is_file("{$path}/controllers/{$name}.php")) {
                    self::$routers[$key] = new DefaultRouter($compname);
                } else {
                    self::$routers[$key] = new Legacy($compname);
                }
            }
        }

        return self::$routers[$key];
    }

    /**
     * Load the installed components into the components property.
     *
     * @param   string   $option  The element value for the extension
     * @param   boolean  $strict  If set and the component does not exist, the enabled attribute will be set to false.
     * @return  object
     */
    public function load($option, $strict = false)
    {
        $option = $this->canonical($option);

        if (isset(self::$components[$option])) {
            return self::$components[$option];
        }

        // Do we have a database connection?
        try {
            $db = $this->app->get('db');

            $query = $db->getQuery()
                ->select('extension_id', 'id')
                ->select('element', '"option"')
                ->select('params')
                ->select('enabled')
                ->from('#__extensions')
                ->whereEquals('type', 'component')
                ->whereEquals('element', $option);

            $db->setQuery($query->toString());

            if (!$this->app->has('cache.store') || !($cache = $this->app['cache.store'])) {
                $cache = new \Hubzero\Cache\Storage\None();
            }

            if (!($data = $cache->get('_system.' . $option))) {
                $data = $db->loadObject();
                $cache->put('_system.' . $option, $data, $this->app['config']->get('cachetime', 15));
            }

            self::$components[$option] = $data;

            if ($error = $db->getErrorMsg()) {
                throw new Exception($this->
                    app['language']->
                    translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error), 500);
            }
        } catch (\Hubzero\Database\Exception\ConnectionFailedException $e) {
        }

        // Create a default object
        if (empty(self::$components[$option])) {
            self::$components[$option] = new stdClass();
            self::$components[$option]->option  = $option;
            self::$components[$option]->enabled = $strict ? 0 : 1;
            self::$components[$option]->params  = '';
            self::$components[$option]->id      = 0;
        }

        // Convert the params to an object.
        if (is_string(self::$components[$option]->params)) {
            self::$components[$option]->params = new Registry(self::$components[$option]->params);
        }

        return self::$components[$option];
    }
}
