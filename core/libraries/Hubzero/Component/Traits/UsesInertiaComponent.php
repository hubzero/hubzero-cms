<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Traits;

use Hubzero\Inertia\InertiaService;

/**
 * Convenience trait for components implementing InertiaComponentInterface.
 */
trait UsesInertiaComponent
{
    /**
     * @return  bool
     */
    public function inertiaEnabled(): bool
    {
        return true;
    }

    /**
     * @param   InertiaService  $inertia
     * @return  void
     */
    public function registerInertia(InertiaService $inertia): void
    {
        $rootView = $this->inertiaRootView();
        if (is_string($rootView) && trim($rootView) !== '') {
            $inertia->rootView($rootView);
        }

        $resolver = $this->inertiaVersionResolver();
        if ($resolver !== null) {
            $inertia->version($resolver);
        }

        $shared = $this->inertiaSharedProps();
        if (!empty($shared)) {
            $inertia->share($shared);
        }
    }

    /**
     * Override to provide a component-specific root view.
     *
     * @return  string|null
     */
    protected function inertiaRootView(): ?string
    {
        return null;
    }

    /**
     * Override to provide a string version or resolver callable.
     *
     * @return  mixed
     */
    protected function inertiaVersionResolver()
    {
        return null;
    }

    /**
     * Override to provide global shared props.
     *
     * @return  array<mixed>
     */
    protected function inertiaSharedProps(): array
    {
        return [];
    }
}
