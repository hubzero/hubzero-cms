<?php

namespace Hubzero\Framework\Facades\Services;

use Hubzero\Framework\Config\Registry;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bridges HubZero's App::get() API to Laravel's container.
 */
class AppService
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
            },
            'client' => new class {
                public string $name = 'site';
                public string $alias = 'site';
                public int $id = 0;
            },
            'session' => new class {
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
                    // CSRF is handled by Laravel's VerifyCsrfToken middleware
                    return true;
                }
            },
            'module' => $this->app->make('hubzero.module'),
            'template' => (object) [
                'path' => base_path('app/templates/hubzero'),
                'template' => 'hubzero',
            ],
            'auth' => new \Hubzero\Framework\Auth\AuthManagerBridge(),
            'config' => $this->app->make('config'),
            'app' => $this,
            default => $this->app->bound($key) ? $this->app->make($key) : $default,
        };
    }

    public function has(string $key): bool
    {
        return match ($key) {
            'request', 'document', 'pathway', 'language', 'component',
            'notification', 'html.builder', 'user', 'router', 'date', 'filesystem',
            'plugin', 'dispatcher', 'db', 'menu', 'menu.params', 'cache.store', 'client',
            'session', 'module', 'template', 'auth', 'config', 'app' => true,
            default => $this->app->bound($key),
        };
    }

    public function set(string $key, mixed $value): void
    {
        $this->app->instance($key, $value);
    }

    public function isSite(): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return false;
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
}
