<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia;

/**
 * Concrete static Inertia facade.
 *
 * Uses explicit static methods only (no __callStatic magic dispatch).
 */
class Inertia
{
    /**
     * @return  InertiaService
     */
    protected static function service(): InertiaService
    {
        $app = \Hubzero\Facades\App::get('app');

        if ($app->has('inertia')) {
            $service = $app->get('inertia');
            if ($service instanceof InertiaService) {
                return $service;
            }
        }

        $service = new InertiaService();
        $app->set('inertia', $service);

        return $service;
    }

    /**
     * @return  bool
     */
    public static function isInertiaRequest(): bool
    {
        return self::service()->isInertiaRequest();
    }

    /**
     * @param   string  $component
     * @param   array   $props
     * @return  array|null
     */
    public static function render(string $component, array $props = []): ?array
    {
        return self::service()->render($component, $props);
    }

    /**
     * @param   string  $component
     * @param   array   $props
     * @return  array
     */
    public static function page(string $component, array $props = []): array
    {
        return self::service()->page($component, $props);
    }

    /**
     * @param   string  $component
     * @param   array   $props
     * @param   string  $mountId
     * @return  string
     */
    public static function renderRootNode(string $component, array $props = [], string $mountId = 'app'): string
    {
        return self::service()->renderRootNode($component, $props, $mountId);
    }

    /**
     * @return  string
     */
    public static function url(): string
    {
        return self::service()->url();
    }

    /**
     * @return  string
     */
    public static function version($resolver = null): string
    {
        if (func_num_args() > 0) {
            return self::service()->version($resolver);
        }

        return self::service()->version();
    }

    /**
     * @param   string|array  $key
     * @param   mixed         $value
     * @return  void
     */
    public static function share($key, $value = null): void
    {
        self::service()->share($key, $value);
    }

    /**
     * @param   string|null  $key
     * @param   mixed        $default
     * @return  mixed
     */
    public static function getShared(?string $key = null, $default = null)
    {
        return self::service()->getShared($key, $default);
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
     * @return  array
     */
    public static function requestDetails(): array
    {
        return self::service()->requestDetails();
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
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    public static function preserveDebugParam(array $params, string $name = 'inertia_debug', string $value = '1'): array
    {
        return self::service()->preserveDebugParam($params, $name, $value);
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    public static function renderDebugHiddenInput(string $name = 'inertia_debug', string $value = '1'): string
    {
        return self::service()->renderDebugHiddenInput($name, $value);
    }

    /**
     * @return  void
     */
    public static function emitDebugHeader(): void
    {
        self::service()->emitDebugHeader();
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
    public static function flush(bool $force = false): void
    {
        self::service()->flush($force);
    }

    /**
     * @param   string  $url
     * @return  void
     */
    public static function location(string $url): void
    {
        self::service()->location($url);
    }

    /**
     * @param   string  $url
     * @param   int     $status
     * @return  void
     */
    public static function redirect(string $url, int $status = 302): void
    {
        self::service()->redirect($url, $status);
    }

    /**
     * @param   string  $view
     * @return  void
     */
    public static function rootView(string $view): void
    {
        self::service()->rootView($view);
    }
}
