<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Component;

use Hubzero\Framework\Config\Registry;

/**
 * Abstract base class for component entry points.
 *
 * Provides a lifecycle: boot → start → flush.
 */
abstract class AbstractComponent
{
    protected Registry $params;
    protected Registry $state;
    protected bool $booted = false;

    public function __construct(?Registry $params = null)
    {
        $this->params = $params ?? new Registry();
        $this->state = new Registry();
    }

    /**
     * Entry point called by the Loader.
     */
    public function start(bool $autoboot = true): void
    {
        if ($autoboot) {
            $this->boot();
        }

        $this->execute();
    }

    /**
     * Initialize the component. Only runs once per lifecycle.
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
        $this->booted = true;
    }

    /**
     * Reset mutable state for a fresh request.
     */
    public function flush(): void
    {
        $this->state = new Registry();
        $this->booted = false;
    }

    /**
     * Component-specific initialization hook.
     */
    protected function init(): void
    {
    }

    /**
     * Component-specific request handling.
     */
    abstract protected function execute(): void;
}
