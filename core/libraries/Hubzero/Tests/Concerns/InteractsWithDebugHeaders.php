<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests\Concerns;

/**
 * Shared assertion helpers for debug/profile response headers.
 */
trait InteractsWithDebugHeaders
{
    /**
     * @param   array   $headers
     * @param   string  $header
     * @param   array   $contains
     * @return  void
     */
    protected function assertDebugHeaderContains(array $headers, string $header, array $contains): void
    {
        $this->assertArrayHasKey($header, $headers);
        $value = (string) $headers[$header];

        foreach ($contains as $fragment) {
            $this->assertStringContainsString((string) $fragment, $value);
        }
    }
}
