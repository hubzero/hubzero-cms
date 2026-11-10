<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class InertiaFlowResponseStub
{
    public InertiaFlowHeaderBagStub $headers;
    public int $statusCode = 200;

    public function __construct()
    {
        $this->headers = new InertiaFlowHeaderBagStub();
    }

    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }
}
