<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Component\Tests {

    use ArrayAccess;
    use Closure;
    use Hubzero\Component\AbstractComponent;
    use Hubzero\Component\InertiaComponentInterface;
    use Hubzero\Component\Traits\UsesInertiaComponent;
    use Hubzero\Inertia\InertiaService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class ContainerStub implements ArrayAccess
    {
        private array $values = [];
        private array $resolved = [];

        public function has(string $key): bool
        {
            return array_key_exists($key, $this->values);
        }

        public function get(string $key)
        {
            return $this->offsetGet($key);
        }

        public function set(string $key, $value): void
        {
            $this->offsetSet($key, $value);
        }

        public function register($provider): void
        {
            $provider->register();
        }

        public function offsetExists(mixed $offset): bool
        {
            return array_key_exists((string) $offset, $this->values);
        }

        public function offsetGet(mixed $offset): mixed
        {
            $key = (string) $offset;
            $value = $this->values[$key] ?? null;

            if ($value instanceof Closure) {
                if (!array_key_exists($key, $this->resolved)) {
                    $this->resolved[$key] = $value($this);
                }

                return $this->resolved[$key];
            }

            return $value;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            $key = (string) $offset;
            $this->values[$key] = $value;
            unset($this->resolved[$key]);
        }

        public function offsetUnset(mixed $offset): void
        {
            $key = (string) $offset;
            unset($this->values[$key], $this->resolved[$key]);
        }
    }

    class AppStub
    {
        private static array $state = [];

        public static function reset(): void
        {
            self::$state = [];
        }

        public static function has(string $key): bool
        {
            return array_key_exists($key, self::$state);
        }

        public static function get(string $key)
        {
            return self::$state[$key] ?? null;
        }

        public static function set(string $key, $value): void
        {
            self::$state[$key] = $value;
        }
    }

    if (!class_exists('\\App', false)) {
        class_alias(AppStub::class, 'App');
    }

    class EnabledInertiaComponent extends AbstractComponent implements InertiaComponentInterface
    {
        use UsesInertiaComponent {
            registerInertia as private registerInertiaDefaults;
        }

        public bool $registered = false;

        public function registerInertia(InertiaService $inertia): void
        {
            $this->registerInertiaDefaults($inertia);
            $this->registered = true;
            $inertia->share('marker', 'enabled');
        }

        protected function execute(): void
        {
        }
    }

    class DisabledInertiaComponent extends AbstractComponent implements InertiaComponentInterface
    {
        use UsesInertiaComponent;

        public bool $registered = false;

        public function inertiaEnabled(): bool
        {
            return false;
        }

        public function registerInertia(InertiaService $inertia): void
        {
            $this->registered = true;
        }

        protected function execute(): void
        {
        }
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class InertiaComponentBootstrapTest extends TestCase
    {
        protected function setUp(): void
        {
            if (is_a('\\App', AppStub::class, true)) {
                AppStub::reset();
            }
        }

        #[Test]
        public function bootRegistersAndConfiguresInertiaForEnabledComponent(): void
        {
            $container = new ContainerStub();
            \Hubzero\Facades\App::set('app', $container);

            $component = new EnabledInertiaComponent();
            $component->boot();

            $this->assertTrue($component->registered);
            $this->assertTrue($container->has('inertia'));

            $service = $container->get('inertia');
            $this->assertInstanceOf(InertiaService::class, $service);
            $this->assertSame('enabled', $service->getShared('marker'));
        }

        #[Test]
        public function bootSkipsInertiaSetupForDisabledComponent(): void
        {
            $container = new ContainerStub();
            \Hubzero\Facades\App::set('app', $container);

            $component = new DisabledInertiaComponent();
            $component->boot();

            $this->assertFalse($component->registered);
            $this->assertFalse($container->has('inertia'));
        }
    }
}
