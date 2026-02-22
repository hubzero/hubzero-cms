<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Traits;

use Hubzero\Htmx\Htmx;

/**
 * Convenience helpers for HTMX debug workflows in components/controllers.
 */
trait UsesHtmxDebug
{
    /**
     * @return  bool
     */
    protected function htmxDebugEnabled(): bool
    {
        return Htmx::isDebugEnabled();
    }

    /**
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    protected function htmxDebugQuery(array $params, string $name = 'htmx_debug', string $value = '1'): array
    {
        return Htmx::preserveDebugParam($params, $name, $value);
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    protected function htmxDebugField(string $name = 'htmx_debug', string $value = '1'): string
    {
        return Htmx::renderDebugHiddenInput($name, $value);
    }

    /**
     * @param   array  $snapshot
     * @param   array  $options
     * @return  string
     */
    protected function htmxDebugBar(array $snapshot = array(), array $options = array()): string
    {
        return Htmx::renderDebugPanel($snapshot, $options);
    }
}
