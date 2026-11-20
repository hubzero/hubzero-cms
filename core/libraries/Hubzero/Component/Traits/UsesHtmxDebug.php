<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @param   array<mixed>   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array<mixed>
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
     * @param   array<mixed>  $snapshot
     * @param   array<mixed>  $options
     * @return  string
     */
    protected function htmxDebugBar(array $snapshot = [], array $options = []): string
    {
        return Htmx::renderDebugPanel($snapshot, $options);
    }
}
