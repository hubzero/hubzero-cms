<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Htmx\Tests {

    use Hubzero\Htmx\Htmx;
    use Hubzero\Htmx\HtmxService;
    use Hubzero\Htmx\HtmxServiceProvider;
    use Hubzero\Htmx\Tests\Concerns\InteractsWithHtmxResponses;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class HeaderBagStub
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

    class ResponseStub
    {
        public HeaderBagStub $headers;
        public int $statusCode = 200;
        public string $content = '';

        public function __construct()
        {
            $this->headers = new HeaderBagStub();
        }

        public function setStatusCode(int $status): void
        {
            $this->statusCode = $status;
        }

        public function setContent(string $content): void
        {
            $this->content = $content;
        }
    }

    class RequestStub
    {
        private array $headers = [];
        private array $server = [];
        private array $query = [];

        public function __construct(array $headers = [], array $server = [], array $query = [])
        {
            foreach ($headers as $key => $value) {
                $this->headers[strtolower((string) $key)] = (string) $value;
            }
            $this->server = $server;
            $this->query = $query;
        }

        public function header(string $key, string $default = ''): string
        {
            return $this->headers[strtolower($key)] ?? $default;
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

    class ContainerStub implements \ArrayAccess
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

    class AppStub
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

        public static function redirect(string $url): void
        {
            self::$state['redirect_to'] = $url;
        }

        public static function close(): void
        {
            self::$state['closed'] = true;
        }
    }

    class RouteStub
    {
        public static function url(string $url, bool $xhtml = true): string
        {
            return '/' . ltrim($url, '/');
        }
    }

    class ConfigStub
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

    if (!class_exists('\\App', false)) {
        class_alias(AppStub::class, 'App');
    }

    if (!class_exists('\\Route', false)) {
        class_alias(RouteStub::class, 'Route');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class HtmxTest extends TestCase
    {
        use InteractsWithHtmxResponses;

        protected function setUp(): void
        {
            if (is_a('\\App', AppStub::class, true)) {
                AppStub::reset();
            }

            \App::set('request', new RequestStub());
            \App::set('response', new ResponseStub());
        }

        #[Test]
        public function serviceDetectsHtmxHeaders(): void
        {
            \App::set('request', new RequestStub([
                'HX-Request' => 'true',
                'HX-Boosted' => 'TRUE',
                'HX-History-Restore-Request' => 'true'
            ]));

            $service = new HtmxService();

            $this->assertTrue($service->isHtmxRequest());
            $this->assertTrue($service->isHtmxBoosted());
            $this->assertTrue($service->isHtmxHistoryRestoreRequest());
        }

        #[Test]
        public function serviceWritesStateAndRendersStateNode(): void
        {
            $service = new HtmxService();
            $service->state('foo', 'bar');
            $service->state(['quoted' => 'A "quote"']);

            $html = $service->renderStateNode('state-node');

            $this->assertSame('bar', $service->getState('foo'));
            $this->assertStringContainsString('<script type="application/json" id="state-node">', $html);
            $this->assertStringContainsString('\\"quote\\"', $html);
        }

        #[Test]
        public function serviceTriggerSetsHxTriggerHeader(): void
        {
            $service = new HtmxService();
            $service->trigger('saved', ['ok' => true]);

            $response = \App::get('response');
            $this->assertArrayHasKey('HX-Trigger', $response->headers->headers);
            $this->assertStringContainsString('saved', $response->headers->headers['HX-Trigger']);
        }

        #[Test]
        public function redirectUsesHxRedirectForHtmxRequests(): void
        {
            \App::set('request', new RequestStub(['HX-Request' => 'true']));
            $service = new HtmxService();

            $service->redirect('/todo');

            $response = \App::get('response');
            $this->assertHtmxRedirect($response, '/todo');
            $this->assertNull(\App::get('redirect_to'));
        }

        #[Test]
        public function redirectFallsBackToStandardRedirectForNonHtmxRequests(): void
        {
            $service = new HtmxService();
            $service->redirect('/classic');

            $this->assertSame('/classic', \App::get('redirect_to'));
        }

        #[Test]
        public function serviceProviderRegistersHtmxService(): void
        {
            $container = new ContainerStub();
            $provider = new HtmxServiceProvider($container);
            $provider->register();

            $this->assertTrue($container->has('htmx'));
            $this->assertInstanceOf(HtmxService::class, $container->get('htmx'));
        }

        #[Test]
        public function facadeDelegatesToContainerBoundService(): void
        {
            $container = new ContainerStub();
            $container->set('htmx', new HtmxService());
            \App::set('app', $container);

            Htmx::state('alpha', 1);
            $this->assertSame(1, Htmx::getState('alpha'));
        }

        #[Test]
        public function facadeRouteHelpersBuildActionUrls(): void
        {
            $container = new ContainerStub();
            $container->set('htmx', new HtmxService());
            \App::set('app', $container);

            $url = Htmx::actionUrl('todo', 'toggle', ['id' => 7]);

            $this->assertStringContainsString('/index.php?', $url);
            $this->assertStringContainsString('option=com_todo', $url);
            $this->assertStringContainsString('task=toggle', $url);
            $this->assertStringContainsString('id=7', $url);
        }

        #[Test]
        public function helperAssertionsValidateFragmentHtml(): void
        {
            $fragment = '<div id="todo-panel"><p>Hi</p></div>';
            $this->assertFragmentResponse($fragment);
            $this->assertNoLayoutInFragment($fragment);
        }

        #[Test]
        public function debugHeaderCanBeEnabledViaQueryFlag(): void
        {
            \App::set('request', new RequestStub(
                ['HX-Request' => 'true'],
                ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/todo/?task=add'],
                ['htmx_debug' => '1', 'option' => 'com_todo', 'task' => 'add']
            ));

            $service = new HtmxService();
            $service->debugContext('branch', 'fragment');
            $service->trigger('saved', ['ok' => true]);

            $response = \App::get('response');
            $this->assertDebugHeaderContains($response->headers->headers, 'X-Hubzero-Htmx-Debug', array(
                '"outgoing"',
                'HX-Trigger',
                '"branch":"fragment"',
                '"task":"add"'
            ));
        }

        #[Test]
        public function profilerEmitsHeaderWhenDebugEnabled(): void
        {
            \App::set('request', new RequestStub([], [], ['htmx_debug' => '1']));
            \App::set('config', new ConfigStub(['debug' => false]));
            $service = new HtmxService();

            $service->profileStart('fragment.render');
            usleep(1000);
            $service->profileStop('fragment.render');
            $service->emitProfileHeader();

            $response = \App::get('response');
            $this->assertDebugHeaderContains(
                $response->headers->headers,
                'X-Hubzero-Htmx-Profile',
                array('fragment.render')
            );
        }

        #[Test]
        public function preserveDebugParamAndHiddenInputAreGeneratedWhenEnabled(): void
        {
            \App::set('request', new RequestStub([], [], ['htmx_debug' => '1']));
            $service = new HtmxService();

            $params = $service->preserveDebugParam(['task' => 'display']);
            $input = $service->renderDebugHiddenInput();

            $this->assertSame('1', $params['htmx_debug']);
            $this->assertStringContainsString('type="hidden"', $input);
            $this->assertStringContainsString('name="htmx_debug"', $input);
        }

        #[Test]
        public function debugPanelMarkupRendersWhenEnabled(): void
        {
            \App::set('request', new RequestStub([], [], ['htmx_debug' => '1']));
            $service = new HtmxService();
            $service->debugContext('branch', 'fragment');

            $html = $service->renderDebugPanel();

            $this->assertDebugPanelMarkup($html, 'htmx', 'hz-htmx-debug-panel', 'HTMX Debug');
        }

        #[Test]
        public function fragmentWritesHtmlAndClosesApplication(): void
        {
            $container = new ContainerStub();
            $container->set('htmx', new HtmxService());
            \App::set('app', $container);

            $payload = '<div id="fragment-ok">ok</div>';
            ob_start();
            Htmx::fragment($payload);
            $output = (string) ob_get_clean();

            $this->assertSame($payload, $output);
            $this->assertTrue((bool) \App::get('closed'));

            $response = \App::get('response');
            $this->assertArrayHasKey('Content-Type', $response->headers->headers);
            $this->assertSame('text/html; charset=utf-8', $response->headers->headers['Content-Type']);
            $this->assertSame(200, $response->statusCode);
        }

        #[Test]
        public function responseHeaderHelpersEmitExpectedHxHeaders(): void
        {
            $service = new HtmxService();
            $service->reswap('outerHTML');
            $service->pushUrl('/projects');
            $service->replaceUrl('/projects/1');
            $service->refresh();
            $service->reselect('#list');
            $service->triggerAfterSettle('saved', ['ok' => true]);
            $service->triggerAfterSwap('ready', ['state' => 'done']);

            $headers = \App::get('response')->headers->headers;

            $this->assertSame('outerHTML', $headers['HX-Reswap']);
            $this->assertSame('/projects', $headers['HX-Push-Url']);
            $this->assertSame('/projects/1', $headers['HX-Replace-Url']);
            $this->assertSame('true', $headers['HX-Refresh']);
            $this->assertSame('#list', $headers['HX-Reselect']);
            $this->assertStringContainsString('saved', $headers['HX-Trigger-After-Settle']);
            $this->assertStringContainsString('ready', $headers['HX-Trigger-After-Swap']);
        }

        #[Test]
        public function selectorHeadersRejectInvalidValues(): void
        {
            $service = new HtmxService();
            $service->retarget("#todo\r\nX-Test: injected");
            $service->reselect('<script>alert(1)</script>');

            $headers = \App::get('response')->headers->headers;
            $this->assertArrayNotHasKey('HX-Retarget', $headers);
            $this->assertArrayNotHasKey('HX-Reselect', $headers);
        }

        #[Test]
        public function varyAndRequestDetailsAreAvailable(): void
        {
            \App::set('request', new RequestStub([
                'HX-Request' => 'true',
                'HX-Boosted' => 'true',
                'HX-History-Restore-Request' => 'true',
                'HX-Target' => 'todo-panel',
                'HX-Trigger' => 'submit-btn',
                'HX-Trigger-Name' => 'save',
                'HX-Triggering-Event' => '{"type":"submit","detail":{"value":"abc"}}',
                'HX-Current-URL' => 'https://example.test/todo?tab=open#list',
                'HX-Prompt' => 'hello'
            ], [
                'REQUEST_SCHEME' => 'https',
                'HTTP_HOST' => 'example.test',
                'SERVER_PORT' => '443'
            ]));

            $service = new HtmxService();
            $service->varyOnRequest();
            $details = $service->requestDetails();

            $headers = \App::get('response')->headers->headers;
            $this->assertArrayHasKey('Vary', $headers);
            $this->assertStringContainsString('HX-Request', $headers['Vary']);
            $this->assertHtmxVaryHeader(\App::get('response'));
            $this->assertTrue($details['request']);
            $this->assertTrue($details['boosted']);
            $this->assertTrue($details['history_restore']);
            $this->assertSame('todo-panel', $details['target']);
            $this->assertSame('submit-btn', $details['trigger']);
            $this->assertSame('save', $details['trigger_name']);
            $this->assertIsArray($details['triggering_event']);
            $this->assertSame('submit', $details['triggering_event']['type']);
            $this->assertSame('abc', $details['triggering_event']['detail']['value']);
            $this->assertSame('https://example.test/todo?tab=open#list', $details['current_url']);
            $this->assertSame('/todo?tab=open#list', $details['current_url_abs_path']);
            $this->assertSame('hello', $details['prompt']);
        }

        #[Test]
        public function currentUrlAbsPathReturnsNullForCrossOriginUrl(): void
        {
            \App::set('request', new RequestStub([
                'HX-Current-URL' => 'https://evil.example/phish'
            ], [
                'REQUEST_SCHEME' => 'https',
                'HTTP_HOST' => 'example.test',
                'SERVER_PORT' => '443'
            ]));

            $service = new HtmxService();

            $this->assertNull($service->currentUrlAbsPath());
            $this->assertNull($service->requestDetails()['current_url_abs_path']);
        }

        #[Test]
        public function noContentHelperSends204AndCloses(): void
        {
            $service = new HtmxService();

            ob_start();
            $service->noContent();
            $output = (string) ob_get_clean();

            $response = \App::get('response');
            $this->assertSame('', $output);
            $this->assertSame(204, $response->statusCode);
            $this->assertHtmxStatus($response, 204);
            $this->assertTrue((bool) \App::get('closed'));
        }

        #[Test]
        public function stopPollingHelperSends286AndCloses(): void
        {
            $service = new HtmxService();

            ob_start();
            $service->stopPolling();
            $output = (string) ob_get_clean();

            $response = \App::get('response');
            $this->assertSame('', $output);
            $this->assertSame(286, $response->statusCode);
            $this->assertHtmxStatus($response, 286);
            $this->assertTrue((bool) \App::get('closed'));
        }

        #[Test]
        public function reswapRejectsUnsupportedStrategies(): void
        {
            $service = new HtmxService();

            $this->expectException(\InvalidArgumentException::class);
            $service->reswap('morph');
        }

        #[Test]
        public function validationHelperSends422AndTrigger(): void
        {
            $service = new HtmxService();
            $html = '<div class="errors">Invalid input</div>';

            ob_start();
            $service->validation($html, array('title' => 'Required'));
            $output = (string) ob_get_clean();

            $response = \App::get('response');
            $this->assertSame($html, $output);
            $this->assertSame(422, $response->statusCode);
            $this->assertHtmxStatus($response, 422);
            $this->assertArrayHasKey('HX-Trigger', $response->headers->headers);
            $this->assertStringContainsString('hubzero:validation-failed', $response->headers->headers['HX-Trigger']);
        }

        #[Test]
        public function htmxLifecycleCanEmitHeadersAndFragmentResponse(): void
        {
            \App::set('request', new RequestStub([
                'HX-Request' => 'true',
                'HX-Target' => 'todo-list'
            ], [
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/index.php?option=com_todo&task=add'
            ]));

            $service = new HtmxService();
            $service->varyOnRequest();
            $service->retarget('#todo-list');
            $service->trigger('todo:added', ['id' => 42]);

            $payload = '<li id="todo-42">Ship docs</li>';
            ob_start();
            $service->fragment($payload, 201);
            $output = (string) ob_get_clean();

            $response = \App::get('response');
            $headers = $response->headers->headers;

            $this->assertSame($payload, $output);
            $this->assertSame(201, $response->statusCode);
            $this->assertSame('text/html; charset=utf-8', $headers['Content-Type']);
            $this->assertStringContainsString('HX-Request', $headers['Vary']);
            $this->assertSame('#todo-list', $headers['HX-Retarget']);
            $this->assertStringContainsString('todo:added', $headers['HX-Trigger']);
            $this->assertTrue((bool) \App::get('closed'));
        }
    }
}
