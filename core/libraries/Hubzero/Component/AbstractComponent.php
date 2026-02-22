<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Config\Registry;
use Hubzero\Inertia\InertiaService;
use Hubzero\Inertia\InertiaServiceProvider;

/**
 * Abstract base class for component entry points.
 *
 * Provides a lifecycle for components: boot -> start -> flush.
 *
 * - start() is the entry point called by the Loader.
 * - boot() initializes config and state once per lifecycle.
 * - flush() resets mutable state so the instance can handle
 *   another request (e.g. under Swoole, RoadRunner, etc.).
 */
abstract class AbstractComponent
{
    /**
     * Component parameters from the extensions table.
     *
     * @var Registry
     */
    protected Registry $params;

    /**
     * Mutable request-scoped state.
     *
     * @var Registry
     */
    protected Registry $state;

    /**
     * Whether boot() has already run.
     *
     * @var bool
     */
    protected bool $booted = false;

    /**
     * Constructor.
     *
     * @param  Registry|null  $params  Component parameters
     */
    public function __construct(?Registry $params = null)
    {
        $this->params = $params ?? new Registry();
        $this->state = new Registry();
    }

    /**
     * Entry point called by the Loader.
     *
     * @param  bool  $autoboot  Automatically call boot() if not yet booted.
     * @return void
     */
    public function start(bool $autoboot = true): void
    {
        if ($autoboot) {
            $this->boot();
        }

        $this->execute();
    }

    /**
     * Initialize the component. Only runs once per lifecycle;
     * subsequent calls are no-ops until flush() resets the flag.
     *
     * @param  bool  $autoflush  Call flush() before booting to ensure a clean slate.
     * @return void
     */
    public function boot(bool $autoflush = true): void
    {
        if ($this->booted) {
            return;
        }

        if ($autoflush) {
            $this->flush();
        }

        $this->init();
        $this->bootInertia();

        $this->booted = true;
    }

    /**
     * Reset mutable state so the instance can handle another request.
     *
     * Subclasses should call parent::flush() when overriding.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->state = new Registry();
        $this->booted = false;
    }

    /**
     * Component-specific initialization.
     *
     * Override in subclasses to set up config, register event
     * listeners, etc. Called once per boot cycle.
     *
     * @return void
     */
    protected function init(): void
    {
    }

    /**
     * Boot Inertia support for components that opt in via InertiaComponentInterface.
     *
     * @return  void
     */
    protected function bootInertia(): void
    {
        if (!($this instanceof InertiaComponentInterface) || !$this->inertiaEnabled()) {
            return;
        }

        if (!class_exists('\\App')) {
            return;
        }

        $app = \App::get('app');
        if (!$app) {
            return;
        }

        if (!$app->has('inertia')) {
            $app->register(new InertiaServiceProvider($app));
        }

        $inertia = $app->get('inertia');
        if (!$inertia instanceof InertiaService) {
            $inertia = new InertiaService();
            $app->set('inertia', $inertia);
        }

        $inertia->flush(true);
        $this->registerInertia($inertia);
    }

    /**
     * Component-specific request handling.
     *
     * Override in subclasses to dispatch controllers, render
     * views, etc.
     *
     * @return void
     */
    abstract protected function execute(): void;
}
