<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests {

    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    if (!class_exists('\\App', false)) {
        class_alias(HtmxFlowAppStub::class, 'App');
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

            \App::set('request', new HtmxFlowRequestStub(['HX-Request' => 'true']));
            \App::set('response', new HtmxFlowResponseStub());
            \App::set('app', new HtmxFlowContainerStub());
        }

        #[Test]
        public function fragmentFlowReturnsHtml200AndClosesRequest(): void
        {
            $controller = new HtmxFlowController();

            ob_start();
            try {
                $controller->fragmentTask();
                $this->fail('The request was not stopped');
            } catch (\Hubzero\Test\Halted $e) {
                // stopped, as the application would
            }
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
            try {
                $controller->fragmentTask();
                $this->fail('The request was not stopped');
            } catch (\Hubzero\Test\Halted $e) {
                // stopped, as the application would
            }
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
            try {
                $controller->noContentTask();
                $this->fail('The request was not stopped');
            } catch (\Hubzero\Test\Halted $e) {
                // stopped, as the application would
            }
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
            try {
                $controller->stopPollingTask();
                $this->fail('The request was not stopped');
            } catch (\Hubzero\Test\Halted $e) {
                // stopped, as the application would
            }
            $output = ob_get_clean();

            $response = \App::get('response');
            $this->assertSame('', $output);
            $this->assertSame(286, $response->statusCode);
            $this->assertStringContainsString('HX-Request', (string) $response->headers->get('Vary', ''));
            $this->assertTrue((bool) \App::get('closed'));
        }
    }
}
