<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Component;

use Hubzero\Framework\Config\Registry;
use Illuminate\Contracts\Foundation\Application;

/**
 * Finds and executes HubZero components.
 *
 * Simplified port of core/libraries/Hubzero/Component/Loader.php.
 * No database lookups — components are discovered by filesystem.
 */
class Loader
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Render a component and return its HTML output.
     */
    public function render(string $option): string
    {
        $option = $this->canonical($option);

        if (empty($option)) {
            abort(404, 'Component not found.');
        }

        $componentPath = $this->path($option);

        if (!$componentPath) {
            abort(404, sprintf('Component "%s" not found.', $option));
        }

        $client = app()->bound('hubzero.client')
            ? app('hubzero.client')->alias
            : 'site';
        $clientPath = $componentPath . DIRECTORY_SEPARATOR . $client;

        // Define PATH_COMPONENT for legacy compatibility
        if (!defined('PATH_COMPONENT')) {
            define('PATH_COMPONENT', $clientPath);
        }
        if (!defined('PATH_COMPONENT_SITE')) {
            define('PATH_COMPONENT_SITE', $componentPath . DIRECTORY_SEPARATOR . 'site');
        }
        if (!defined('PATH_COMPONENT_ADMINISTRATOR')) {
            define('PATH_COMPONENT_ADMINISTRATOR', $componentPath . DIRECTORY_SEPARATOR . 'admin');
        }

        // Register class aliases that legacy components expect as global classes.
        // These must be set before the component runs, since component views
        // (e.g. com_cpanel/html/default.php) reference Toolbar::, Submenu::, etc.
        $aliases = [
            'Toolbar' => \Hubzero\Facades\Toolbar::class,
            'Submenu' => \Hubzero\Facades\Submenu::class,
            'Session' => \Hubzero\Facades\Session::class,
            'Module'  => \Hubzero\Facades\Module::class,
        ];
        foreach ($aliases as $alias => $class) {
            if (!class_exists($alias, false)) {
                class_alias($class, $alias);
            }
        }

        // Register generic autoloader for Components\ namespace
        $compName = ucfirst(substr($option, 4));
        $this->registerComponentAutoloader();

        // Find and execute the component
        $clientNs = ucfirst($client); // 'Site' or 'Admin'
        $namespace = '\\Components\\' . $compName . '\\' . $clientNs . '\\' . $compName;
        $bootstrapPath = $clientPath . DIRECTORY_SEPARATOR . $compName . '.php';

        // Ensure HubZero's Route facade is available as 'Route' for legacy code
        if (!class_exists('Route', false)) {
            class_alias(\Hubzero\Framework\Facades\LegacyRoute::class, 'Route');
        }

        // Auto-load the component's language file
        if ($this->app->bound('hubzero.lang')) {
            $this->app->make('hubzero.lang')->load($option, $componentPath);
        }

        ob_start();

        // Try bootstrap class
        if (!class_exists($namespace, false) && is_file($bootstrapPath)) {
            require_once $bootstrapPath;
        }

        $isComponent = class_exists($namespace, false) && (
            is_subclass_of($namespace, AbstractComponent::class)
            || is_subclass_of($namespace, \Hubzero\Component\AbstractComponent::class)
        );

        if ($isComponent) {
            // Pass null to let the component use its own Registry type
            $component = new $namespace(null);
            $component->start();
        } elseif (is_dir($clientPath)) {
            // Default: try to instantiate controller directly
            $this->executeDefault($option, $compName, $clientPath);
        }

        return ob_get_clean();
    }

    /**
     * Normalize a component name to com_xxx format.
     */
    public function canonical(string $option): string
    {
        $option = preg_replace('/[^A-Z0-9_-]/i', '', $option);

        if (empty($option)) {
            return '';
        }

        if (!str_starts_with($option, 'com_')) {
            $option = 'com_' . $option;
        }

        return $option === 'com_' ? '' : $option;
    }

    /**
     * Find the component directory.
     *
     * Matches directories that contain the requested client subdirectory
     * (site/ or admin/). Defaults to the current client context.
     */
    public function path(string $option, ?string $client = null): string
    {
        $name = substr($option, 4);

        if ($client === null) {
            $client = app()->bound('hubzero.client')
                ? app('hubzero.client')->alias
                : 'site';
        }

        $searchPaths = [
            base_path('packages/hubzero/components/' . $option),
            base_path('packages/hubzero/components/com_' . $name),
            base_path('core/components/' . $option),
            base_path('core/components/com_' . $name),
        ];

        foreach ($searchPaths as $path) {
            if (is_dir($path) && is_dir($path . DIRECTORY_SEPARATOR . $client)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Get component parameters from the extensions table.
     */
    public function params(string $option): Registry
    {
        if (app()->bound('hubzero.component')) {
            return app('hubzero.component')->params($option);
        }
        return new Registry();
    }

    private bool $autoloaderRegistered = false;

    /**
     * Register a generic autoloader for all Components\* classes.
     *
     * Unlike the legacy per-component autoloader, this handles cross-component
     * dependencies (e.g., com_content using Components\Categories\*) by
     * extracting the component name from the namespace and finding the
     * directory dynamically.
     */
    private function registerComponentAutoloader(): void
    {
        if ($this->autoloaderRegistered) {
            return;
        }
        $this->autoloaderRegistered = true;

        spl_autoload_register(function (string $class) {
            if (!str_starts_with($class, 'Components\\')) {
                return;
            }

            $parts = explode('\\', substr($class, strlen('Components\\')));
            $compName = array_shift($parts);
            $lname = strtolower($compName);

            // Find the component directory — no site/ requirement here,
            // since library components like com_categories have only
            // helpers/ and models/.
            $compDir = null;
            foreach ([
                base_path("core/components/com_{$lname}"),
                base_path("packages/hubzero/components/com_{$lname}"),
            ] as $dir) {
                if (is_dir($dir)) {
                    $compDir = $dir;
                    break;
                }
            }

            if (!$compDir) {
                return;
            }

            // Resolve remaining namespace to file path
            $fileName = array_pop($parts);
            $subDir = implode('/', array_map('strtolower', $parts));
            $base = $compDir . '/' . ($subDir ? $subDir . '/' : '');

            // Try lowercase filename (HubZero convention)
            $path = $base . strtolower($fileName) . '.php';
            if (is_file($path)) {
                require_once $path;
                return;
            }

            // Try PascalCase filename
            $path = $base . $fileName . '.php';
            if (is_file($path)) {
                require_once $path;
            }
        });
    }

    /**
     * Execute a component without an explicit entry point class.
     */
    private function executeDefault(string $option, string $compName, string $clientPath): void
    {
        $component = strtolower($compName);
        $controllerName = app('hubzero.request')->getCmd('controller', $component);
        $controllerName = preg_replace('/[^A-Z0-9_]/i', '', $controllerName);

        $namespace = 'Components\\' . $compName . '\\Site\\Controllers';
        $class = $namespace . '\\' . ucfirst(strtolower($controllerName));

        if (class_exists($class)) {
            $controller = new $class(['base_path' => $clientPath]);
            $controller->execute();
        }
    }
}
