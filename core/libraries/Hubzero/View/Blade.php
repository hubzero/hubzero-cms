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
            // Allow @include('modules.mod_foo.tmpl._partial') etc.
            $viewPaths[] = PATH_CORE;
        }

        $finder = new FileViewFinder($fs, $viewPaths);

        // Register template namespaces so @include('hubzero::partials.head')
        // resolves to app/templates/hubzero/partials/head.blade.php
        foreach (['hubzero', 'hzadmin'] as $tplNs) {
            if (defined('PATH_APP') && is_dir(PATH_APP . '/templates/' . $tplNs)) {
                $finder->addNamespace($tplNs, PATH_APP . '/templates/' . $tplNs);
            }
            if (defined('PATH_CORE') && is_dir(PATH_CORE . '/templates/' . $tplNs)) {
                $finder->addNamespace($tplNs, PATH_CORE . '/templates/' . $tplNs);
            }
        }

        $container = new Container();
        $dispatcher = new Dispatcher($container);

        $factory = new Factory($resolver, $finder, $dispatcher);

        // Bind factory + compiler into container for component tag resolution.
        // BladeCompiler::anonymousComponentPath() needs the view factory interface.
        // ComponentTagCompiler::guessClassName() needs an Application to get a
        // namespace for class-based component lookup — we provide a stub so the
        // lookup fails cleanly and falls through to anonymous resolution.
        $container->instance(Factory::class, $factory);
        $container->instance(\Illuminate\Contracts\View\Factory::class, $factory);
        $container->instance('view', $factory);
        $container->instance(BladeCompiler::class, $compiler);
        $container->instance(
            \Illuminate\Contracts\Foundation\Application::class,
            new class {
                public function getNamespace(): string
                {
                    return 'App\\';
                }
            }
        );
        $factory->setContainer($container);
        Container::setInstance($container);

        static::registerGlobalComponentPaths($compiler);

        return $factory;
    }

    /**
     * Register global and template-level anonymous component paths.
     *
     * Called once at factory creation. Extension-specific paths are
     * registered lazily by their loaders via registerPath().
     *
     * @param  BladeCompiler  $compiler
     */
    private static function registerGlobalComponentPaths(BladeCompiler $compiler): void
    {
        // Layer 1: Global components (no prefix)
        if (defined('PATH_CORE')) {
            $globalPath = PATH_CORE . '/blade/components';
            if (is_dir($globalPath)) {
                $compiler->anonymousComponentPath($globalPath);
            }
        }

        // Layer 2: Template global override (no prefix, wins over layer 1)
        if (defined('PATH_APP')) {
            $tplComponents = PATH_APP . '/templates/hubzero/blade/components';
            if (is_dir($tplComponents)) {
                $compiler->anonymousComponentPath($tplComponents);
            }
        }
    }

    /**
     * Register a blade/components directory for an extension.
     *
     * Called by Component/Module/Plugin loaders after resolving the
     * extension path. Also registers the template override directory
     * if it exists. Available for explicit use in edge cases.
     *
     * @param  string  $path    Resolved extension base path (e.g., core/components/com_blog)
     * @param  string  $prefix  Component prefix (e.g., com-blog)
     */
    public static function registerPath(string $path, string $prefix): void
    {
        $bladeDir = $path . '/blade/components';
        if (!is_dir($bladeDir)) {
            return;
        }

        $compiler = static::compiler();
        $compiler->anonymousComponentPath($bladeDir, $prefix);

        // Template override for this extension
        if (defined('PATH_APP')) {
            $override = PATH_APP . '/templates/hubzero/blade/components/' . $prefix;
            if (is_dir($override)) {
                $compiler->anonymousComponentPath($override, $prefix);
            }
        }
    }

    /**
     * Get the BladeCompiler instance.
     *
     * @return BladeCompiler
     */
    public static function compiler(): BladeCompiler
    {
        // Ensure factory is initialized (creates compiler)
        static::factory();

        return Container::getInstance()->make(BladeCompiler::class);
    }
}
