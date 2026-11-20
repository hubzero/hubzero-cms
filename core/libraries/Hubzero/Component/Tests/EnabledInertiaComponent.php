<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\AbstractComponent;
use Hubzero\Component\InertiaComponentInterface;
use Hubzero\Component\Traits\UsesInertiaComponent;
use Hubzero\Inertia\InertiaService;

class EnabledInertiaComponent extends AbstractComponent implements InertiaComponentInterface
{
    use UsesInertiaComponent {
        registerInertia as private registerInertiaDefaults;
    }

    public bool $registered = false;

    public function registerInertia(InertiaService $inertia): void
    {
        $this->registerInertiaDefaults($inertia);
        $this->registered = true;
        $inertia->share('marker', 'enabled');
    }

    protected function execute(): void {}
}
