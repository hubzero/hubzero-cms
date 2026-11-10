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
        class_alias(InertiaFlowAppStub::class, 'App');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class InertiaSiteControllerFlowTest extends TestCase
    {
        private function bootstrap(
            array $headers,
            string $method,
            string $uri,
            string $version = 'v1',
        ): TestableInertiaFlowService {
            if (is_a('\\App', InertiaFlowAppStub::class, true)) {
                InertiaFlowAppStub::reset();
            }

            $request = new InertiaFlowRequestStub($headers, $method, ['REQUEST_URI' => $uri]);
            \App::set('request', $request);
            \App::set('response', new InertiaFlowResponseStub());

            $container = new InertiaFlowContainerStub();
            \App::set('app', $container);

            $service = new TestableInertiaFlowService();
            $service->version($version);
            $container->set('inertia', $service);

            return $service;
        }

        #[Test]
        public function jsonRenderFlowReturns200WithInertiaAndVaryHeaders(): void
        {
            $service = $this->bootstrap(['X-Inertia' => 'true'], 'GET', '/component/render');
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
            $service = $this->bootstrap([], 'GET', '/component/render');
            $controller = new InertiaFlowController();

            $result = $controller->renderTask();

            $this->assertIsArray($result);
            $this->assertSame('Test/Page', $result['component'] ?? null);
            $this->assertSame('/component/render', $result['url'] ?? null);
            $this->assertSame(200, \App::get('response')->statusCode);
            $this->assertNull($service->status);
            $this->assertFalse($service->terminated);
        }

        #[Test]
        public function mutationRedirectFlowNormalizes302To303(): void
        {
            $service = $this->bootstrap(['X-Inertia' => 'true'], 'POST', '/component/mutate');
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
                ['X-Inertia' => 'true', 'X-Inertia-Version' => 'old'],
                'GET',
                '/component/render',
                'new',
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
