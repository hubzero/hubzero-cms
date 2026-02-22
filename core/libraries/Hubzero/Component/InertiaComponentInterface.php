<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Inertia\InertiaService;

/**
 * Contract for components that opt into Inertia support at component boot time.
 */
interface InertiaComponentInterface
{
    /**
     * Whether this component enables Inertia support.
     *
     * @return  bool
     */
    public function inertiaEnabled(): bool;

    /**
     * Configure Inertia for this component request lifecycle.
     *
     * @param   InertiaService  $inertia
     * @return  void
     */
    public function registerInertia(InertiaService $inertia): void;
}
