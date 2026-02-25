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
    use Hubzero\Inertia\InertiaService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class InertiaFlowContainerStub implements ArrayAccess
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

    class InertiaFlowAppStub
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

    class InertiaFlowRequestStub
    {
        public array $headers = [];
        public string $method = 'GET';
        public array $server = [];
        public array $query = [];

        public function __construct(array $headers = [], string $method = 'GET', array $server = [], array $query = [])
        {
            $this->headers = $headers;
            $this->method = $method;
            $this->server = $server;
            $this->query = $query;
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

        public function getMethod(): string
        {
            return $this->method;
        }

        public function server(string $key, $default = null)
        {
            return $this->server[$key] ?? $default;
        }

        public function get(string $key, $default = null)
        {
            return $this->query[$key] ?? $default;
        }
    }

    class InertiaFlowHeaderBagStub
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

    class InertiaFlowResponseStub
    {
        public InertiaFlowHeaderBagStub $headers;
        public int $statusCode = 200;

        public function __construct()
        {
            $this->headers = new InertiaFlowHeaderBagStub();
        }

        public function setStatusCode(int $code): void
        {
            $this->statusCode = $code;
        }
    }

    if (!class_exists('\\App', false)) {
        class_alias(InertiaFlowAppStub::class, 'App');
    }

    class TestableInertiaFlowService extends InertiaService
    {
        public ?int $status = null;
        public array $headers = [];
        public ?string $body = null;
        public bool $terminated = false;

        protected function addVaryInertia(): void
        {
            $this->headers['Vary'] = 'X-Inertia';
        }

        protected function sendAndTerminate(int $status, array $headers = array(), ?string $body = null): void
        {
            if ($this->terminated) {
                return;
            }

            $this->terminated = true;
            $this->status = $status;

            foreach ($headers as $name => $value) {
                $this->headers[(string) $name] = (string) $value;
            }

            $this->addVaryInertia();
            $this->body = $body;

            $response = \Hubzero\Facades\App::get('response');
            if ($response) {
                $response->setStatusCode($status);
                foreach ($headers as $name => $value) {
                    $response->headers->set((string) $name, (string) $value);
                }
                $response->headers->set('Vary', 'X-Inertia');
            }
        }
    }

    class InertiaFlowController extends SiteController
    {
        public function __construct()
        {
        }

        public function renderTask(): ?array
        {
            return $this->inertiaRender('Test/Page', array('ok' => true));
        }

        public function mutateTask(): void
        {
            $this->inertiaRedirect('/target', 302);
        }
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class InertiaSiteControllerFlowTest extends TestCase
    {
        private function bootstrap(
            array $headers,
            string $method,
            string $uri,
            string $version = 'v1'
        ): TestableInertiaFlowService {
            if (is_a('\\App', InertiaFlowAppStub::class, true)) {
                InertiaFlowAppStub::reset();
            }

            $request = new InertiaFlowRequestStub($headers, $method, array('REQUEST_URI' => $uri));
            \Hubzero\Facades\App::set('request', $request);
            \Hubzero\Facades\App::set('response', new InertiaFlowResponseStub());

            $container = new InertiaFlowContainerStub();
            \Hubzero\Facades\App::set('app', $container);

            $service = new TestableInertiaFlowService();
            $service->version($version);
            $container->set('inertia', $service);

            return $service;
        }

        #[Test]
        public function jsonRenderFlowReturns200WithInertiaAndVaryHeaders(): void
        {
            $service = $this->bootstrap(array('X-Inertia' => 'true'), 'GET', '/component/render');
            $controller = new InertiaFlowController();

            $result = $controller->renderTask();

            $this->assertNull($result);
            $this->assertSame(200, $service->status);
            $this->assertSame('true', $service->headers['X-Inertia']);
            $this->assertSame('application/json; charset=utf-8', $service->headers['Content-Type']);
            $this->assertStringContainsString('X-Inertia', $service->headers['Vary'] ?? '');
            $this->assertNotNull($service->body);
        }

        #[Test]
        public function fullPageVisitFlowReturnsPageEnvelopeWithoutTerminatingResponse(): void
        {
            $service = $this->bootstrap(array(), 'GET', '/component/render');
            $controller = new InertiaFlowController();

            $result = $controller->renderTask();

            $this->assertIsArray($result);
            $this->assertSame('Test/Page', $result['component'] ?? null);
            $this->assertSame('/component/render', $result['url'] ?? null);
            $this->assertSame(200, \Hubzero\Facades\App::get('response')->statusCode);
            $this->assertNull($service->status);
            $this->assertFalse($service->terminated);
        }

        #[Test]
        public function mutationRedirectFlowNormalizes302To303(): void
        {
            $service = $this->bootstrap(array('X-Inertia' => 'true'), 'POST', '/component/mutate');
            $controller = new InertiaFlowController();

            $controller->mutateTask();

            $this->assertSame(303, $service->status);
            $this->assertSame('/target', $service->headers['Location']);
            $this->assertStringContainsString('X-Inertia', $service->headers['Vary'] ?? '');
        }

        #[Test]
        public function locationMismatchFlowReturns409WithInertiaLocationHeader(): void
        {
            $service = $this->bootstrap(
                array('X-Inertia' => 'true', 'X-Inertia-Version' => 'old'),
                'GET',
                '/component/render',
                'new'
            );
            $controller = new InertiaFlowController();

            $controller->renderTask();

            $this->assertSame(409, $service->status);
            $this->assertSame('true', $service->headers['X-Inertia']);
            $this->assertSame('/component/render', $service->headers['X-Inertia-Location']);
            $this->assertStringContainsString('X-Inertia', $service->headers['Vary'] ?? '');
        }
    }
}
