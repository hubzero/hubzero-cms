<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * MemoryTest
 */
class MemoryTest extends AbstractCache
{
    /**
     * Test setup
     *
     * @return  void
     */
    public function setUp(): void
    {
        parent::setup();

        $this->cache->setDefaultDriver('memory');
    }
}
