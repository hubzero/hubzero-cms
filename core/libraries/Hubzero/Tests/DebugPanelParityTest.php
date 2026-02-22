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

    class DebugParityRequestStub
    {
        private array $headers = [];
        private string $method = 'GET';
        private array $server = [];
        private array $query = [];

        public function __construct(array $headers = [], array $query = [], array $server = [], string $method = 'GET')
        {
            foreach ($headers as $key => $value) {
                $this->headers[strtolower((string) $key)] = (string) $value;
            }
            $this->query = $query;
            $this->server = $server;
            $this->method = strtoupper($method);
        }

        public function header(string $name, string $default = ''): string
        {
            return $this->headers[strtolower($name)] ?? $default;
        }

        public function get(string $name, $default = null)
        {
            return $this->query[$name] ?? $default;
        }

        public function server(string $name, $default = null)
        {
            return $this->server[$name] ?? $default;
        }

        public function getMethod(): string
        {
            return $this->method;
        }
    }

    class DebugParityHeaderBagStub
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

    class DebugParityResponseStub
    {
        public DebugParityHeaderBagStub $headers;
        public int $statusCode = 200;

        public function __construct()
        {
            $this->headers = new DebugParityHeaderBagStub();
        }

        public function setStatusCode(int $status): void
        {
            $this->statusCode = $status;
        }
    }

    class DebugParityAppStub
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
        class_alias(DebugParityAppStub::class, 'App');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class DebugPanelParityTest extends TestCase
    {
        protected function setUp(): void
        {
            if (is_a('\\App', DebugParityAppStub::class, true)) {
                DebugParityAppStub::reset();
            }

            \App::set('response', new DebugParityResponseStub());
        }

        #[Test]
        public function htmxAndInertiaDebugPanelsExposeSameCoreControls(): void
        {
            \App::set('request', new DebugParityRequestStub(
                array('HX-Request' => 'true', 'X-Inertia' => 'true'),
                array('htmx_debug' => '1', 'inertia_debug' => '1'),
                array('REQUEST_URI' => '/debug/parity'),
                'GET'
            ));

            $htmx = (new HtmxService())->renderDebugPanel();
            $inertia = (new InertiaService())->renderDebugPanel();

            $sharedMarkers = array(
                'x-model="mode"',
                'x-model="timelineKind"',
                'x-model="autoscroll"',
                'x-on:click="clear()"',
                'panelData()',
                'window.__hzDebugPanel(',
            );

            foreach ($sharedMarkers as $marker) {
                $this->assertStringContainsString($marker, $htmx);
                $this->assertStringContainsString($marker, $inertia);
            }
        }

        #[Test]
        public function debugPanelsUseProtocolSpecificSharedFactoryInit(): void
        {
            \App::set('request', new DebugParityRequestStub(
                array('HX-Request' => 'true', 'X-Inertia' => 'true'),
                array('htmx_debug' => '1', 'inertia_debug' => '1'),
                array('REQUEST_URI' => '/debug/parity'),
                'GET'
            ));

            $htmx = (new HtmxService())->renderDebugPanel();
            $inertia = (new InertiaService())->renderDebugPanel();

            $this->assertStringContainsString("__hzDebugPanel('htmx'", $htmx);
            $this->assertStringContainsString("__hzDebugPanel('inertia'", $inertia);
        }

        #[Test]
        public function sharedTimelineHelperDefinesCanonicalEventShapeKeys(): void
        {
            $timelineJs = (string) file_get_contents(__DIR__ . '/../../../assets/js/hubzero-debug-timeline.js');
            $this->assertNotSame('', $timelineJs);
            $this->assertStringContainsString('ts:', $timelineJs);
            $this->assertStringContainsString('action:', $timelineJs);
            $this->assertStringContainsString('request:', $timelineJs);
            $this->assertStringContainsString('profile:', $timelineJs);
            $this->assertStringContainsString('snapshot:', $timelineJs);
        }

        #[Test]
        public function htmxAndInertiaShareDebugLifecycleHelpersForParamAndHiddenInput(): void
        {
            \App::set('request', new DebugParityRequestStub(
                array(),
                array('htmx_debug' => '1', 'inertia_debug' => '1'),
                array('REQUEST_URI' => '/debug/parity'),
                'GET'
            ));

            $htmx = new HtmxService();
            $inertia = new InertiaService();

            $htmxParams = $htmx->preserveDebugParam(array('task' => 'display'), 'debug mode[]', 'on');
            $inertiaParams = $inertia->preserveDebugParam(array('task' => 'display'), 'debug mode[]', 'on');
            $htmxInput = $htmx->renderDebugHiddenInput('debug mode[]', 'on');
            $inertiaInput = $inertia->renderDebugHiddenInput('debug mode[]', 'on');

            $this->assertSame('on', $htmxParams['debugmode[]'] ?? null);
            $this->assertSame('on', $inertiaParams['debugmode[]'] ?? null);
            $this->assertStringContainsString('name="debugmode[]"', $htmxInput);
            $this->assertStringContainsString('name="debugmode[]"', $inertiaInput);
            $this->assertStringContainsString('value="on"', $htmxInput);
            $this->assertStringContainsString('value="on"', $inertiaInput);
        }
    }
}
