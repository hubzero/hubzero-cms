<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx;

use Hubzero\Debug\PanelRenderer;
use Hubzero\Debug\ModeSupport;
use Hubzero\Debug\HeaderSupport;

/**
 * HTMX protocol helper service.
 */
class HtmxService
{
    /**
     * Supported values for HX-Reswap.
     *
     * @var array
     */
    protected const RESWAP_STRATEGIES = array(
        'innerhtml',
        'outerhtml',
        'textcontent',
        'beforebegin',
        'afterbegin',
        'beforeend',
        'afterend',
        'delete',
        'none',
    );

    /**
     * Shared HTMX/Alpine state for current request.
     *
     * @var array
     */
    protected $state = array();

    /**
     * Accumulated HX-Trigger payload.
     *
     * @var array
     */
    protected $triggers = array();

    /**
     * Accumulated HX-Trigger-After-Settle payload.
     *
     * @var array
     */
    protected $triggersAfterSettle = array();

    /**
     * Accumulated HX-Trigger-After-Swap payload.
     *
     * @var array
     */
    protected $triggersAfterSwap = array();

    /**
     * Outbound HTMX headers emitted during this request.
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
     * @return  bool
     */
    public function isHtmxRequest(): bool
    {
        $request = \App::get('request');
        if (!$request) {
            return false;
        }

        return $this->isTruthyHeader($request, array(
            'HX-Request',
            'Hx-Request',
            'hx-request'
        ), array(
            'HTTP_HX_REQUEST',
            'REDIRECT_HTTP_HX_REQUEST',
            'HX_REQUEST',
            'REDIRECT_HX_REQUEST'
        ));
    }

    /**
     * @return  bool
     */
    public function isHtmxBoosted(): bool
    {
        $request = \App::get('request');
        if (!$request) {
            return false;
        }

        return $this->isTruthyHeader($request, array(
            'HX-Boosted',
            'Hx-Boosted',
            'hx-boosted'
        ), array(
            'HTTP_HX_BOOSTED',
            'REDIRECT_HTTP_HX_BOOSTED',
            'HX_BOOSTED',
            'REDIRECT_HX_BOOSTED'
        ));
    }

    /**
     * @return  bool
     */
    public function isHtmxHistoryRestoreRequest(): bool
    {
        $request = \App::get('request');
        if (!$request) {
            return false;
        }

        return $this->isTruthyHeader($request, array(
            'HX-History-Restore-Request',
            'Hx-History-Restore-Request',
            'hx-history-restore-request'
        ), array(
            'HTTP_HX_HISTORY_RESTORE_REQUEST',
            'REDIRECT_HTTP_HX_HISTORY_RESTORE_REQUEST',
            'HX_HISTORY_RESTORE_REQUEST',
            'REDIRECT_HX_HISTORY_RESTORE_REQUEST'
        ));
    }

    /**
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public function state($key, $value = null): void
    {
        if (is_array($key)) {
            $this->state = array_merge($this->state, $key);
            return;
        }

        $this->state[(string) $key] = $value;
    }

    /**
     * @param   string|null  $key
     * @param   mixed        $default
     * @return  mixed
     */
    public function getState(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->state;
        }

