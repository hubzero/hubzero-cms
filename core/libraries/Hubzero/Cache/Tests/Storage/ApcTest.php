<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * ApcTest
 *
 * Runs only where APCu is enabled. From the command line that takes
 * `php -d apc.enable_cli=1 vendor/bin/phpunit ...`.
 */
class ApcTest extends AbstractCache
{
    /**
     * Test setup
     *
     * @return  void
     */
    public function setUp(): void
    {
        if (!extension_loaded('apcu') || !apcu_enabled()) {
            $this->markTestSkipped('APCu is not enabled');
        }

        parent::setup();

        $this->cache->setDefaultDriver('apc');
    }
}
