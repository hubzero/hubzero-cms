<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests {

    use Hubzero\Htmx\HtmxService;
    use Hubzero\Inertia\InertiaService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

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
                ['HX-Request' => 'true', 'X-Inertia' => 'true'],
                ['htmx_debug' => '1', 'inertia_debug' => '1'],
                ['REQUEST_URI' => '/debug/parity'],
                'GET',
            ));

            $htmx = new HtmxService()->renderDebugPanel();
            $inertia = new InertiaService()->renderDebugPanel();

            $sharedMarkers = [
                'x-model="mode"',
                'x-model="timelineKind"',
                'x-model="autoscroll"',
                'x-on:click="clear()"',
                'panelData()',
                'window.__hzDebugPanel(',
            ];

            foreach ($sharedMarkers as $marker) {
                $this->assertStringContainsString($marker, $htmx);
                $this->assertStringContainsString($marker, $inertia);
            }
        }

        #[Test]
        public function debugPanelsUseProtocolSpecificSharedFactoryInit(): void
        {
            \App::set('request', new DebugParityRequestStub(
                ['HX-Request' => 'true', 'X-Inertia' => 'true'],
                ['htmx_debug' => '1', 'inertia_debug' => '1'],
                ['REQUEST_URI' => '/debug/parity'],
                'GET',
            ));

            $htmx = new HtmxService()->renderDebugPanel();
            $inertia = new InertiaService()->renderDebugPanel();

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
                [],
                ['htmx_debug' => '1', 'inertia_debug' => '1'],
                ['REQUEST_URI' => '/debug/parity'],
                'GET',
            ));

            $htmx = new HtmxService();
            $inertia = new InertiaService();

            $htmxParams = $htmx->preserveDebugParam(['task' => 'display'], 'debug mode[]', 'on');
            $inertiaParams = $inertia->preserveDebugParam(['task' => 'display'], 'debug mode[]', 'on');
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