        return array_key_exists($key, $this->state) ? $this->state[$key] : $default;
    }

    /**
     * @param   string  $id
     * @return  string
     */
    public function renderStateNode(string $id = 'hx-state'): string
    {
        $safeId = htmlspecialchars(trim($id) !== '' ? $id : 'hx-state', ENT_QUOTES, 'UTF-8');
        $json = json_encode($this->state);
        if (!is_string($json)) {
            $json = '{}';
        }
        $safeJson = htmlspecialchars($json, ENT_NOQUOTES, 'UTF-8');

        return '<script type="application/json" id="' . $safeId . '">' . $safeJson . '</script>';
    }

    /**
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    public function trigger(string $event, $payload = array()): void
    {
        $event = trim($event);
        if ($event === '') {
            return;
        }

        $this->triggers[$event] = $payload;
        $this->setHeader('HX-Trigger', json_encode($this->triggers));
    }

    /**
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    public function triggerAfterSettle(string $event, $payload = array()): void
    {
        $event = trim($event);
        if ($event === '') {
            return;
        }

        $this->triggersAfterSettle[$event] = $payload;
        $this->setHeader('HX-Trigger-After-Settle', json_encode($this->triggersAfterSettle));
    }

    /**
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    public function triggerAfterSwap(string $event, $payload = array()): void
    {
        $event = trim($event);
        if ($event === '') {
            return;
        }

        $this->triggersAfterSwap[$event] = $payload;
        $this->setHeader('HX-Trigger-After-Swap', json_encode($this->triggersAfterSwap));
    }

    /**
     * @param   string  $url
     * @return  void
     */
    public function redirect(string $url): void
    {
        if ($this->isHtmxRequest()) {
            $this->setHeader('HX-Redirect', $url);
            return;
        }

        \App::redirect($url);
    }

    /**
     * @param   string  $url
     * @param   array   $options
     * @return  void
     */
    public function location(string $url, array $options = array()): void
    {
        if ($this->isHtmxRequest()) {
            if (empty($options)) {
                $this->setHeader('HX-Location', $url);
                return;
            }

            $payload = array_merge(array('path' => $url), $options);
            $this->setHeader('HX-Location', json_encode($payload));
            return;
        }

        \App::redirect($url);
    }

    /**
     * @param   string  $strategy
     * @return  void
     */
    public function reswap(string $strategy): void
    {
        $strategy = trim($strategy);
        if ($strategy === '') {
            return;
        }

        $normalized = strtolower($strategy);
        if (!in_array($normalized, self::RESWAP_STRATEGIES, true)) {
            throw new \InvalidArgumentException(
                'Unsupported HX-Reswap strategy "' . $strategy . '".'
            );
        }

        $this->setHeader('HX-Reswap', $strategy);
    }

    /**
     * @param   string  $selector
     * @return  void
     */
    public function retarget(string $selector): void
    {
        $selector = trim($selector);
        if (!$this->isValidSelectorHeaderValue($selector)) {
            return;
        }

        $this->setHeader('HX-Retarget', $selector);
    }

    /**
     * @param   string|bool  $url
     * @return  void
     */
    public function pushUrl($url): void
    {
        $value = is_bool($url) ? ($url ? 'true' : 'false') : trim((string) $url);
        if ($value === '') {
            return;
        }

        $this->setHeader('HX-Push-Url', $value);
    }

    /**
     * @param   string|bool  $url
     * @return  void
     */
    public function replaceUrl($url): void
    {
        $value = is_bool($url) ? ($url ? 'true' : 'false') : trim((string) $url);
        if ($value === '') {
            return;
        }

        $this->setHeader('HX-Replace-Url', $value);
    }

    /**
     * @return  void
     */
    public function refresh(): void
    {
        $this->setHeader('HX-Refresh', 'true');
    }

    /**
     * @param   string  $selector
     * @return  void
     */
    public function reselect(string $selector): void
    {
        $selector = trim($selector);
        if (!$this->isValidSelectorHeaderValue($selector)) {
            return;
        }

        $this->setHeader('HX-Reselect', $selector);
    }

    /**
     * Ensure response includes cache Vary token for HX-Request.
     *
     * @return  void
     */
    public function varyOnRequest(): void
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

        $normalized = array();
        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                continue;
            }
            $normalized[strtolower($token)] = $token;
        }

        $normalized['hx-request'] = 'HX-Request';
        $this->setHeader('Vary', implode(', ', array_values($normalized)));
    }

    /**
     * Return parsed HTMX request context details.
     *
     * @return  array
     */
    public function requestDetails(): array
    {
        return array(
            'request'         => $this->isHtmxRequest(),
            'boosted'         => $this->isHtmxBoosted(),
            'history_restore' => $this->isHtmxHistoryRestoreRequest(),
            'target'          => $this->requestHeaderValue('HX-Target'),
            'trigger'         => $this->requestHeaderValue('HX-Trigger'),
            'trigger_name'    => $this->requestHeaderValue('HX-Trigger-Name'),
            'triggering_event' => $this->parseTriggeringEvent(),
            'current_url'     => $this->requestHeaderValue('HX-Current-URL'),
            'current_url_abs_path' => $this->currentUrlAbsPath(),
            'prompt'          => $this->requestHeaderValue('HX-Prompt'),
        );
    }

    /**
     * Return same-origin absolute path derived from HX-Current-URL.
     *
     * @return  string|null
     */
    public function currentUrlAbsPath(): ?string
    {
        $currentUrl = trim($this->requestHeaderValue('HX-Current-URL'));
        if ($currentUrl === '') {
            return null;
        }

        $parts = parse_url($currentUrl);
        if ($parts === false) {
            return null;
        }

        if (isset($parts['host'])) {
            $requestOrigin = $this->requestOrigin();
            if ($requestOrigin === null) {
                return null;
            }

            $urlScheme = strtolower((string) ($parts['scheme'] ?? ''));
            $urlHost = strtolower((string) $parts['host']);
            $urlPort = isset($parts['port']) ? (int) $parts['port'] : $this->defaultPortForScheme($urlScheme);

            if ($urlScheme === '' || $urlHost === '' || $urlPort === null) {
                return null;
            }

            if (
                $urlScheme !== $requestOrigin['scheme']
                || $urlHost !== $requestOrigin['host']
                || $urlPort !== $requestOrigin['port']
            ) {
                return null;
            }
        }

        $path = (string) ($parts['path'] ?? '/');
        if ($path === '') {
            $path = '/';
        } elseif (strpos($path, '/') !== 0) {
            $path = '/' . ltrim($path, '/');
        }

        $result = $path;
        if (isset($parts['query']) && $parts['query'] !== '') {
            $result .= '?' . $parts['query'];
        }
        if (isset($parts['fragment']) && $parts['fragment'] !== '') {
            $result .= '#' . $parts['fragment'];
        }

        return $result;
    }

    /**
     * @param   string  $url
     * @param   array   $params
     * @return  string
     */
    public function url(string $url, array $params = array()): string
    {
        if (!empty($params)) {
            $sep = strpos($url, '?') !== false ? '&' : '?';
            $url .= $sep . http_build_query($params);
        }

        return \Route::url($url, false);
    }

    /**
     * @param   string  $component
     * @param   string  $task
     * @param   array   $params
     * @return  string
     */
    public function actionUrl(string $component, string $task, array $params = array()): string
    {
        $component = trim($component);
        if ($component !== '' && strpos($component, 'com_') !== 0) {
            $component = 'com_' . $component;
        }

        $query = array(
            'option' => $component,
            'task'   => trim($task)
        );

        return $this->url('index.php', array_merge($query, $params));
    }

    /**
     * Emit an HTMX fragment response and terminate request processing.
     *
     * @param   string  $html
     * @return  void
     */
    public function fragment(string $html, int $status = 200): void
    {
        if ($status < 100 || $status > 599) {
            $status = 200;
        }

        $response = \App::get('response');
        if ($response && isset($response->headers)) {
            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
            if (method_exists($response, 'setStatusCode')) {
                $response->setStatusCode($status);
            }
        } else {
            header('Content-Type: text/html; charset=utf-8', true, $status);
            if (function_exists('http_response_code')) {
                http_response_code($status);
            }
        }

        echo $html;
        \App::close();
    }

    /**
     * Emit a 204 No Content response.
     *
     * @return  void
     */
    public function noContent(): void
    {
        $this->fragment('', 204);
    }

    /**
     * Emit a 286 response to stop HTMX polling.
     *
     * @return  void
     */
    public function stopPolling(): void
    {
        $this->fragment('', 286);
    }

    /**
     * Emit a 422 validation response with fragment HTML payload.
     *
     * @param   string  $html
     * @param   array   $errors
     * @return  void
     */
    public function validation(string $html, array $errors = array()): void
    {
        $payload = array(
            'count' => count($errors),
            'errors' => $errors
        );
        $this->trigger('hubzero:validation-failed', $payload);
        $this->fragment($html, 422);
    }

    /**
     * Clear mutable request lifecycle state.
     *
     * @return  void
     */
    public function flush(): void
    {
        $this->state = array();
        $this->triggers = array();
        $this->triggersAfterSettle = array();
        $this->triggersAfterSwap = array();
        $this->sentHeaders = array();
        $this->debugContext = array();
        $this->profile = array();
        $this->profileTimers = array();
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
     * Set debug context data (dev/debug mode only consumption).
     *
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public function debugContext($key, $value = null): void
    {
        if (is_array($key)) {
            $this->debugContext = array_merge($this->debugContext, $key);
            return;
        }

        $this->debugContext[(string) $key] = $value;
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
            $this->emitDebugHeaderIfEnabled();
            return;
        }

        header($name . ': ' . $value, true);
        $this->emitDebugHeaderIfEnabled();
    }

    /**
     * Check request header/server keys for a true-ish value.
     *
     * @param   object  $request
     * @param   array   $headerKeys
     * @param   array   $serverKeys
     * @return  bool
     */
    protected function isTruthyHeader($request, array $headerKeys, array $serverKeys): bool
    {
        $normalizedHeaderKeys = array();
        foreach ($headerKeys as $key) {
            $normalizedHeaderKeys[] = strtolower(trim($key));
        }

        if (method_exists($request, 'header')) {
            foreach ($headerKeys as $key) {
                $value = $this->normalizeBoolString((string) $request->header($key, ''));
                if ($value === 'true' || $value === '1') {
                    return true;
                }
            }
        }

        if (method_exists($request, 'server')) {
            foreach ($serverKeys as $key) {
                $value = $this->normalizeBoolString((string) $request->server($key, ''));
                if ($value === 'true' || $value === '1') {
                    return true;
                }
            }
        }

        foreach ($serverKeys as $key) {
            if (isset($_SERVER[$key])) {
                $value = $this->normalizeBoolString((string) $_SERVER[$key]);
                if ($value === 'true' || $value === '1') {
                    return true;
                }
            }
        }

        $headers = $this->allIncomingHeaders();
        foreach ($headers as $key => $value) {
            if (in_array(strtolower(trim((string) $key)), $normalizedHeaderKeys, true)) {
                $normalized = $this->normalizeBoolString((string) $value);
                if ($normalized === 'true' || $normalized === '1') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param   string  $value
     * @return  string
     */
    protected function normalizeBoolString(string $value): string
    {
        return strtolower(trim($value));
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
     * @return  bool
     */
    public function isDebugEnabled(): bool
    {
        $request = \App::get('request');
        if (!$request) {
            return false;
        }

        if (method_exists($request, 'header')) {
            $header = $this->normalizeBoolString((string) $request->header('HX-Debug', ''));
            if ($header === 'true' || $header === '1') {
                return true;
            }
        }

        if (method_exists($request, 'get')) {
            $query = $this->normalizeBoolString((string) $request->get('htmx_debug', ''));
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
            if (strpos($lower, 'hx-') === 0 || $lower === 'x-requested-with') {
                $incoming[$name] = $value;
            }
        }

        return array(
            'incoming' => $incoming,
            'outgoing' => $this->sentHeaders,
            'context'  => array_merge($this->requestContext(), $this->debugContext)
        );
    }

    /**
     * Preserve HTMX debug mode in outbound parameters.
     *
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    public function preserveDebugParam(array $params, string $name = 'htmx_debug', string $value = '1'): array
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
    public function renderDebugHiddenInput(string $name = 'htmx_debug', string $value = '1'): string
    {
        return ModeSupport::renderHiddenInput($this->isDebugEnabled(), $name, $value);
    }

    /**
     * Render reusable HTMX debug panel.
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
            'htmx',
            $snapshot,
            $options,
            'htmx-debug-panel',
            'HTMX Debug',
            'htmx_debug_panel_',
            'hz-htmx-debug-panel',
            'hz-htmx-debug-bar',
            'hz-htmx-debug-actions'
        );
    }

    /**
     * Emit debug header immediately (if debug mode enabled).
     *
     * @return  void
     */
    public function emitDebugHeader(): void
    {
        $this->emitDebugHeaderIfEnabled();
    }

    /**
     * Emit profile header/log output immediately when enabled.
     *
     * @return  void
     */
    public function emitProfileHeader(): void
    {
        $this->emitProfileHeaderIfEnabled();
    }

    /**
     * Emit an optional compact HTMX debug header.
     *
     * @return  void
     */
    protected function emitDebugHeaderIfEnabled(): void
    {
        if (!$this->isDebugEnabled()) {
            return;
        }

        $encoded = HeaderSupport::encodeJson($this->debugSnapshot(), '{"error":"debug_encode_failed"}', 3500);
        HeaderSupport::emitHeader('X-Hubzero-Htmx-Debug', $encoded);
        $this->emitProfileHeaderIfEnabled();
    }

    /**
     * Emit an optional compact HTMX profile header and log line.
     *
     * @return  void
     */
    protected function emitProfileHeaderIfEnabled(): void
    {
        if (!$this->isDebugEnabled() || empty($this->profile)) {
            return;
        }

        $encoded = HeaderSupport::encodeJson($this->profile, '{"error":"profile_encode_failed"}');
        HeaderSupport::emitHeader('X-Hubzero-Htmx-Profile', $encoded);
        HeaderSupport::logProfile('htmx', $encoded);
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
        if (method_exists($request, 'server')) {
            $ctx['method'] = (string) $request->server('REQUEST_METHOD', '');
            $ctx['uri'] = (string) $request->server('REQUEST_URI', '');
        }

        if (method_exists($request, 'get')) {
            $ctx['option'] = (string) $request->get('option', '');
            $ctx['task'] = (string) $request->get('task', '');
        }

        return $ctx;
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
     * Parse HX-Triggering-Event JSON header payload.
     *
     * @return  array|null
     */
    protected function parseTriggeringEvent(): ?array
    {
        $raw = $this->requestHeaderValue('HX-Triggering-Event');
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    /**
     * Resolve current request origin (scheme, host, port).
     *
     * @return  array|null
     */
    protected function requestOrigin(): ?array
    {
        $request = \App::get('request');

        $scheme = '';
        $host = '';
        $port = null;

        if ($request && method_exists($request, 'server')) {
            $scheme = strtolower(trim((string) $request->server('REQUEST_SCHEME', '')));
            $https = strtolower(trim((string) $request->server('HTTPS', '')));
            if ($scheme === '' && ($https === 'on' || $https === '1')) {
                $scheme = 'https';
            }

            $host = trim((string) $request->server('HTTP_HOST', ''));
            if ($host === '') {
                $host = trim((string) $request->server('SERVER_NAME', ''));
            }

            $serverPort = (int) $request->server('SERVER_PORT', 0);
            if ($serverPort > 0) {
                $port = $serverPort;
            }
        }

        if ($scheme === '') {
            $scheme = strtolower(trim((string) ($_SERVER['REQUEST_SCHEME'] ?? '')));
            if ($scheme === '') {
                $https = strtolower(trim((string) ($_SERVER['HTTPS'] ?? '')));
                if ($https === 'on' || $https === '1') {
                    $scheme = 'https';
                }
            }
        }

        if ($host === '') {
            $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        }

        if ($port === null) {
            $serverPort = (int) ($_SERVER['SERVER_PORT'] ?? 0);
            if ($serverPort > 0) {
                $port = $serverPort;
            }
        }

        if ($scheme === '') {
            $scheme = 'http';
        }

        if ($host === '') {
            $host = trim($this->requestHeaderValue('Host'));
        }
        if ($host === '') {
            return null;
        }

        $parsedHost = parse_url('http://' . $host);
        if ($parsedHost !== false && isset($parsedHost['host'])) {
            $host = strtolower((string) $parsedHost['host']);
            if (isset($parsedHost['port'])) {
                $port = (int) $parsedHost['port'];
            }
        } else {
            $host = strtolower($host);
        }

        if ($port === null || $port <= 0) {
            $port = $this->defaultPortForScheme($scheme);
        }

        if ($port === null) {
            return null;
        }

        return array(
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
        );
    }

    /**
     * @param   string  $scheme
     * @return  int|null
     */
    protected function defaultPortForScheme(string $scheme): ?int
    {
        $scheme = strtolower(trim($scheme));
        if ($scheme === 'https') {
            return 443;
        }
        if ($scheme === 'http') {
            return 80;
        }

        return null;
    }

    /**
     * Validate selector values used in HTMX selector headers.
     *
     * @param   string  $selector
     * @return  bool
     */
    protected function isValidSelectorHeaderValue(string $selector): bool
    {
        if ($selector === '') {
            return false;
        }

        // Prevent malformed header values and obvious HTML/script payloads.
        if (
            strlen($selector) > 512
            || strpos($selector, "\r") !== false
            || strpos($selector, "\n") !== false
            || strpos($selector, "\0") !== false
            || strpos($selector, '<') !== false
            || strpos($selector, '>') !== false
        ) {
            return false;
        }

        return true;
    }
}
