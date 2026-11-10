<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
