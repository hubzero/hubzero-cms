<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Database {
    if (!class_exists(__NAMESPACE__ . '\\Config', false)) {
        class Config
        {
            public static array $values = [];

            public static function get($key, $default = null)
            {
                return self::$values[$key] ?? $default;
            }
        }
    }

    if (!class_exists('\\Config', false)) {
        class_alias(__NAMESPACE__ . '\\Config', 'Config');
    }
}

namespace Hubzero\Database\Tests {

    use Hubzero\Database\Config;
    use Hubzero\Database\DatabaseServiceProvider;
    use Hubzero\Database\Driver;
    use Hubzero\Database\Drivers\Mock\MockDriver;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class TestContainer implements \ArrayAccess
    {
        private array $values = [];
        private array $frozen = [];

        public function offsetExists(mixed $offset): bool
        {
            return array_key_exists($offset, $this->values);
        }

        public function offsetGet(mixed $offset): mixed
        {
            $value = $this->values[$offset];
            if ($value instanceof \Closure) {
                if (!array_key_exists($offset, $this->frozen)) {
                    $this->frozen[$offset] = $value($this);
                }
                return $this->frozen[$offset];
            }

            return $value;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            $this->values[$offset] = $value;
        }

        public function offsetUnset(mixed $offset): void
        {
            unset($this->values[$offset], $this->frozen[$offset]);
        }
    }

    class DatabaseServiceProviderTest extends TestCase
    {
        protected function setUp(): void
        {
            Config::$values = [];

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
            Config::$values = [
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
