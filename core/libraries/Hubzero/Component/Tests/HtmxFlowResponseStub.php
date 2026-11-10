<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class HtmxFlowResponseStub
{
    public HtmxFlowHeaderBagStub $headers;
    public int $statusCode = 200;

    public function __construct()
    {
        $this->headers = new HtmxFlowHeaderBagStub();
    }

    public function setStatusCode(int $status): void
    {
        $this->statusCode = $status;
    }
}
