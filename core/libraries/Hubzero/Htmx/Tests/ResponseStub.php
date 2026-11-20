<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx\Tests;

class ResponseStub
{
    public HeaderBagStub $headers;
    public int $statusCode = 200;
    public string $content = '';

    public function __construct()
    {
        $this->headers = new HeaderBagStub();
    }

    public function setStatusCode(int $status): void
    {
        $this->statusCode = $status;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
