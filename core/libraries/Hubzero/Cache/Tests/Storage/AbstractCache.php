<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

use Hubzero\Test\Basic;
use Hubzero\Container\Container;
use Hubzero\Config\Registry;
use Hubzero\Cache\Manager;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * AbstractCacheTest
 */
abstract class AbstractCache extends Basic
{
    /**
     * Cache manager
     *
     * @var  object
     */
    protected $cache;

    /**
     * Test setup
     *
     * @return  void
     */
    public function setup(): void
    {
        $configurationFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'config.json';

        if (!is_file($configurationFile)) {
            throw new \Exception('Configuration file not found in "' . $configurationFile . '"');
        }

        $config = json_decode(file_get_contents($configurationFile), true);

        // Use Container instead of Application to avoid destructor
        // that flushes output buffers (causes PHPUnit risky test warning)
        $app = new Container();
        $app['config'] = new Registry();
        foreach ($config as $key => $value) {
            $app['config']->set($key, $value);
        }

        $this->cache = new Manager($app);
    }

    /**
     * @return  array
     */
    public static function dataProvider()
    {
        return [
            ['key1', 'value1', 1],
            ['key2', 'value2', 100],
            ['key3', 'value3', null],
            ['key4', true, null],
            ['key5', false, null],
            ['key6', array(), null],
            ['key7', new \DateTime('now', new \DateTimeZone('UTC')), null],
        ];
    }

    /**
     * Test if an item exists int he cache
     *
     * @param   string    $key
     * @param   mixed     $value
     * @param   int|null  $ttl
     * @return  void
     */
    #[DataProvider('dataProvider')]
    public function testHas($key, $value, $ttl)
    {
        $this->assertTrue($this->cache->forget($key));
        $this->assertFalse($this->cache->has($key));
        $this->assertTrue($this->cache->put($key, $value, $ttl));
        $this->assertTrue($this->cache->has($key));
    }

    /**
     * Test adding item to cache, returning FALSE if it already exists
     *
     * @param   string    $key
     * @param   mixed     $value
     * @param   int|null  $ttl
     * @return  void
     */
    #[DataProvider('dataProvider')]
    public function testAdd($key, $value, $ttl)
    {
        $this->cache->put($key, $value, $ttl);
        $this->assertFalse($this->cache->add($key, $value, $ttl));
    }

    /**
     * Test retrieving item from cache
     *
     * @param   string    $key
     * @param   mixed     $value
     * @param   int|null  $ttl
     * @return  void
     */
    #[DataProvider('dataProvider')]
    public function testGet($key, $value, $ttl)
    {
        $this->cache->put($key, $value, $ttl);
        $this->assertEquals($value, $this->cache->get($key));
    }

    /**
     * Test removing item from cache
     *
     * @param   string    $key
     * @param   mixed     $value
     * @param   int|null  $ttl
     * @return  void
     */
    #[DataProvider('dataProvider')]
    public function testForget($key, $value, $ttl)
    {
        $this->cache->put($key, $value, $ttl);
        $this->assertTrue($this->cache->forget($key));
        $this->assertFalse($this->cache->has($key));
    }

    /**
     * Test that cache items with valid TTL are accessible
     *
     * @return  void
     */
    public function testHasWithValidTtl()
    {
        $key = 'ttl_valid_test_' . uniqid();

        // Ensure key doesn't exist
        $this->cache->forget($key);

        // Store with 10 minute TTL
        $this->cache->put($key, 'test_value', 10);

        // Should exist immediately
        $this->assertTrue($this->cache->has($key), 'Cache item should exist with valid TTL');
        $this->assertEquals('test_value', $this->cache->get($key));

        // Clean up
        $this->cache->forget($key);
    }
}
