<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
