<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests\Concerns;

/**
 * Shared assertion helpers for debug/profile response headers.
 *
 * @phpstan-require-extends \PHPUnit\Framework\TestCase
 */
trait InteractsWithDebugHeaders
{
    /**
     * @param   array<mixed>   $headers
     * @param   string  $header
     * @param   array<mixed>   $contains
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
