<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
