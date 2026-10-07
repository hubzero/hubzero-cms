<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template\Tests\Support;

use Hubzero\Container\Container;
use Hubzero\Facades\Facade;
use Hubzero\Template\Overrides;

/**
 * A throwaway site template and supergroup template on disk, with the
 * container the override machinery reads pointed at them
 *
 * Call restore() in tearDown().
 */
class Environment
{
    /**
     * Root of the throwaway tree
     *
     * @var  string
     */
    public $root;

    /**
     * The site template directory (named "site")
     *
     * @var  string
     */
    public $siteTemplate;

    /**
     * The supergroup's base directory (holds template/)
     *
     * @var  string
     */
    public $groupBase;

    /**
     * The container the bootstrap installed, restored by restore()
     *
     * @var  mixed
     */
    private $previousApp;

    /**
     * Build the tree and swap in a container that points at it
     *
     * @param   bool  $groupTemplate  Create the supergroup's template directory
     * @return  self
     */
    public static function create(bool $groupTemplate = true): self
    {
        $env = new self();

        $env->root = sys_get_temp_dir() . '/hz-overrides-' . getmypid() . '-' . uniqid();
        $env->siteTemplate = $env->root . '/templates/site';
        $env->groupBase = $env->root . '/groups/42';

        mkdir($env->siteTemplate, 0700, true);
        mkdir($env->groupBase . ($groupTemplate ? '/template' : ''), 0700, true);

        $site = $env->siteTemplate;
        $app  = new Container();

        $app['template'] = function () use ($site) {
            return (object) ['template' => 'site', 'path' => $site];
        };

        $app['client'] = function () {
            return (object) ['name' => 'site', 'alias' => 'site'];
        };

        $app['request'] = function () {
            return new class {
                public function getCmd($name, $default = '')
                {
                    return $default;
                }
                public function base($pathonly = false)
                {
                    return '';
                }
                public function __call($name, $arguments)
                {
                    return isset($arguments[1]) ? $arguments[1] : '';
                }
            };
        };

        $app['app'] = function () use ($app) {
            return new class ($app) {
                private $container;
                public function __construct($container)
                {
                    $this->container = $container;
                }
                public function isAdmin()
                {
                    return false;
                }
                public function get($id)
                {
                    return $this->container->get($id);
                }
                public function has($id)
                {
                    return $this->container->has($id);
                }
            };
        };

        $env->previousApp = Facade::getApplication();
        Facade::setApplication($app);

        $env->withSuperGroup();

        return $env;
    }

    /**
     * Render inside a supergroup whose base directory is this tree's
     *
     * Overrides prefixes the group's base path with PATH_APP, so hand it a
     * path that climbs back out to the throwaway tree.
     *
     * @return  self
     */
    public function withSuperGroup(): self
    {
        $base = str_repeat('/..', count(array_filter(explode('/', PATH_APP)))) . $this->groupBase;

        Overrides::setSuperGroup(new class ($base) {
            private $base;
            public function __construct($base)
            {
                $this->base = $base;
            }
            public function isSuperGroup()
            {
                return true;
            }
            public function getBasePath()
            {
                return $this->base;
            }
        });

        return $this;
    }

    /**
     * Render outside any supergroup
     *
     * @return  self
     */
    public function withoutSuperGroup(): self
    {
        Overrides::setSuperGroup(false);

        return $this;
    }

    /**
     * Create a file under the tree
     *
     * @param   string  $relative  Path relative to the tree root
     * @param   string  $contents
     * @return  string  Absolute path
     */
    public function file(string $relative, string $contents = ''): string
    {
        $path = $this->root . '/' . $relative;

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0700, true);
        }

        file_put_contents($path, $contents);

        return $path;
    }

    /**
     * Put the container back and remove the tree
     *
     * @return  void
     */
    public function restore(): void
    {
        Overrides::reset();
        Facade::setApplication($this->previousApp);

        self::rmtree($this->root);
    }

    /**
     * Remove a directory tree
     *
     * @param   string  $path
     * @return  void
     */
    private static function rmtree(string $path): void
    {
        if (is_file($path) || is_link($path)) {
            unlink($path);
            return;
        }
        if (!is_dir($path)) {
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                self::rmtree($path . '/' . $entry);
            }
        }
        rmdir($path);
    }
}
