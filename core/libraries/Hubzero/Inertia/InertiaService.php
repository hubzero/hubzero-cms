<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia;

use Hubzero\Debug\PanelRenderer;
use Hubzero\Debug\ModeSupport;
use Hubzero\Debug\HeaderSupport;

/**
 * Inertia protocol service.
 */
class InertiaService
{
    /**
     * Shared props for all responses in the current request.
     *
     * @var array
     */
    protected $sharedProps = array();

    /**
     * Optional version resolver.
     *
     * @var callable|string|null
     */
    protected $versionResolver = null;

    /**
     * Optional root view template identifier.
     *
     * @var string|null
     */
    protected $rootView = null;

    /**
     * Outbound Inertia headers emitted during this request.
     *
     * @var array
     */
    protected $sentHeaders = array();

    /**
     * Optional debug context entries for current request.
     *
     * @var array
     */
    protected $debugContext = array();

    /**
     * Profile timing data for current request.
     *
     * @var array
     */
    protected $profile = array();

    /**
     * Active profile timers keyed by label.
     *
     * @var array
     */
    protected $profileTimers = array();

    /**
     * Tracks whether mutable request lifecycle APIs were used since last flush.
     *
     * @var bool
     */
    protected $lifecycleTouched = false;

    /**
     * @return  bool
     */
    public function isInertiaRequest(): bool
    {
        $request = \App::get('request');
        $header = strtolower((string) $request->header('X-Inertia', ''));

        return $header === 'true';
    }

    /**
     * @param   string  $component
     * @param   array   $props
     * @return  array|null
     */
    public function render(string $component, array $props = []): ?array
    {
        $props = $this->resolveProps($component, array_merge($this->resolveSharedProps(), $props));
        $this->addVaryInertia();

        if (!$this->isInertiaRequest()) {
            return $this->page($component, $props);
        }

        $this->handleVersionMismatch();
        $this->json($this->page($component, $props));

        return null;
    }

    /**
     * @param   string  $component
     * @param   array   $props
     * @return  array
     */
    public function page(string $component, array $props = []): array
    {
        return array(
            'component' => $component,
            'props'     => $props,
            'url'       => $this->url(),
            'version'   => $this->version()
        );
    }

    /**
     * Build a canonical mount node for initial HTML responses.
     *
     * @param   string  $component
     * @param   array   $props
     * @param   string  $mountId
     * @return  string
     */
    public function renderRootNode(string $component, array $props = array(), string $mountId = 'app'): string
    {
        // Go through the same merge render() does. Calling page() with the raw
        // props would ship an initial page without any shared prop, so a
        // component reading one would render blank on first paint and only
        // fill in after the first client side visit.
        $props = $this->resolveProps(
            $component,
            array_merge($this->resolveSharedProps(), $props)
        );

        $page = $this->page($component, $props);
        $json = json_encode($page);

        if (!is_string($json)) {
            $json = '{}';
        }

        $safeId = htmlspecialchars(trim($mountId) !== '' ? $mountId : 'app', ENT_QUOTES, 'UTF-8');
        $safeJson = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

        return '<div id="' . $safeId . '" data-page="' . $safeJson . '"></div>';
    }

    /**
     * @return  string
     */
    public function url(): string
    {
        $request = \App::get('request');
        return (string) $request->server('REQUEST_URI', '/');
    }

    /**
     * @return  string
     */
    public function version($resolver = null): string
    {
        if (func_num_args() > 0) {
            $this->versionResolver = $resolver;
        }

        if ($this->isLazyValue($this->versionResolver)) {
            return (string) call_user_func($this->versionResolver);
        }

        if (is_string($this->versionResolver) && $this->versionResolver !== '') {
            return $this->versionResolver;
        }

        // Generic fallback when no resolver is configured.
        return 'dev';
    }

    /**
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public function share($key, $value = null): void
    {
        $this->markLifecycleTouched();

        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
            return;
        }

        $this->sharedProps[(string) $key] = $value;
    }

    /**
     * @param   string|null  $key
     * @param   mixed        $default
     * @return  mixed
     */
    public function getShared(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->sharedProps;
        }

