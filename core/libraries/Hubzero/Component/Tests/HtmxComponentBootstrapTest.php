<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Component\Tests {

    use Hubzero\Component\AbstractComponent;
    use Hubzero\Component\HtmxComponentInterface;
    use Hubzero\Component\Traits\UsesHtmxComponent;
    use Hubzero\Htmx\HtmxService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class HtmxContainerStub implements \ArrayAccess
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

            if ($value instanceof \Closure) {
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

    class HtmxAppStub
    {
        private static array $state = [];

        public static function reset(): void
        {
            self::$state = [];
        }

        public static function get(string $key)
        {
            if (array_key_exists($key, self::$state)) {
                return self::$state[$key];
            }

            // This stub is aliased over the global App for the whole run, not
            // just this file, so fall through to the real container for keys it
            // does not define. Returning null unconditionally would break every
            // later test that resolves a service through the App facade.
            $app = \Hubzero\Facades\Facade::getApplication();

            return ($app && isset($app[$key])) ? $app[$key] : null;
        }

        public static function set(string $key, $value): void
        {
            self::$state[$key] = $value;
        }
    }

    class HtmxHeaderBagStub
    {
        public array $headers = [];

        public function set(string $name, string $value): void
        {
            $this->headers[$name] = $value;
        }

        public function get(string $name, string $default = ''): string
        {
            return $this->headers[$name] ?? $default;
        }
    }

    class HtmxResponseStub
    {
        public HtmxHeaderBagStub $headers;

        public function __construct()
        {
            $this->headers = new HtmxHeaderBagStub();
        }
    }

    if (!class_exists('\\App', false)) {
        class_alias(HtmxAppStub::class, 'App');
    }

    // 2.4-main: AbstractComponent (class-based component refactor) is not present.
    // Guard the helper components so this file loads; the test self-skips in setUp().
    if (class_exists(AbstractComponent::class)) {

    class EnabledHtmxComponent extends AbstractComponent implements HtmxComponentInterface
    {
        use UsesHtmxComponent;

        public bool $registered = false;

        public function registerHtmx(HtmxService $htmx): void
        {
            $this->registered = true;
            $htmx->state('marker', 'enabled');
        }

        protected function execute(): void
        {
        }
    }

    class DisabledHtmxComponent extends AbstractComponent implements HtmxComponentInterface
    {
        use UsesHtmxComponent;

        public bool $registered = false;

        public function htmxEnabled(): bool
        {
            return false;
        }

        public function registerHtmx(HtmxService $htmx): void
        {
            $this->registered = true;
        }

        protected function execute(): void
        {
        }
    }

    class TraitDefaultHtmxComponent extends AbstractComponent implements HtmxComponentInterface
    {
        use UsesHtmxComponent;

        protected function execute(): void
        {
        }
    }

    class TraitCustomHtmxComponent extends AbstractComponent implements HtmxComponentInterface
    {
        use UsesHtmxComponent;

        protected function htmxSecurityConfig(): array
        {
            return array(
                'allowEval' => true,
                'allowScriptTags' => true,
                'historyCacheSize' => 5
            );
        }

        protected function execute(): void
        {
        }
    }

    } // end class_exists(AbstractComponent) guard

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class HtmxComponentBootstrapTest extends TestCase
    {
        protected function setUp(): void
        {
            if (!class_exists(AbstractComponent::class)) {
                $this->markTestSkipped('Requires Hubzero\\Component\\AbstractComponent (class-based component refactor); not present on 2.4-main.');
            }
            if (is_a('\\App', HtmxAppStub::class, true)) {
                HtmxAppStub::reset();
            }

            \App::set('response', new HtmxResponseStub());
        }

        #[Test]
        public function bootRegistersHtmxForEnabledComponent(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new EnabledHtmxComponent();
            $component->boot();

            $this->assertTrue($component->registered);
            $this->assertTrue($container->has('htmx'));

            $service = $container->get('htmx');
            $this->assertInstanceOf(HtmxService::class, $service);
            $this->assertSame('enabled', $service->getState('marker'));
            $this->assertStringContainsString('HX-Request', \App::get('response')->headers->get('Vary'));
        }

        #[Test]
        public function bootSkipsHtmxSetupForDisabledComponent(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new DisabledHtmxComponent();
            $component->boot();

            $this->assertFalse($component->registered);
            $this->assertFalse($container->has('htmx'));
        }

        #[Test]
        public function traitRegistersDefaultHtmxSecurityState(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new TraitDefaultHtmxComponent();
            $component->boot();

            $service = $container->get('htmx');
            $htmxState = $service->getState('htmx');

            $this->assertIsArray($htmxState);
            $this->assertFalse($htmxState['security']['allowEval']);
            $this->assertFalse($htmxState['security']['allowScriptTags']);
            $this->assertSame(20, $htmxState['security']['historyCacheSize']);
        }

        #[Test]
        public function traitAllowsOverridingHtmxSecurityState(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new TraitCustomHtmxComponent();
            $component->boot();

            $service = $container->get('htmx');
            $htmxState = $service->getState('htmx');

            $this->assertIsArray($htmxState);
            $this->assertTrue($htmxState['security']['allowEval']);
            $this->assertTrue($htmxState['security']['allowScriptTags']);
            $this->assertSame(5, $htmxState['security']['historyCacheSize']);
        }
    }
}
