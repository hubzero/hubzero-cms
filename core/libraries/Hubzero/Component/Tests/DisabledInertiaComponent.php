<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\AbstractComponent;
use Hubzero\Component\InertiaComponentInterface;
use Hubzero\Component\Traits\UsesInertiaComponent;
use Hubzero\Inertia\InertiaService;

class DisabledInertiaComponent extends AbstractComponent implements InertiaComponentInterface
{
    use UsesInertiaComponent;

    public bool $registered = false;

    public function inertiaEnabled(): bool
    {
        return false;
    }

    public function registerInertia(InertiaService $inertia): void
    {
        $this->registered = true;
    }

    protected function execute(): void {}
}
