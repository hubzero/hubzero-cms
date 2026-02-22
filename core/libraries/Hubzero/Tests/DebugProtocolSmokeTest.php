<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Tests {

    use Hubzero\Htmx\HtmxService;
    use Hubzero\Inertia\InertiaService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class DebugSmokeRequestStub
    {
        private array $headers = [];
        private string $method;
        private array $query;
        private array $server;

        public function __construct(array $headers = [], string $method = 'GET', array $query = [], array $server = [])
        {
            foreach ($headers as $key => $value) {
                $this->headers[strtolower((string) $key)] = (string) $value;
            }
            $this->method = strtoupper($method);
            $this->query = $query;
            $this->server = $server;
        }

        public function header(string $key, string $default = ''): string
        {
            return $this->headers[strtolower($key)] ?? $default;
        }

        public function getMethod(): string
        {
            return $this->method;
        }

        public function get(string $key, $default = null)
        {
            return $this->query[$key] ?? $default;
        }

        public function server(string $key, $default = null)
        {
            return $this->server[$key] ?? $default;
        }
    }

    class DebugSmokeHeaderBagStub
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

    class DebugSmokeResponseStub
    {
        public DebugSmokeHeaderBagStub $headers;
        public int $statusCode = 200;

        public function __construct()
        {
            $this->headers = new DebugSmokeHeaderBagStub();
        }

        public function setStatusCode(int $statusCode): void
        {
            $this->statusCode = $statusCode;
        }
    }

    class DebugSmokeConfigStub
    {
        private array $values;

        public function __construct(array $values = [])
        {
            $this->values = $values;
        }

        public function get(string $key, $default = null)
        {
            return $this->values[$key] ?? $default;
        }
    }

    class DebugSmokeAppStub
    {
        private static array $state = [];

        public static function reset(): void
        {
            self::$state = [];
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
        class_alias(DebugSmokeAppStub::class, 'App');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class DebugProtocolSmokeTest extends TestCase
    {
        protected function setUp(): void
        {
            if (is_a('\\App', DebugSmokeAppStub::class, true)) {
                DebugSmokeAppStub::reset();
            }

            \App::set('response', new DebugSmokeResponseStub());
            \App::set('config', new DebugSmokeConfigStub(['debug' => false]));
        }

        #[Test]
        public function htmxDebugHeadersAndPanelMarkersArePresent(): void
        {
            \App::set('request', new DebugSmokeRequestStub(
                ['HX-Request' => 'true'],
                'POST',
                ['htmx_debug' => '1', 'option' => 'com_todo', 'task' => 'add'],
                ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/todo/?task=add']
            ));

            $service = new HtmxService();
            $service->debugContext('branch', 'smoke');
            $service->trigger('todo:added', ['ok' => true]);
            $service->profileStart('smoke.htmx');
            usleep(1000);
            $service->profileStop('smoke.htmx');
            $service->emitDebugHeader();
            $service->emitProfileHeader();

            $headers = \App::get('response')->headers->headers;
            $this->assertArrayHasKey('X-Hubzero-Htmx-Debug', $headers);
            $this->assertArrayHasKey('X-Hubzero-Htmx-Profile', $headers);
            $this->assertStringContainsString('"branch":"smoke"', $headers['X-Hubzero-Htmx-Debug']);
            $this->assertStringContainsString('smoke.htmx', $headers['X-Hubzero-Htmx-Profile']);

            $panel = $service->renderDebugPanel();
            $this->assertStringContainsString("window.__hzDebugPanel('htmx'", $panel);
            $this->assertStringContainsString('x-model="timelineKind"', $panel);
        }

        #[Test]
        public function inertiaDebugHeadersAndPanelMarkersArePresent(): void
        {
            \App::set('request', new DebugSmokeRequestStub(
                ['X-Inertia' => 'true'],
                'GET',
                ['inertia_debug' => '1', 'option' => 'com_games', 'task' => 'app'],
                ['REQUEST_URI' => '/games/?task=app']
            ));

            $service = new InertiaService();
            $service->debugContext('branch', 'smoke');
            $service->profileStart('smoke.inertia');
            usleep(1000);
            $service->profileStop('smoke.inertia');
            $service->emitDebugHeader();
            $service->emitProfileHeader();

            $headers = \App::get('response')->headers->headers;
            $this->assertArrayHasKey('X-Hubzero-Inertia-Debug', $headers);
            $this->assertArrayHasKey('X-Hubzero-Inertia-Profile', $headers);
            $this->assertStringContainsString('"branch":"smoke"', $headers['X-Hubzero-Inertia-Debug']);
            $this->assertStringContainsString('smoke.inertia', $headers['X-Hubzero-Inertia-Profile']);

            $panel = $service->renderDebugPanel();
            $this->assertStringContainsString("window.__hzDebugPanel('inertia'", $panel);
            $this->assertStringContainsString('x-model="timelineKind"', $panel);
        }
    }
}
