<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
