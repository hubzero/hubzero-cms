<?php

namespace Hubzero\Framework\Facades;

/**
 * HubZero's legacy Route facade.
 *
 * Cannot be aliased globally as 'Route' because that conflicts with
 * Laravel's Route facade. Instead, the LegacyTemplateRenderer registers
 * this as 'Route' only within the template execution scope.
 */
class LegacyRoute
{
    /**
     * Generate a URL from a HubZero-style query string.
     *
     * Resolves Itemid to SEF paths via the menu table, and converts
     * index.php?option=com_xxx URLs to /xxx paths.
     */
    public static function url(string $url, bool $xhtml = true, ?int $ssl = null): string
    {
        if (!str_starts_with($url, 'index.php')) {
            return $url;
        }

        // Bare 'index.php' with no query → return base URL
        if ($url === 'index.php') {
            $base = rtrim(request()->root(), '/') . '/';
            return $base;
        }

        parse_str(substr($url, 10), $params);

        // If we have an Itemid, look up the menu item's SEF path
        if (!empty($params['Itemid'])) {
            $path = static::resolveItemId((int) $params['Itemid'], $params);
            if ($path !== null) {
                return $path;
            }
        }

        // Fall back to component-based URL
        $option = $params['option'] ?? '';
        if ($option && str_starts_with($option, 'com_')) {
            $component = substr($option, 4);

            // Try to find a menu item for this component to get its path
            $menuPath = static::findMenuPath($option, $params);
            if ($menuPath !== null) {
                return $menuPath;
            }

            unset($params['option'], $params['Itemid']);

            // Try the component's own router to build SEF segments
            $segments = static::buildComponentSegments($option, $params);
            if ($segments !== null) {
                $path = '/' . $component;
                if (!empty($segments)) {
                    $path .= '/' . implode('/', $segments);
                }
                if (!empty($params)) {
                    $path .= '?' . http_build_query($params);
                }
                return $path;
            }

            $path = '/' . $component;
            if (!empty($params)) {
                $path .= '?' . http_build_query($params);
            }
            return $path;
        }

        return $url;
    }

    /**
     * Resolve an Itemid to a SEF URL path via the menu service.
     */
    private static function resolveItemId(int $itemId, array $params): ?string
    {
        if (!app()->bound('hubzero.menu')) {
            return null;
        }

        $menu = app('hubzero.menu');
        $item = $menu->getItem($itemId);

        if (!$item || !isset($item->path)) {
            return null;
        }

        // For the home page, return /
        if (!empty($item->home)) {
            return '/';
        }

        // Build the path from the menu item's path column
        $path = '/' . ltrim($item->path, '/');

        // If there are extra query params beyond option/Itemid,
        // append them (e.g., task=xxx, id=xxx)
        unset($params['option'], $params['Itemid']);

        // Also remove params that are already in the menu item's link
        if (!empty($item->query)) {
            foreach ($item->query as $k => $v) {
                if (isset($params[$k]) && $params[$k] == $v) {
                    unset($params[$k]);
                }
            }
        }

        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }

    /** @var array<string, object|false> Cached component routers */
    private static array $routers = [];

    /**
     * Try the component's own router to build SEF URL segments.
     *
     * Component routers live at core/components/com_xxx/site/router.php
     * and implement a build() method that converts query params to
     * path segments.
     */
    private static function buildComponentSegments(string $option, array &$params): ?array
    {
        if (!isset(self::$routers[$option])) {
            self::$routers[$option] = static::loadComponentRouter($option);
        }

        $router = self::$routers[$option];
        if ($router === false) {
            return null;
        }

        try {
            $query = $params;
            $segments = $router->build($query);

            if (!is_array($segments)) {
                return null;
            }

            // The router modifies $query by reference, removing consumed params
            $params = $query;
            return array_filter($segments, fn($v) => $v !== '' && $v !== null);
        } catch (\Throwable $e) {
            \Log::debug("LegacyRoute: component router for {$option} failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Load a component's router class directly from its file.
     *
     * @return object|false
     */
    private static function loadComponentRouter(string $option): object|false
    {
        $compName = ucfirst(substr($option, 4));
        $className = "\\Components\\{$compName}\\Site\\Router";

        // Try to load the router file
        $paths = [
            base_path("app/components/{$option}/site/router.php"),
            base_path("core/components/{$option}/site/router.php"),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                break;
            }
        }

        if (class_exists($className)) {
            try {
                return new $className();
            } catch (\Throwable $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Try to find a menu item matching a component option and use its path.
     */
    private static function findMenuPath(string $option, array &$params): ?string
    {
        if (!app()->bound('hubzero.menu')) {
            return null;
        }

        $menu = app('hubzero.menu');
        $items = $menu->getItems('component', $option);

        if (empty($items)) {
            return null;
        }

        // Find the best match: prefer items whose query params match
        $bestItem = null;
        $bestScore = -1;

        foreach ($items as $item) {
            if (empty($item->path)) {
                continue;
            }

            $score = 0;
            $itemQuery = $item->query ?? [];

            // Score by how many query params match
            foreach ($itemQuery as $k => $v) {
                if ($k === 'option') {
                    continue;
                }
                if (isset($params[$k]) && $params[$k] == $v) {
                    $score++;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestItem = $item;
            }
        }

        if (!$bestItem) {
            return null;
        }

        $path = '/' . ltrim($bestItem->path, '/');

        // Remove matched query params
        unset($params['option'], $params['Itemid']);
        if (!empty($bestItem->query)) {
            foreach ($bestItem->query as $k => $v) {
                if (isset($params[$k]) && $params[$k] == $v) {
                    unset($params[$k]);
                }
            }
        }

        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }
}
