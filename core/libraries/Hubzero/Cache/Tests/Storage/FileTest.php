<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * FileTest
 */
class FileTest extends AbstractCache
{
    /**
     * Test setup
     *
     * @return  void
     */
    public function setup(): void
    {
        parent::setup();

        $this->cache->setDefaultDriver('file');
    }

    /**
     * Clear out any leftover test data
     *
     * @return  void
     */
    public function tearDown(): void
    {
        $this->cache->clean();
    }
}
