<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\AbstractComponent;
use Hubzero\Component\HtmxComponentInterface;
use Hubzero\Component\Traits\UsesHtmxComponent;
use Hubzero\Htmx\HtmxService;

class EnabledHtmxComponent extends AbstractComponent implements HtmxComponentInterface
{
    use UsesHtmxComponent;

    public bool $registered = false;

    public function registerHtmx(HtmxService $htmx): void
    {
        $this->registered = true;
        $htmx->state('marker', 'enabled');
    }

    protected function execute(): void {}
}