        return array_key_exists($key, $this->sharedProps) ? $this->sharedProps[$key] : $default;
    }

    /**
     * Flush request-scoped Inertia state.
     *
     * Guardrail: once lifecycle state has been touched via share()/debugContext()/profileStart(),
     * flush() is ignored unless forced. In debug mode, a warning is recorded into debug context.
     *
     * @param   bool  $force
     * @return  void
     */
    public function flush(bool $force = false): void
    {
        if (!$force && $this->lifecycleTouched) {
            if ($this->isDebugEnabled() && !empty($this->profileTimers)) {
                $this->debugContext['_warnings'] = isset($this->debugContext['_warnings'])
                    && is_array($this->debugContext['_warnings'])
                    ? $this->debugContext['_warnings']
                    : array();
                $this->debugContext['_warnings'][] =
                    'flush() ignored: call flush() before profileStart/share/debugContext.';
            }
            return;
        }

        $this->sharedProps = array();
        $this->versionResolver = null;
        $this->rootView = null;
        $this->sentHeaders = array();
        $this->debugContext = array();
        $this->profile = array();
        $this->profileTimers = array();
        $this->lifecycleTouched = false;
    }

    /**
     * @param   string  $url
     * @return  void
     */
    public function location(string $url): void
    {
        if ($this->isInertiaRequest()) {
            $this->sendAndTerminate(409, array(
                'X-Inertia' => 'true',
                'X-Inertia-Location' => $url
            ));
            return;
        }

        \App::redirect($url);
    }

    /**
     * @param   string  $url
     * @param   int     $status
     * @return  void
     */
    public function redirect(string $url, int $status = 302): void
    {
        $method = strtoupper((string) \App::get('request')->getMethod());
        if (
            $this->isInertiaRequest()
            && $status === 302
            && in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'), true)
        ) {
            $status = 303;
        }

        $this->sendAndTerminate($status, array(
            'Location' => $url
        ));
    }

    /**
     * @param   string  $view
     * @return  void
     */
    public function rootView(string $view): void
    {
        $this->rootView = trim($view);
    }

    /**
     * @return  string|null
     */
    public function getRootView(): ?string
    {
        return $this->rootView;
    }

    /**
     * @param   array  $page
     * @return  void
     */
    protected function json(array $page): void
    {
        $encoded = json_encode($page);
        if (!is_string($encoded)) {
            $encoded = '{}';
        }

        $this->sendAndTerminate(200, array(
            'X-Inertia' => 'true',
            'Content-Type' => 'application/json; charset=utf-8'
        ), $encoded);
    }

    /**
     * @return  void
     */
    protected function handleVersionMismatch(): void
    {
        $request = \App::get('request');
        $method = strtoupper((string) $request->getMethod());
        if ($method !== 'GET') {
            return;
        }

        $clientVersion = (string) $request->header('X-Inertia-Version', '');

        if ($clientVersion === '' || $clientVersion === $this->version()) {
            return;
        }

        $this->location($this->url());
    }

    /**
     * Whether a value should be called to produce the real one
     *
     * Deliberately narrower than is_callable(): a string or an array is data
     * here, even when it happens to name a function or a method pair.
     *
     * @param   mixed  $value
     * @return  bool
     */
    protected function isLazyValue($value): bool
    {
        return $value instanceof \Closure
            || (is_object($value) && is_callable($value));
    }

    /**
     * @return  array
     */
    protected function resolveSharedProps(): array
    {
        $resolved = array();

        foreach ($this->sharedProps as $key => $value) {
            // Only a closure or an invokable object counts as a lazy prop.
            // is_callable() is true for any string naming a global function, so
            // sharing the plain string 'time' or 'compact' would otherwise call
            // it instead of passing the value through.
            $resolved[$key] = $this->isLazyValue($value)
                ? call_user_func($value)
                : $value;
        }

        return $resolved;
    }

    /**
     * @param   string  $component
     * @param   array   $props
     * @return  array
     */
    protected function resolveProps(string $component, array $props): array
    {
        if (!$this->isInertiaRequest()) {
            return $props;
        }

        $request = \App::get('request');
        $partialComponent = (string) $request->header('X-Inertia-Partial-Component', '');
        if ($partialComponent !== '' && $partialComponent !== $component) {
            return $props;
        }

        $partialData = trim((string) $request->header('X-Inertia-Partial-Data', ''));
        if ($partialData === '') {
            return $props;
        }

        $keys = array_filter(array_map('trim', explode(',', $partialData)));
        if (empty($keys)) {
            return $props;
        }

        $filtered = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $props)) {
                $filtered[$key] = $props[$key];
            }
        }

        return $filtered;
    }

    /**
     * @return  void
     */
    protected function addVaryInertia(): void
    {
        $tokens = array();

        if (isset($this->sentHeaders['Vary'])) {
            $tokens = array_merge($tokens, preg_split('/\s*,\s*/', (string) $this->sentHeaders['Vary']) ?: array());
        }

        $response = \App::get('response');
        if ($response && isset($response->headers) && method_exists($response->headers, 'get')) {
            $existing = (string) $response->headers->get('Vary', '');
            if ($existing !== '') {
                $tokens = array_merge($tokens, preg_split('/\s*,\s*/', $existing) ?: array());
            }
        }

        foreach (headers_list() as $header) {
            if (stripos($header, 'Vary:') === 0) {
                $value = trim(substr($header, strlen('Vary:')));
                if ($value !== '') {
                    $tokens = array_merge($tokens, preg_split('/\s*,\s*/', $value) ?: array());
                }
            }
        }

        $normalized = array();
        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                continue;
            }
            $normalized[strtolower($token)] = $token;
        }

        $normalized['x-inertia'] = 'X-Inertia';
        $this->setHeader('Vary', implode(', ', array_values($normalized)));
    }

    /**
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public function debugContext($key, $value = null): void
    {
        $this->markLifecycleTouched();

        if (is_array($key)) {
            $this->debugContext = array_merge($this->debugContext, $key);
            return;
        }

        $this->debugContext[(string) $key] = $value;
    }

    /**
     * @return  bool
     */
    public function isDebugEnabled(): bool
    {
        $request = \App::get('request');
        if (!$request) {
            return false;
        }

        $header = strtolower(trim((string) $request->header('X-Inertia-Debug', '')));
        if ($header === 'true' || $header === '1') {
            return true;
        }

        if (method_exists($request, 'get')) {
            $query = strtolower(trim((string) $request->get('inertia_debug', '')));
            if ($query === 'true' || $query === '1') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return  array
     */
    public function debugSnapshot(): array
    {
        $incoming = array();
        foreach ($this->allIncomingHeaders() as $name => $value) {
            $lower = strtolower((string) $name);
            if (strpos($lower, 'x-inertia') === 0 || $lower === 'x-requested-with') {
                $incoming[$name] = $value;
            }
        }

        return array(
            'incoming' => $incoming,
            'outgoing' => $this->sentHeaders,
            'context'  => array_merge($this->requestContext(), $this->debugContext),
            'request'  => $this->requestDetails(),
        );
    }

    /**
     * Return parsed Inertia request context details.
     *
     * @return  array
     */
    public function requestDetails(): array
    {
        return array(
            'request' => $this->isInertiaRequest(),
            'version' => $this->requestHeaderValue('X-Inertia-Version'),
            'partial_component' => $this->requestHeaderValue('X-Inertia-Partial-Component'),
            'partial_data' => $this->requestHeaderValue('X-Inertia-Partial-Data'),
            'partial_except' => $this->requestHeaderValue('X-Inertia-Partial-Except'),
            'error_bag' => $this->requestHeaderValue('X-Inertia-Error-Bag'),
            'reset' => $this->requestHeaderValue('X-Inertia-Reset'),
        );
    }

    /**
     * Render reusable Inertia debug panel.
     *
     * @param   array  $snapshot
     * @param   array  $options
     * @return  string
     */
    public function renderDebugPanel(array $snapshot = array(), array $options = array()): string
    {
        if (!$this->isDebugEnabled()) {
            return '';
        }

        $snapshot = !empty($snapshot) ? $snapshot : $this->debugSnapshot();

        return PanelRenderer::render(
            'inertia',
            $snapshot,
            $options,
            'inertia-debug-panel',
            'Inertia Debug',
            'inertia_debug_panel_',
            'hz-inertia-debug-panel',
            'hz-inertia-debug-bar',
            'hz-inertia-debug-actions'
        );
    }

    /**
     * Preserve Inertia debug mode in outbound parameters.
     *
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    public function preserveDebugParam(array $params, string $name = 'inertia_debug', string $value = '1'): array
    {
        return ModeSupport::preserveParam($this->isDebugEnabled(), $params, $name, $value);
    }

    /**
     * Render hidden input used to preserve debug mode across form submissions.
     *
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    public function renderDebugHiddenInput(string $name = 'inertia_debug', string $value = '1'): string
    {
        return ModeSupport::renderHiddenInput($this->isDebugEnabled(), $name, $value);
    }

    /**
     * Emit debug header immediately when enabled.
     *
     * @return  void
     */
    public function emitDebugHeader(): void
    {
        $this->emitDebugHeaderIfEnabled();
    }

    /**
     * Start a named profile timer.
     *
     * @param   string  $label
     * @return  void
     */
    public function profileStart(string $label): void
    {
        $label = trim($label);
        if ($label === '') {
            return;
        }

        $this->markLifecycleTouched();
        $this->profileTimers[$label] = microtime(true);
    }

    /**
     * Stop a named profile timer.
     *
     * @param   string  $label
     * @return  void
     */
    public function profileStop(string $label): void
    {
        $label = trim($label);
        if ($label === '' || !isset($this->profileTimers[$label])) {
            return;
        }

        $elapsedMs = (microtime(true) - $this->profileTimers[$label]) * 1000;
        unset($this->profileTimers[$label]);

        $this->profile[$label] = round($elapsedMs, 2);
    }

    /**
     * Return recorded profile timing data.
     *
     * @return  array
     */
    public function profileSnapshot(): array
    {
        return $this->profile;
    }

    /**
     * Emit profile header immediately when enabled.
     *
     * @return  void
     */
    public function emitProfileHeader(): void
    {
        $this->emitProfileHeaderIfEnabled();
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @return  void
     */
    protected function setHeader(string $name, string $value): void
    {
        $this->sentHeaders[$name] = $value;

        $response = \App::get('response');
        if ($response && isset($response->headers)) {
            $response->headers->set($name, $value);
        }

        if ($name === 'Vary') {
            header($name . ': ' . $value, false);
        } else {
            header($name . ': ' . $value, true);
        }

        $this->emitDebugHeaderIfEnabled();
    }

    /**
     * @param   int  $status
     * @return  void
     */
    protected function setStatusCode(int $status): void
    {
        $response = \App::get('response');
        if ($response && method_exists($response, 'setStatusCode')) {
            $response->setStatusCode($status);
        }

        http_response_code($status);
    }

    /**
     * Centralized terminal response path for status/headers/body + exit.
     *
     * @param   int          $status
     * @param   array        $headers
     * @param   string|null  $body
     * @return  void
     */
    protected function sendAndTerminate(int $status, array $headers = array(), ?string $body = null): void
    {
        $this->setStatusCode($status);

        foreach ($headers as $name => $value) {
            $this->setHeader((string) $name, (string) $value);
        }

        $this->addVaryInertia();

        if ($body !== null) {
            echo $body;
        }

        exit;
    }

    /**
     * Emit an optional compact Inertia debug header.
     *
     * @return  void
     */
    protected function emitDebugHeaderIfEnabled(): void
    {
        if (!$this->isDebugEnabled()) {
            return;
        }

        $encoded = HeaderSupport::encodeJson($this->debugSnapshot(), '{"error":"debug_encode_failed"}', 3500);
        HeaderSupport::emitHeader('X-Hubzero-Inertia-Debug', $encoded);
        $this->emitProfileHeaderIfEnabled();
    }

    /**
     * Emit an optional compact Inertia profile header and log line.
     *
     * @return  void
     */
    protected function emitProfileHeaderIfEnabled(): void
    {
        if (!$this->isDebugEnabled() || empty($this->profile)) {
            return;
        }

        $encoded = HeaderSupport::encodeJson($this->profile, '{"error":"profile_encode_failed"}');
        HeaderSupport::emitHeader('X-Hubzero-Inertia-Profile', $encoded);
        HeaderSupport::logProfile('inertia', $encoded);
    }

    /**
     * @return  array
     */
    protected function requestContext(): array
    {
        $request = \App::get('request');
        if (!$request) {
            return array();
        }

        $ctx = array();
        if (method_exists($request, 'getMethod')) {
            $ctx['method'] = (string) $request->getMethod();
        }
        if (method_exists($request, 'server')) {
            $ctx['uri'] = (string) $request->server('REQUEST_URI', '');
        }
        if (method_exists($request, 'get')) {
            $ctx['option'] = (string) $request->get('option', '');
            $ctx['task'] = (string) $request->get('task', '');
        }

        return $ctx;
    }

    /**
     * @return  array
     */
    protected function allIncomingHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                return $headers;
            }
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (is_array($headers)) {
                return $headers;
            }
        }

        return array();
    }

    /**
     * Best-effort lookup for incoming request header value.
     *
     * @param   string  $name
     * @return  string
     */
    protected function requestHeaderValue(string $name): string
    {
        $request = \App::get('request');
        if ($request && method_exists($request, 'header')) {
            $value = (string) $request->header($name, '');
            if ($value !== '') {
                return $value;
            }
        }

        $headers = $this->allIncomingHeaders();
        $needle = strtolower(trim($name));
        foreach ($headers as $key => $value) {
            if (strtolower(trim((string) $key)) === $needle) {
                return (string) $value;
            }
        }

        $serverKey = 'HTTP_' . str_replace('-', '_', strtoupper($name));
        if (isset($_SERVER[$serverKey])) {
            return (string) $_SERVER[$serverKey];
        }

        return '';
    }

    /**
     * Mark that mutable request-lifecycle APIs have been used.
     *
     * @return  void
     */
    protected function markLifecycleTouched(): void
    {
        $this->lifecycleTouched = true;
    }
}
