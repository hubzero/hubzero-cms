<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * Standalone Blade renderer.
 *
 * Works in both the legacy system (:8443) and the Laravel system (:9443).
 * When Laravel's view factory is available it delegates to that; otherwise
 * it creates a minimal standalone factory using illuminate/view.
 */
class Blade
{
    /**
     * Standalone view factory (used when Laravel is not available).
     *
     * @var Factory|null
     */
    private static ?Factory $factory = null;

    /**
     * Render a .blade.php file with the given data.
     *
     * @param  string  $path  Absolute path to the .blade.php file
     * @param  array   $data  Variables to pass to the template
     * @return string  Rendered HTML
     */
    public static function render(string $path, array $data = []): string
    {
        return static::factory()->file($path, $data)->render();
    }

    /**
     * Get or create the Blade view factory.
     *
     * If Laravel's view factory is bound in the container (running under
     * the Laravel system on :9443), uses that. Otherwise builds a minimal
     * standalone factory for the legacy system on :8443.
     *
     * @return Factory
     */
    public static function factory(): Factory
    {
        // Use Laravel's factory if available
        if (function_exists('app')) {
            try {
                $app = app();
                if ($app->bound('view')) {
                    return $app->make('view');
                }
            } catch (\Throwable $e) {
                // Container not available, fall through to standalone
            }
        }

        if (static::$factory === null) {
            static::$factory = static::createFactory();
        }

        return static::$factory;
    }

    /**
     * Create a standalone Blade view factory.
     *
     * Sets up the minimum required services: filesystem, compiler,
     * engine resolver, view finder, and event dispatcher.
     *
     * @return Factory
     */
    private static function createFactory(): Factory
    {
        $fs = new Filesystem();

        // Compiled Blade templates go here
        $cachePath = defined('PATH_APP')
            ? PATH_APP . '/cache/views'
            : sys_get_temp_dir() . '/hubzero-blade-cache';

        if (!is_dir($cachePath)) {
            @mkdir($cachePath, 0755, true);
        }

        $compiler = new BladeCompiler($fs, $cachePath);

        $resolver = new EngineResolver();
        $resolver->register('blade', fn () => new CompilerEngine($compiler));

        // View finder with template directories
        $viewPaths = [];
        if (defined('PATH_APP')) {
            $viewPaths[] = PATH_APP . '/templates';
        }
        if (defined('PATH_CORE')) {
            $viewPaths[] = PATH_CORE . '/templates';
        }

        $finder = new FileViewFinder($fs, $viewPaths);

        // Register template namespaces so @include('hubzero::partials.head')
        // resolves to app/templates/hubzero/partials/head.blade.php
        if (defined('PATH_APP') && is_dir(PATH_APP . '/templates/hubzero')) {
            $finder->addNamespace('hubzero', PATH_APP . '/templates/hubzero');
        }
        if (defined('PATH_CORE') && is_dir(PATH_CORE . '/templates/hubzero')) {
            $finder->addNamespace('hubzero', PATH_CORE . '/templates/hubzero');
        }

        $container = new Container();
        $dispatcher = new Dispatcher($container);

        return new Factory($resolver, $finder, $dispatcher);
    }
}
