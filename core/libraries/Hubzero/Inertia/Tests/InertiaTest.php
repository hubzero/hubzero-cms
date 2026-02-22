<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Inertia\Tests {

    use ArrayAccess;
    use Closure;
    use Hubzero\Inertia\Inertia;
    use Hubzero\Inertia\InertiaService;
    use Hubzero\Inertia\InertiaServiceProvider;
    use Hubzero\Inertia\Tests\Concerns\InteractsWithInertiaResponses;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class RequestStub
    {
        private array $headers = [];
        private string $method = 'GET';
        private array $server = [];
        private array $query = [];

        public function __construct(array $headers = [], string $method = 'GET', array $server = [], array $query = [])
        {
            foreach ($headers as $key => $value) {
                $this->headers[strtolower((string) $key)] = (string) $value;
            }

            $this->method = strtoupper($method);
            $this->server = $server;
            $this->query = $query;
        }

        public function header(string $key, string $default = ''): string
        {
            $normalized = strtolower($key);
            return $this->headers[$normalized] ?? $default;
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

        public function __construct()
        {
            $this->headers = new HeaderBagStub();
        }

        public function setStatusCode(int $status): void
        {
            $this->statusCode = $status;
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

    class ContainerStub implements ArrayAccess
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
            $value = $this->values[$key];

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

    class AppStub
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

        public static function redirect(string $url): void
        {
            self::$state['redirect_to'] = $url;
        }
    }

    if (!class_exists('\\App', false)) {
        class_alias(AppStub::class, 'App');
    }

    class TestableInertiaService extends InertiaService
    {
        public ?int $status = null;
        public array $headers = [];
        public ?array $jsonPayload = null;
        public ?string $body = null;
        public ?string $locationTarget = null;
        public ?string $redirectTarget = null;
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

            if (isset($this->headers['X-Inertia-Location'])) {
                $this->locationTarget = $this->headers['X-Inertia-Location'];
            }
            if (isset($this->headers['Location'])) {
                $this->redirectTarget = $this->headers['Location'];
            }

            if (
                is_string($body)
                && isset($this->headers['Content-Type'])
                && strpos(strtolower($this->headers['Content-Type']), 'application/json') !== false
            ) {
                $decoded = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->jsonPayload = $decoded;
                }
            }
        }
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class InertiaTest extends TestCase
    {
        use InteractsWithInertiaResponses;

        protected function setUp(): void
        {
            if (is_a('\\App', AppStub::class, true)) {
                AppStub::reset();
            }

            $request = new RequestStub([], 'GET', ['REQUEST_URI' => '/unit/inertia']);
            \App::set('request', $request);
            \App::set('response', new ResponseStub());
        }

        #[Test]
        public function serviceManagesSharedPropsAndFlushesState(): void
        {
            $service = new InertiaService();

            $service->share('user', 'alice');
            $service->share(['role' => 'admin']);

            $this->assertSame('alice', $service->getShared('user'));
            $this->assertSame('admin', $service->getShared('role'));
            $this->assertSame(['user' => 'alice', 'role' => 'admin'], $service->getShared());

            $service->flush(true);

            $this->assertSame([], $service->getShared());
            $this->assertSame('fallback', $service->getShared('missing', 'fallback'));
        }

        #[Test]
        public function flushGuardrailPreventsLateStateResetUnlessForced(): void
        {
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/inertia'], ['inertia_debug' => '1']));
            $service = new InertiaService();
            $service->share('user', 'alice');
            $service->profileStart('phase');

            $service->flush();

            $this->assertSame('alice', $service->getShared('user'));
            $service->profileStop('phase');
            $this->assertArrayHasKey('phase', $service->profileSnapshot());

            $service->flush(true);
            $this->assertSame([], $service->getShared());
            $this->assertSame([], $service->profileSnapshot());
        }

        #[Test]
        public function flushGuardrailAddsWarningInDebugModeWhenCalledTooLate(): void
        {
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/inertia'], ['inertia_debug' => '1']));
            $service = new InertiaService();

            $service->profileStart('phase');
            $service->flush();

            $context = $service->debugSnapshot()['context'] ?? array();

            $this->assertArrayHasKey('_warnings', $context);
            $this->assertContains(
                'flush() ignored: call flush() before profileStart/share/debugContext.',
                $context['_warnings']
            );
        }

        #[Test]
        public function serviceDetectsInertiaHeaderCaseInsensitively(): void
        {
            \App::set('request', new RequestStub([
                'x-inertia' => 'TrUe'
            ], 'GET', ['REQUEST_URI' => '/inertia']));

            $service = new InertiaService();

            $this->assertTrue($service->isInertiaRequest());
        }

        #[Test]
        public function serviceVersionSupportsFallbackStringAndCallableResolver(): void
        {
            $service = new InertiaService();

            $this->assertSame('dev', $service->version());

            $service->version('v1');
            $this->assertSame('v1', $service->version());

            $service->version(function () {
                return 'v2';
            });
            $this->assertSame('v2', $service->version());
        }

        #[Test]
        public function serviceRenderReturnsPageForNonInertiaRequestWithResolvedSharedProps(): void
        {
            $service = new InertiaService();
            $service->version('asset-hash');
            $service->share('lazy', function () {
                return 'computed';
            });

            $page = $service->render('Users/Index', ['local' => 'value']);

            $this->assertIsArray($page);
            $this->assertSame('Users/Index', $page['component']);
            $this->assertSame('/unit/inertia', $page['url']);
            $this->assertSame('asset-hash', $page['version']);
            $this->assertSame('computed', $page['props']['lazy']);
            $this->assertSame('value', $page['props']['local']);
            $this->assertInertiaPageShape($page);
            $this->assertInertiaComponent($page, 'Users/Index');
            $this->assertSame('value', $this->assertInertiaProp($page, 'local'));
        }

        #[Test]
        public function serviceRenderProducesInertiaJsonEnvelopeForInertiaRequests(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true'
            ], 'GET', ['REQUEST_URI' => '/inertia/page']));

            $service = new TestableInertiaService();
            $service->version('v-json');

            $result = $service->render('Users/Index', ['name' => 'sam']);

            $this->assertNull($result);
            $this->assertSame(200, $service->status);
            $this->assertSame('true', $service->headers['X-Inertia']);
            $this->assertSame('X-Inertia', $service->headers['Vary']);
            $this->assertSame('application/json; charset=utf-8', $service->headers['Content-Type']);
            $this->assertSame('Users/Index', $service->jsonPayload['component']);
            $this->assertSame('sam', $service->jsonPayload['props']['name']);
            $this->assertSame('v-json', $service->jsonPayload['version']);
            $this->assertInertiaPageShape($service->jsonPayload);
        }

        #[Test]
        public function terminalJsonFlowSetsStatusHeadersAndBody(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true'
            ], 'GET', ['REQUEST_URI' => '/inertia/page']));

            $service = new TestableInertiaService();
            $service->version('v-json-flow');
            $service->render('Users/Index', ['name' => 'sam']);

            $this->assertSame(200, $service->status);
            $this->assertSame('true', $service->headers['X-Inertia']);
            $this->assertSame('X-Inertia', $service->headers['Vary']);
            $this->assertSame('application/json; charset=utf-8', $service->headers['Content-Type']);
            $this->assertNotNull($service->body);
            $this->assertSame('Users/Index', $service->jsonPayload['component']);
        }

        #[Test]
        public function pagePropsOverrideSharedPropsOnKeyCollision(): void
        {
            $service = new InertiaService();
            $service->share('auth', ['role' => 'guest']);

            $page = $service->render('Users/Index', [
                'auth' => ['role' => 'member']
            ]);

            $this->assertIsArray($page);
            $this->assertSame(['role' => 'member'], $page['props']['auth']);
        }

        #[Test]
        public function servicePageReturnsCanonicalEnvelopeShape(): void
        {
            $service = new InertiaService();
            $service->version('v-envelope');

            $page = $service->page('Admin/Dashboard', ['ok' => true]);

            $this->assertSame('Admin/Dashboard', $page['component']);
            $this->assertSame(['ok' => true], $page['props']);
            $this->assertSame('/unit/inertia', $page['url']);
            $this->assertSame('v-envelope', $page['version']);
        }

        #[Test]
        public function serviceStoresAndReturnsRootView(): void
        {
            $service = new InertiaService();

            $this->assertNull($service->getRootView());

            $service->rootView(' component.inertia.shell ');

            $this->assertSame('component.inertia.shell', $service->getRootView());
        }

        #[Test]
        public function serviceRendersStandardizedRootNodeMarkup(): void
        {
            $service = new InertiaService();
            $service->version('v-root');

            $html = $service->renderRootNode('Polls/Dashboard', ['title' => 'A "quote"'], 'root-app');

            $this->assertStringContainsString('<div id="root-app"', $html);
            $this->assertStringContainsString('data-page="', $html);
            $this->assertStringContainsString('Polls\/Dashboard', $html);
            $this->assertStringContainsString('\\&quot;quote\\&quot;', $html);
            $this->assertStringContainsString('v-root', $html);
        }

        #[Test]
        public function serviceResolvesPartialPropsOnlyForMatchingComponent(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true',
                'X-Inertia-Partial-Component' => 'Polls/Show',
                'X-Inertia-Partial-Data' => 'alpha,gamma'
            ], 'GET', ['REQUEST_URI' => '/polls2/1']));

            $service = new InertiaService();
            $method = new \ReflectionMethod(InertiaService::class, 'resolveProps');
            $method->setAccessible(true);

            $filtered = $method->invoke($service, 'Polls/Show', [
                'alpha' => 1,
                'beta' => 2,
                'gamma' => 3
            ]);

            $this->assertSame(['alpha' => 1, 'gamma' => 3], $filtered);

            $unfiltered = $method->invoke($service, 'Polls/Index', [
                'alpha' => 1,
                'beta' => 2
            ]);

            $this->assertSame(['alpha' => 1, 'beta' => 2], $unfiltered);
        }

        #[Test]
        public function resolvePropsSkipsPartialFilteringWhenRequestIsNotInertia(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia-Partial-Component' => 'Polls/Show',
                'X-Inertia-Partial-Data' => 'alpha'
            ], 'GET', ['REQUEST_URI' => '/polls2/1']));

            $service = new InertiaService();
            $method = new \ReflectionMethod(InertiaService::class, 'resolveProps');
            $method->setAccessible(true);

            $props = [
                'alpha' => 1,
                'beta' => 2
            ];

            $actual = $method->invoke($service, 'Polls/Show', $props);

            $this->assertSame($props, $actual);
        }

        #[Test]
        public function serviceRedirectNormalizesMutationRedirectTo303ForInertiaRequests(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true'
            ], 'POST', ['REQUEST_URI' => '/polls2/vote']));

            $service = new TestableInertiaService();
            $service->redirect('/polls2', 302);

            $this->assertSame(303, $service->status);
            $this->assertSame('/polls2', $service->headers['Location']);
            $this->assertSame('X-Inertia', $service->headers['Vary']);
        }

        #[Test]
        public function terminalRedirectFlowNormalizes302To303ForMutations(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true'
            ], 'PATCH', ['REQUEST_URI' => '/polls2/vote']));

            $service = new TestableInertiaService();
            $service->redirect('/polls2', 302);

            $this->assertSame(303, $service->status);
            $this->assertSame('/polls2', $service->redirectTarget);
            $this->assertSame('X-Inertia', $service->headers['Vary']);
        }

        #[Test]
        public function serviceVersionMismatchTriggersInertiaLocationResponse(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true',
                'X-Inertia-Version' => 'old'
            ], 'GET', ['REQUEST_URI' => '/polls2/1']));

            $service = new TestableInertiaService();
            $service->version('new');

            $method = new \ReflectionMethod(InertiaService::class, 'handleVersionMismatch');
            $method->setAccessible(true);
            $method->invoke($service);

            $this->assertSame(409, $service->status);
            $this->assertSame('true', $service->headers['X-Inertia']);
            $this->assertSame('/polls2/1', $service->headers['X-Inertia-Location']);
            $this->assertSame('/polls2/1', $service->locationTarget);
        }

        #[Test]
        public function terminalLocationFlowReturns409AndLocationHeaderOnVersionMismatch(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true',
                'X-Inertia-Version' => 'old'
            ], 'GET', ['REQUEST_URI' => '/polls2/2']));

            $service = new TestableInertiaService();
            $service->version('new');
            $service->render('Polls/Show', ['id' => 2]);

            $this->assertSame(409, $service->status);
            $this->assertSame('true', $service->headers['X-Inertia']);
            $this->assertSame('/polls2/2', $service->headers['X-Inertia-Location']);
            $this->assertSame('/polls2/2', $service->locationTarget);
        }

        #[Test]
        public function requestDetailsExposeInertiaHeaders(): void
        {
            \App::set('request', new RequestStub([
                'X-Inertia' => 'true',
                'X-Inertia-Version' => 'v-test',
                'X-Inertia-Partial-Component' => 'Games/App',
                'X-Inertia-Partial-Data' => 'user,flash',
                'X-Inertia-Partial-Except' => 'debug',
                'X-Inertia-Error-Bag' => 'default',
                'X-Inertia-Reset' => 'wizard'
            ], 'GET', ['REQUEST_URI' => '/games?task=app']));

            $service = new InertiaService();
            $details = $service->requestDetails();

            $this->assertTrue($details['request']);
            $this->assertSame('v-test', $details['version']);
            $this->assertSame('Games/App', $details['partial_component']);
            $this->assertSame('user,flash', $details['partial_data']);
            $this->assertSame('debug', $details['partial_except']);
            $this->assertSame('default', $details['error_bag']);
            $this->assertSame('wizard', $details['reset']);
        }

        #[Test]
        public function serviceProviderRegistersSingletonLikeInertiaService(): void
        {
            $app = new ContainerStub();
            $provider = new InertiaServiceProvider($app);

            $provider->register();

            $this->assertTrue($app->has('inertia'));
            $first = $app->get('inertia');
            $second = $app->get('inertia');
            $this->assertInstanceOf(InertiaService::class, $first);
            $this->assertSame($first, $second);
        }

        #[Test]
        public function facadeDelegatesToBoundContainerService(): void
        {
            $container = new ContainerStub();
            $container->set('inertia', new InertiaService());
            \App::set('app', $container);

            Inertia::share('flash', 'saved');

            $this->assertSame('saved', Inertia::getShared('flash'));

            Inertia::version('build-123');
            $this->assertSame('build-123', Inertia::version());
        }

        #[Test]
        public function facadePageUsesContainerBoundService(): void
        {
            $service = new InertiaService();
            $service->version('v-facade');

            $container = new ContainerStub();
            $container->set('inertia', $service);
            \App::set('app', $container);
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/from/facade']));

            $page = Inertia::page('Docs/Index', ['a' => 1]);

            $this->assertSame('Docs/Index', $page['component']);
            $this->assertSame('v-facade', $page['version']);
            $this->assertSame('/from/facade', $page['url']);
        }

        #[Test]
        public function facadeRenderRootNodeUsesContainerBoundService(): void
        {
            $service = new InertiaService();
            $service->version('v-root-facade');

            $container = new ContainerStub();
            $container->set('inertia', $service);
            \App::set('app', $container);
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/from/facade']));

            $html = Inertia::renderRootNode('Docs/Index', ['x' => 1], 'docs-root');

            $this->assertStringContainsString('<div id="docs-root"', $html);
            $this->assertStringContainsString('Docs\/Index', $html);
            $this->assertStringContainsString('v-root-facade', $html);
        }

        #[Test]
        public function debugHeaderCanBeEnabledViaQueryFlag(): void
        {
            \App::set('request', new RequestStub(
                [],
                'GET',
                ['REQUEST_URI' => '/inertia?page=1'],
                ['inertia_debug' => '1', 'option' => 'com_games', 'task' => 'app']
            ));

            $service = new InertiaService();
            $service->debugContext('branch', 'inertia');
            $service->render('Games/App', ['ok' => true]);

            $response = \App::get('response');
            $headers = $response->headers->headers;
            $this->assertDebugHeaderContains($headers, 'X-Hubzero-Inertia-Debug', array(
                '"outgoing"',
                '"branch":"inertia"',
                '"task":"app"'
            ));
            $this->assertInertiaVaryHeader($response);
            $this->assertInertiaStatus($response, 200);
        }

        #[Test]
        public function renderDebugPanelOutputsMarkupWhenDebugEnabled(): void
        {
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/inertia'], ['inertia_debug' => '1']));
            $service = new InertiaService();
            $service->debugContext('branch', 'panel');

            $html = $service->renderDebugPanel();
            $this->assertDebugPanelMarkup($html, 'inertia', 'hz-inertia-debug-panel', 'Inertia Debug');
            $this->assertStringContainsString('"branch":"panel"', $html);
        }

        #[Test]
        public function preserveDebugParamAndHiddenInputAreGeneratedWhenEnabled(): void
        {
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/inertia'], ['inertia_debug' => '1']));
            $service = new InertiaService();

            $params = $service->preserveDebugParam(['task' => 'app']);
            $input = $service->renderDebugHiddenInput();

            $this->assertSame('1', $params['inertia_debug']);
            $this->assertStringContainsString('type="hidden"', $input);
            $this->assertStringContainsString('name="inertia_debug"', $input);
        }

        #[Test]
        public function profilerEmitsHeaderWhenDebugEnabled(): void
        {
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/inertia'], ['inertia_debug' => '1']));
            \App::set('config', new ConfigStub(['debug' => false]));

            $service = new InertiaService();
            $service->profileStart('page.render');
            usleep(1000);
            $service->profileStop('page.render');
            $service->emitProfileHeader();

            $response = \App::get('response');
            $headers = $response->headers->headers;
            $this->assertDebugHeaderContains($headers, 'X-Hubzero-Inertia-Profile', array('page.render'));
        }

        #[Test]
        public function facadeDelegatesInertiaDebugAndProfileHelpers(): void
        {
            \App::set('request', new RequestStub([], 'GET', ['REQUEST_URI' => '/inertia'], ['inertia_debug' => '1']));
            \App::set('config', new ConfigStub(['debug' => false]));

            $service = new InertiaService();
            $container = new ContainerStub();
            $container->set('inertia', $service);
            \App::set('app', $container);

            Inertia::debugContext('branch', 'facade');
            Inertia::profileStart('facade.render');
            usleep(1000);
            Inertia::profileStop('facade.render');
            Inertia::emitDebugHeader();
            Inertia::emitProfileHeader();

            $this->assertTrue(Inertia::isDebugEnabled());
            $this->assertArrayHasKey('branch', Inertia::debugSnapshot()['context']);
            $this->assertArrayHasKey('request', Inertia::requestDetails());
            $this->assertArrayHasKey('facade.render', Inertia::profileSnapshot());
            $this->assertStringContainsString('hz-inertia-debug-panel', Inertia::renderDebugPanel());
            $this->assertSame('1', Inertia::preserveDebugParam(array())['inertia_debug']);
            $this->assertStringContainsString('name="inertia_debug"', Inertia::renderDebugHiddenInput());

            $headers = \App::get('response')->headers->headers;
            $this->assertDebugHeaderContains($headers, 'X-Hubzero-Inertia-Debug', array('"outgoing"'));
            $this->assertDebugHeaderContains($headers, 'X-Hubzero-Inertia-Profile', array('facade.render'));
        }
    }
}
