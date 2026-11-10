<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Shibboleth\Tests;

use Plugins\Authentication\Shibboleth\Shibboleth;
use PHPUnit\Framework\TestCase;

/**
 * The plugin names a client's host by a reverse lookup with a short timeout,
 * and falls back to the address when the lookup fails.
 */
class HostByAddressTest extends TestCase
{
    /**
     * @return  void
     */
    public function testAFailedLookupGivesTheAddressBack(): void
    {
        $lookup = new \ReflectionMethod(Shibboleth::class, 'getHostByAddress');
        $lookup->setAccessible(true);

        // Nothing answers DNS on this loopback address
        $this->assertSame('192.0.2.1', $lookup->invoke(null, '192.0.2.1', '127.0.0.9', 1));
    }
}
