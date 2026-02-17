<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Config\Registry;
use Hubzero\Htmx\HtmxService;
use Hubzero\Inertia\InertiaService;

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

        $this->bootHtmx();
        $this->bootInertia();

        $this->init();

        $this->booted = true;
    }

    /**
     * Give an HTMX component its service and let it register shared state.
     *
     * Skipped entirely unless the component asks for HTMX, so a component that
     * does not use it never causes the service to be constructed.
     *
     * @return void
     */
    protected function bootHtmx(): void
    {
        if (!($this instanceof HtmxComponentInterface) || !$this->htmxEnabled()) {
            return;
        }

        $app = \App::get('app');

        $service = null;
        if ($app->has('htmx')) {
            $candidate = $app->get('htmx');
            if ($candidate instanceof HtmxService) {
                $service = $candidate;
            }
        }

        if (!$service) {
            $service = new HtmxService();
            $app->set('htmx', $service);
        }

        $this->registerHtmx($service);
        $service->varyOnRequest();
    }

    /**
     * Give an Inertia component its service and let it register shared props.
     *
     * @return void
     */
    protected function bootInertia(): void
    {
        if (!($this instanceof InertiaComponentInterface) || !$this->inertiaEnabled()) {
            return;
        }

        $app = \App::get('app');

        $service = null;
        if ($app->has('inertia')) {
            $candidate = $app->get('inertia');
            if ($candidate instanceof InertiaService) {
                $service = $candidate;
            }
        }

        if (!$service) {
            $service = new InertiaService();
            $app->set('inertia', $service);
        }

        $this->registerInertia($service);
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
     * Component-specific request handling.
     *
     * Override in subclasses to dispatch controllers, render
     * views, etc.
     *
     * @return void
     */
    abstract protected function execute(): void;
}
