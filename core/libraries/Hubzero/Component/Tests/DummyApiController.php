<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
