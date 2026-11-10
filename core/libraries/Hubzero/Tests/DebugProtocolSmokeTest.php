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
                ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/todo/?task=add'],
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
                ['REQUEST_URI' => '/games/?task=app'],
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
