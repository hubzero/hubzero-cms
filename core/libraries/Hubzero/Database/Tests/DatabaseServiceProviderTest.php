<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests {

    use Hubzero\Database\DatabaseServiceProvider;
    use Hubzero\Database\Driver;
    use Hubzero\Database\Drivers\Mock\MockDriver;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    // The provider reads its settings through the global Config facade
    if (!class_exists('\\Config', false)) {
        class_alias(ConfigStub::class, 'Config');
    }

    class DatabaseServiceProviderTest extends TestCase
    {
        protected function setUp(): void
        {
            ConfigStub::$values = [];

            $property = new \ReflectionProperty(Driver::class, 'instances');
            $property->setAccessible(true);
            $property->setValue(null, []);
        }

        #[Test]
        public function registerAddsDbServiceToContainer(): void
        {
            $app = new TestContainer();
            $provider = new DatabaseServiceProvider($app);

            $provider->register();

            $this->assertTrue(isset($app['db']));
        }

        #[Test]
        public function dbServiceBuildsDriverUsingDbtypeConfiguration(): void
        {
            ConfigStub::$values = [
                'dbtype' => 'mock',
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'user' => 'tester',
                'password' => 'secret',
                'db' => 'hubzero_test',
                'dbprefix' => 'hz_',
            ];

            $app = new TestContainer();
            $provider = new DatabaseServiceProvider($app);
            $provider->register();

            $driver = $app['db'];

            $this->assertInstanceOf(MockDriver::class, $driver);
            $this->assertSame($driver, $app['db']);
        }
    }
}
