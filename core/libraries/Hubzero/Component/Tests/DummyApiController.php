<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\ApiController;

class DummyApiController extends ApiController
{
    public function inspectInertiaState(): bool
    {
        return $this->isInertiaRequest();
    }

    public function inspectHtmxState(): bool
    {
        return $this->isHtmxRequest();
    }
}
