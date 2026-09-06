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
    use Hubzero\Component\SiteController;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class HtmxFlowContainerStub implements ArrayAccess
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

    class HtmxFlowAppStub
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

        public static function close(): void
        {
            self::$state['closed'] = true;
        }

        public static function redirect(string $url): void
        {
            self::$state['redirect_to'] = $url;
        }
    }

    class HtmxFlowRequestStub
    {
        public array $headers = [];

        public function __construct(array $headers = [])
        {
            $this->headers = $headers;
        }

        public function header(string $name, $default = '')
        {
            foreach ($this->headers as $key => $value) {
                if (strtolower((string) $key) === strtolower($name)) {
                    return $value;
                }
            }

            return $default;
        }
    }

    class HtmxFlowHeaderBagStub
    {
        public array $headers = [];

        public function set(string $name, string $value): void
        {
            $this->headers[$name] = $value;
        }

        public function get(string $name, $default = '')
        {
            return $this->headers[$name] ?? $default;
        }
    }

    class HtmxFlowResponseStub
    {
        public HtmxFlowHeaderBagStub $headers;
        public int $statusCode = 200;

        public function __construct()
        {
            $this->headers = new HtmxFlowHeaderBagStub();
        }

        public function setStatusCode(int $status): void
        {
            $this->statusCode = $status;
        }
    }

    if (!class_exists('\\App', false)) {
        class_alias(HtmxFlowAppStub::class, 'App');
    }

    class HtmxFlowController extends SiteController
    {
        public function __construct()
        {
        }

        public function fragmentTask(): void
        {
            $this->htmxVaryOnRequest();
            $this->htmxFragment('<li>ok</li>');
        }

        public function noContentTask(): void
        {
            $this->htmxVaryOnRequest();
            $this->htmxNoContent();
        }

        public function stopPollingTask(): void
        {
            $this->htmxVaryOnRequest();
            $this->htmxStopPolling();
        }
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class HtmxSiteControllerFlowTest extends TestCase
    {
        protected function setUp(): void
        {
            if (is_a('\\App', HtmxFlowAppStub::class, true)) {
                HtmxFlowAppStub::reset();
            }

            \App::set('request', new HtmxFlowRequestStub(array('HX-Request' => 'true')));
            \App::set('response', new HtmxFlowResponseStub());
            \App::set('app', new HtmxFlowContainerStub());
        }

        #[Test]
        public function fragmentFlowReturnsHtml200AndClosesRequest(): void
        {
            $controller = new HtmxFlowController();

            ob_start();
            $controller->fragmentTask();
            $output = ob_get_clean();

            $response = \App::get('response');
            $this->assertSame('<li>ok</li>', $output);
            $this->assertSame(200, $response->statusCode);
            $this->assertStringContainsString('HX-Request', (string) $response->headers->get('Vary', ''));
            $this->assertTrue((bool) \App::get('closed'));
        }

        #[Test]
        public function fragmentFlowEmitsOnlyFragmentMarkupAndNotLayoutScaffold(): void
        {
            $controller = new HtmxFlowController();

            ob_start();
            $controller->fragmentTask();
            $output = (string) ob_get_clean();

            $this->assertSame('<li>ok</li>', $output);
            $this->assertStringNotContainsString('<html', strtolower($output));
            $this->assertStringNotContainsString('<body', strtolower($output));
            $this->assertStringNotContainsString('</html>', strtolower($output));
        }

        #[Test]
        public function noContentFlowReturns204AndClosesRequest(): void
        {
            $controller = new HtmxFlowController();

            ob_start();
            $controller->noContentTask();
            $output = ob_get_clean();

            $response = \App::get('response');
            $this->assertSame('', $output);
            $this->assertSame(204, $response->statusCode);
            $this->assertStringContainsString('HX-Request', (string) $response->headers->get('Vary', ''));
            $this->assertTrue((bool) \App::get('closed'));
        }

        #[Test]
        public function stopPollingFlowReturns286AndClosesRequest(): void
        {
            $controller = new HtmxFlowController();

            ob_start();
            $controller->stopPollingTask();
            $output = ob_get_clean();

            $response = \App::get('response');
            $this->assertSame('', $output);
            $this->assertSame(286, $response->statusCode);
            $this->assertStringContainsString('HX-Request', (string) $response->headers->get('Vary', ''));
            $this->assertTrue((bool) \App::get('closed'));
        }
    }
}
