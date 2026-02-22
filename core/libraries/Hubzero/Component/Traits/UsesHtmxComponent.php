<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Traits;

use Hubzero\Htmx\HtmxService;

/**
 * Convenience trait for components implementing HtmxComponentInterface.
 */
trait UsesHtmxComponent
{
    /**
     * @return  bool
     */
    public function htmxEnabled(): bool
    {
        return true;
    }

    /**
     * @param   HtmxService  $htmx
     * @return  void
     */
    public function registerHtmx(HtmxService $htmx): void
    {
        $state = $this->mergeHtmxConfigIntoState($this->htmxState());
        if (!empty($state)) {
            $htmx->state($state);
        }
    }

    /**
     * Override for component-global HTMX/Alpine shared state.
     *
     * @return  array
     */
    protected function htmxState(): array
    {
        return array();
    }

    /**
     * Component-level HTMX runtime security configuration.
     *
     * Override in component entry class when a component needs a different policy.
     *
     * @return  array
     */
    protected function htmxSecurityConfig(): array
    {
        return array(
            'allowEval'       => false,
            'allowScriptTags' => false,
            'historyCacheSize' => 20
        );
    }

    /**
     * Merge security runtime config into shared HTMX state payload.
     *
     * @param   array  $state
     * @return  array
     */
    protected function mergeHtmxConfigIntoState(array $state): array
    {
        $security = $this->htmxSecurityConfig();
        if (empty($security)) {
            return $state;
        }

        if (!isset($state['htmx']) || !is_array($state['htmx'])) {
            $state['htmx'] = array();
        }
        if (!isset($state['htmx']['security']) || !is_array($state['htmx']['security'])) {
            $state['htmx']['security'] = array();
        }

        $state['htmx']['security'] = array_merge($state['htmx']['security'], $security);

        return $state;
    }
}
