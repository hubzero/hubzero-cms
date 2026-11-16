<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Traits;

use Hubzero\Inertia\Inertia;

/**
 * Convenience helpers for Inertia debug workflows in components/controllers.
 */
trait UsesInertiaDebug
{
    /**
     * @return  bool
     */
    protected function inertiaDebugEnabled(): bool
    {
        return Inertia::isDebugEnabled();
    }

    /**
     * @param   array<mixed>   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array<mixed>
     */
    protected function inertiaDebugQuery(array $params, string $name = 'inertia_debug', string $value = '1'): array
    {
        return Inertia::preserveDebugParam($params, $name, $value);
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    protected function inertiaDebugField(string $name = 'inertia_debug', string $value = '1'): string
    {
        return Inertia::renderDebugHiddenInput($name, $value);
    }

    /**
     * @param   array<mixed>  $snapshot
     * @param   array<mixed>  $options
     * @return  string
     */
    protected function inertiaDebugBar(array $snapshot = [], array $options = []): string
    {
        return Inertia::renderDebugPanel($snapshot, $options);
    }
}
