<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
