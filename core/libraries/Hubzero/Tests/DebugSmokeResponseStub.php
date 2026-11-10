<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugSmokeResponseStub
{
    public DebugSmokeHeaderBagStub $headers;
    public int $statusCode = 200;

    public function __construct()
    {
        $this->headers = new DebugSmokeHeaderBagStub();
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}
