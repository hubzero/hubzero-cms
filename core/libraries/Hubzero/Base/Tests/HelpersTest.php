<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Container\Container;
use Hubzero\Config\Registry;
use Hubzero\Facades\Facade;

/**
 * Helpers test
 */
class HelpersTest extends Basic
{
    /**
     * The application container
     *
     * @var Container|null
     */
    protected static $app = null;

    /**
     * Set up the test environment
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (self::$app === null) {
            // Adopt the container the bootstrap installed rather than
            // replacing it, so other bindings survive for later test classes.
            self::$app = Facade::getApplication() ?: new Container();

            if (!isset(self::$app['config'])) {
                self::$app['config'] = function () {
                    return new Registry([
                        'application_env' => 'testing',
                    ]);
                };
            }

            Facade::setApplication(self::$app);
        }
    }

    /**
     * Test app()
     *
     * @return  void
     **/
    public function testApp()
    {
        $app = app();

        $this->assertInstanceOf(Container::class, $app);

        $config = app('config');

        $this->assertInstanceOf(Registry::class, $config);
    }

    /**
     * Test config()
     *
     * @return  void
     **/
    public function testConfig()
    {
        $config = config();

        $this->assertInstanceOf(Registry::class, $config);

        $val = config('application_env');

        $this->assertEquals('testing', $val);

        $val = config('bar', 'foo');

        $this->assertEquals('foo', $val);
    }

    /**
     * Test with()
     *
     * @return  void
     **/
    public function testWith()
    {
        $obj = with(new \stdClass());

        $this->assertInstanceOf('stdClass', $obj);

        $obj = with(new \Hubzero\Base\Obj(array('foo' => 'bar')));

        $this->assertInstanceOf('Hubzero\\Base\\Obj', $obj);
        $this->assertEquals($obj->get('foo'), 'bar');
    }

    /**
     * Test classExists()
     *
     * @return  void
     **/
    public function testClassExists()
    {
        $this->assertFalse(classExists('Hubzero\\Foo\\Bar'));

        $this->assertTrue(classExists('Hubzero\\Base\\Obj'));
    }
}
