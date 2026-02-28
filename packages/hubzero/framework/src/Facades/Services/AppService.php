<?php

namespace Hubzero\Framework\Facades\Services;

use Hubzero\Framework\Config\Registry;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bridges HubZero's App::get() API to Laravel's container.
 */
class AppService implements \ArrayAccess
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return match ($key) {
            'request' => $this->app->make('hubzero.request'),
            'document' => $this->app->make('hubzero.document'),
            'pathway' => $this->app->make('hubzero.pathway'),
            // Legacy facade accessor keys
            'language' => $this->app->make('hubzero.lang'),
            'component' => $this->app->make('hubzero.component.facade'),
            'notification' => $this->app->make('hubzero.notify'),
            'html.builder' => $this->app->make('hubzero.html'),
            'user' => $this->app->make('hubzero.user'),
            'response' => new \Symfony\Component\HttpFoundation\Response(),
            'router' => new \Hubzero\Framework\Facades\LegacyRoute(),
            'date' => new \Hubzero\Framework\Facades\DateFacade(),
            'filesystem' => new class {
                public function extension(string $file): string
                {
                    return pathinfo($file, PATHINFO_EXTENSION);
                }

                public function exists(string $path): bool
                {
                    return file_exists($path);
                }

                public function files(string $path, string $filter = '.', bool $recurse = false, bool $full = false): array
                {
                    if (!is_dir($path)) {
                        return [];
                    }
                    $results = [];
                    foreach (scandir($path) as $file) {
                        if ($file === '.' || $file === '..') {
                            continue;
                        }
                        $fullPath = $path . '/' . $file;
                        if (is_file($fullPath) && preg_match('/' . $filter . '/', $file)) {
                            $results[] = $full ? $fullPath : $file;
                        }
                    }
                    return $results;
                }

                public function name(string $file): string
                {
                    return pathinfo($file, PATHINFO_FILENAME);
                }

                public function read(string $path): string
                {
                    return is_file($path) ? file_get_contents($path) : '';
                }

                public function write(string $path, string $data): bool
                {
                    return file_put_contents($path, $data) !== false;
                }

                public function delete(string $path): bool
                {
                    return is_file($path) && unlink($path);
                }

                public function isDirectory(string $path): bool
                {
                    return is_dir($path);
                }

                public function makeDirectory(string $path, int $mode = 0755, bool $recursive = false): bool
                {
                    return is_dir($path) || mkdir($path, $mode, $recursive);
                }

                public function cleanPath(string $path): string
                {
                    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
                    return preg_replace('#' . preg_quote(DIRECTORY_SEPARATOR) . '+#', DIRECTORY_SEPARATOR, $path);
                }

                public function directories(string $path, string $filter = '.', bool $recurse = false, bool $full = false): array
                {
                    if (!is_dir($path)) {
                        return [];
                    }
                    $results = [];
                    foreach (scandir($path) as $item) {
                        if ($item === '.' || $item === '..') {
                            continue;
                        }
                        $fullPath = $path . '/' . $item;
                        if (is_dir($fullPath) && preg_match('/' . $filter . '/', $item)) {
                            $results[] = $full ? $fullPath : $item;
                        }
                    }
                    return $results;
                }
            },
            'plugin' => $this->app->make('hubzero.plugin'),
            'dispatcher' => $this->app->make('hubzero.dispatcher'),
            'db' => $this->getDbDriver(),
            'menu' => $this->app->make('hubzero.menu'),
            'menu.params' => new Registry(),
            'cache.store' => new class {
                public function get(string $key): mixed
                {
                    return cache()->get($key);
                }

                public function put(string $key, mixed $value, int $minutes = 15): bool
                {
                    cache()->put($key, $value, $minutes * 60);
                    return true;
                }

                public function has(string $key): bool
                {
                    return cache()->has($key);
                }

                public function forget(string $key): bool
                {
                    return cache()->forget($key);
                }

                public function clean(?string $group = null): bool
                {
                    cache()->flush();
                    return true;
                }
            },
            'client' => $this->app->bound('hubzero.client')
                ? $this->app->make('hubzero.client')
                : (object) ['name' => 'site', 'alias' => 'site', 'id' => 0],
            'session' => new class {
                public function get(string $key, mixed $default = null): mixed
                {
                    return session($key, $default);
                }

                public function set(string $key, mixed $value = null): void
                {
                    session([$key => $value]);
                }

                public function has(string $key): bool
                {
                    return session()->has($key);
                }

                public function clear(string $key): void
                {
                    session()->forget($key);
                }

                public function getFormToken(): string
                {
                    return csrf_token();
                }

                public function getToken(): string
                {
                    return csrf_token();
                }

                public function checkToken(string $method = 'post'): bool
                {
                    return true;
                }

                public function getStore(): object
                {
                    return new class {
                        public function session($id)
                        {
                            return \Illuminate\Support\Facades\DB::table('session')
                                ->where('session_id', $id)
                                ->first();
                        }

                        public function all(array $filters = []): array
                        {
                            $query = \Illuminate\Support\Facades\DB::table('session');

                            if (!empty($filters['client_id'])) {
                                $query->where('client_id', $filters['client_id']);
                            }
                            if (!empty($filters['guest'])) {
                                $query->where('guest', $filters['guest']);
                            }

                            return $query->get()->all();
                        }
                    };
                }
            },
            'module' => $this->app->make('hubzero.module'),
            'template' => $this->getTemplateInfo(),
            'auth' => new \Hubzero\Framework\Auth\AuthManagerBridge(),
            'editor' => \Hubzero\Html\Editor::getInstance(
                config('hubzero.app.editor', 'none')
            ),
            'config' => $this->getLegacyConfig(),
            'app' => $this,
            default => $this->app->bound($key) ? $this->app->make($key) : $default,
        };
    }

    public function has(string $key): bool
    {
        return match ($key) {
            'request', 'document', 'pathway', 'language', 'component',
            'notification', 'html.builder', 'user', 'response', 'router', 'date', 'filesystem',
            'plugin', 'dispatcher', 'db', 'menu', 'menu.params', 'cache.store', 'client',
            'session', 'module', 'template', 'auth', 'editor', 'config', 'app' => true,
            default => $this->app->bound($key),
        };
    }

    public function set(string $key, mixed $value): void
    {
        if ($value instanceof \Closure) {
            $this->app->singleton($key, $value);
        } else {
            $this->app->instance($key, $value);
        }
    }

    /**
     * Return the root application object. Legacy code uses this
     * to pass the container to services like Cache\Manager.
     */
    public function getRoot(): static
    {
        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set((string) $offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->forget((string) $offset);
    }

    public function isSite(): bool
    {
        return !$this->app->bound('hubzero.client')
            || $this->app->make('hubzero.client')->id === 0;
    }

    public function isAdmin(): bool
    {
        return $this->app->bound('hubzero.client')
            && $this->app->make('hubzero.client')->id === 1;
    }

    public function abort(int $code, string $message = ''): never
    {
        abort($code, $message);
    }

    public function hash(string $seed): string
    {
        return md5(config('app.key', '') . $seed);
    }

    public function redirect(string $url, ?string $message = null, string $type = 'success'): never
    {
        if ($message && $this->app->bound('hubzero.notify')) {
            $this->app->make('hubzero.notify')->message($message, $type);
        }

        // Throw an exception to break out of legacy code and return a
        // proper Laravel redirect response through the middleware pipeline.
        throw new \Hubzero\Framework\Http\LegacyRedirectException($url ?: '/');
    }

    public function forget(string $key): void
    {
        $this->app->forgetInstance($key);
    }

    /**
     * Get the HubZero Database Driver singleton.
     *
     * Uses the same config as bootRelationalOrm() so getInstance()
     * returns the existing connection by matching the signature hash.
     */
    private function getDbDriver(): \Hubzero\Database\Driver
    {
        $connection = config('database.default', 'mysql');
        $dbConfig = config("database.connections.{$connection}");
        $driverName = $dbConfig['driver'] ?? 'mysql';
        if ($driverName === 'pdo') {
            $driverName = 'mysql';
        }

        return \Hubzero\Database\Driver::getInstance([
            'driver'   => $driverName,
            'host'     => $dbConfig['host'] ?? '127.0.0.1',
            'user'     => $dbConfig['username'] ?? '',
            'password' => $dbConfig['password'] ?? '',
            'database' => $dbConfig['database'] ?? '',
            'prefix'   => $dbConfig['prefix'] ?? 'jos_',
        ]);
    }

    private function getTemplateInfo(): object
    {
        $isAdmin = $this->app->bound('hubzero.client')
            && $this->app->make('hubzero.client')->id === 1;

        if ($isAdmin) {
            return (object) [
                'path' => base_path('core/templates/kameleon'),
                'template' => 'kameleon',
            ];
        }

        return (object) [
            'path' => base_path('app/templates/hubzero'),
            'template' => 'hubzero',
        ];
    }

    /**
     * Return a config wrapper that maps legacy flat keys to Laravel config.
     *
     * Legacy code uses $config->get('sitename'), $config->get('MetaDesc'), etc.
     */
    private function getLegacyConfig(): object
    {
        return new class implements \ArrayAccess {
            private static array $keyMap = [
                'sitename' => 'app.name',
                'MetaDesc' => 'hubzero.meta.MetaDesc',
                'MetaKeys' => 'hubzero.meta.MetaKeys',
                'offline' => 'hubzero.app.offline',
                'offline_message' => 'hubzero.app.offline_message',
                'debug' => 'app.debug',
                'secret' => 'app.key',
                'sef' => 'hubzero.seo.sef',
                'sef_rewrite' => 'hubzero.seo.sef_rewrite',
                'sef_suffix' => 'hubzero.seo.sef_suffix',
                'list_limit' => 'hubzero.app.list_limit',
                'feed_limit' => 'hubzero.app.feed_limit',
                'session_lifetime' => 'session.lifetime',
            ];

            public function get(?string $key = null, mixed $default = null): mixed
            {
                if ($key === null || $key === '') {
                    return $default;
                }
                $laravelKey = self::$keyMap[$key] ?? null;
                if ($laravelKey) {
                    return config($laravelKey, $default);
                }
                // Try hubzero.app.{key} as fallback
                $value = config('hubzero.app.' . $key);
                if ($value !== null) {
                    return $value;
                }
                // Defaults for keys that legacy code expects
                return match ($key) {
                    'cache_handler' => 'file',
                    default => $default,
                };
            }

            public function set(?string $key, mixed $value): void
            {
                if ($key === null) {
                    return;
                }
                $laravelKey = self::$keyMap[$key] ?? 'hubzero.app.' . $key;
                config([$laravelKey => $value]);
            }

            public function offsetExists(mixed $offset): bool
            {
                return $this->get((string) $offset) !== null;
            }

            public function offsetGet(mixed $offset): mixed
            {
                return $this->get((string) $offset);
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->set((string) $offset, $value);
            }

            public function offsetUnset(mixed $offset): void
            {
                $this->set((string) $offset, null);
            }
        };
    }
}
