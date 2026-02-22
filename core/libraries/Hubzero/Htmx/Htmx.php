<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx;

/**
 * Concrete static HTMX facade.
 */
class Htmx
{
    /**
     * @return  HtmxService
     */
    protected static function service(): HtmxService
    {
        $app = \App::get('app');

        if ($app->has('htmx')) {
            $service = $app->get('htmx');
            if ($service instanceof HtmxService) {
                return $service;
            }
        }

        $service = new HtmxService();
        $app->set('htmx', $service);

        return $service;
    }

    /**
     * @return  bool
     */
    public static function isHtmxRequest(): bool
    {
        return self::service()->isHtmxRequest();
    }

    /**
     * @return  bool
     */
    public static function isHtmxBoosted(): bool
    {
        return self::service()->isHtmxBoosted();
    }

    /**
     * @return  bool
     */
    public static function isHtmxHistoryRestoreRequest(): bool
    {
        return self::service()->isHtmxHistoryRestoreRequest();
    }

    /**
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public static function state($key, $value = null): void
    {
        self::service()->state($key, $value);
    }

    /**
     * @param   string|null  $key
     * @param   mixed        $default
     * @return  mixed
     */
    public static function getState(?string $key = null, $default = null)
    {
        return self::service()->getState($key, $default);
    }

    /**
     * @param   string  $id
     * @return  string
     */
    public static function renderStateNode(string $id = 'hx-state'): string
    {
        return self::service()->renderStateNode($id);
    }

    /**
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public static function debugContext($key, $value = null): void
    {
        self::service()->debugContext($key, $value);
    }

    /**
     * @return  bool
     */
    public static function isDebugEnabled(): bool
    {
        return self::service()->isDebugEnabled();
    }

    /**
     * @return  array
     */
    public static function debugSnapshot(): array
    {
        return self::service()->debugSnapshot();
    }

    /**
     * @return  void
     */
    public static function emitDebugHeader(): void
    {
        self::service()->emitDebugHeader();
    }

    /**
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    public static function trigger(string $event, $payload = array()): void
    {
        self::service()->trigger($event, $payload);
    }

    /**
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    public static function triggerAfterSettle(string $event, $payload = array()): void
    {
        self::service()->triggerAfterSettle($event, $payload);
    }

    /**
     * @param   string       $event
     * @param   array|mixed  $payload
     * @return  void
     */
    public static function triggerAfterSwap(string $event, $payload = array()): void
    {
        self::service()->triggerAfterSwap($event, $payload);
    }

    /**
     * @param   string  $url
     * @return  void
     */
    public static function redirect(string $url): void
    {
        self::service()->redirect($url);
    }

    /**
     * @param   string  $url
     * @param   array   $options
     * @return  void
     */
    public static function location(string $url, array $options = array()): void
    {
        self::service()->location($url, $options);
    }

    /**
     * @param   string  $strategy
     * @return  void
     */
    public static function reswap(string $strategy): void
    {
        self::service()->reswap($strategy);
    }

    /**
     * @param   string  $selector
     * @return  void
     */
    public static function retarget(string $selector): void
    {
        self::service()->retarget($selector);
    }

    /**
     * @param   string|bool  $url
     * @return  void
     */
    public static function pushUrl($url): void
    {
        self::service()->pushUrl($url);
    }

    /**
     * @param   string|bool  $url
     * @return  void
     */
    public static function replaceUrl($url): void
    {
        self::service()->replaceUrl($url);
    }

    /**
     * @return  void
     */
    public static function refresh(): void
    {
        self::service()->refresh();
    }

    /**
     * @param   string  $selector
     * @return  void
     */
    public static function reselect(string $selector): void
    {
        self::service()->reselect($selector);
    }

    /**
     * @return  void
     */
    public static function varyOnRequest(): void
    {
        self::service()->varyOnRequest();
    }

    /**
     * @return  array
     */
    public static function requestDetails(): array
    {
        return self::service()->requestDetails();
    }

    /**
     * @return  string|null
     */
    public static function currentUrlAbsPath(): ?string
    {
        return self::service()->currentUrlAbsPath();
    }

    /**
     * @param   string  $url
     * @param   array   $params
     * @return  string
     */
    public static function url(string $url, array $params = array()): string
    {
        return self::service()->url($url, $params);
    }

    /**
     * @param   string  $component
     * @param   string  $task
     * @param   array   $params
     * @return  string
     */
    public static function actionUrl(string $component, string $task, array $params = array()): string
    {
        return self::service()->actionUrl($component, $task, $params);
    }

    /**
     * @param   string  $html
     * @return  void
     */
    public static function fragment(string $html, int $status = 200): void
    {
        self::service()->fragment($html, $status);
    }

    /**
     * @return  void
     */
    public static function noContent(): void
    {
        self::service()->noContent();
    }

    /**
     * @return  void
     */
    public static function stopPolling(): void
    {
        self::service()->stopPolling();
    }

    /**
     * @param   string  $html
     * @param   array   $errors
     * @return  void
     */
    public static function validation(string $html, array $errors = array()): void
    {
        self::service()->validation($html, $errors);
    }

    /**
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    public static function preserveDebugParam(array $params, string $name = 'htmx_debug', string $value = '1'): array
    {
        return self::service()->preserveDebugParam($params, $name, $value);
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    public static function renderDebugHiddenInput(string $name = 'htmx_debug', string $value = '1'): string
    {
        return self::service()->renderDebugHiddenInput($name, $value);
    }

    /**
     * @param   array  $snapshot
     * @param   array  $options
     * @return  string
     */
    public static function renderDebugPanel(array $snapshot = array(), array $options = array()): string
    {
        return self::service()->renderDebugPanel($snapshot, $options);
    }

    /**
     * @param   string  $label
     * @return  void
     */
    public static function profileStart(string $label): void
    {
        self::service()->profileStart($label);
    }

    /**
     * @param   string  $label
     * @return  void
     */
    public static function profileStop(string $label): void
    {
        self::service()->profileStop($label);
    }

    /**
     * @return  array
     */
    public static function profileSnapshot(): array
    {
        return self::service()->profileSnapshot();
    }

    /**
     * @return  void
     */
    public static function emitProfileHeader(): void
    {
        self::service()->emitProfileHeader();
    }

    /**
     * @return  void
     */
    public static function flush(): void
    {
        self::service()->flush();
    }
}
