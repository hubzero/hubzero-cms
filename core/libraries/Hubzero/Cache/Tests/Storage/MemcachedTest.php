<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * MemcachedTest
 */
class MemcachedTest extends AbstractCache
{
    /**
     * Test setup
     *
     * @return  void
     */
    public function setUp(): void
    {
        if (!extension_loaded('memcached') || !class_exists('\Memcached')) {
            $this->markTestSkipped(
                'The Memcached extension is not available.'
            );
        }

        parent::setup();

        $this->cache->setDefaultDriver('memcached');
    }
}
