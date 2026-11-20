<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class HtmxResponseStub
{
    public HtmxHeaderBagStub $headers;

    public function __construct()
    {
        $this->headers = new HtmxHeaderBagStub();
    }
}
