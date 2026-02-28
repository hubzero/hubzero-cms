<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework;

use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Framework\Auth\HubzeroHasher;
use Hubzero\Framework\Auth\HubzeroUserProvider;
use Hubzero\Framework\Component\Loader;
use Hubzero\Framework\Module;
use Hubzero\Framework\Document\Document;
use Hubzero\Framework\Facades\FacadeRegistrar;
use Hubzero\Framework\Facades\Services\AppService;
use Hubzero\Framework\Facades\Services\ComponentService;
use Hubzero\Framework\Facades\Services\LangService;
use Hubzero\Framework\Facades\Services\MenuService;
use Hubzero\Framework\Facades\Services\NotifyService;
use Hubzero\Framework\Facades\Services\UserService;
use Hubzero\Framework\Http\AdminClientMiddleware;
use Hubzero\Framework\Http\ComponentDispatchController;
use Hubzero\Framework\Http\HubzeroRequest;
use Hubzero\Framework\Pathway\Pathway;
use Hubzero\Framework\Session\HubzeroSessionHandler;
use Hubzero\Html\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class FrameworkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HubzeroHasher::class);

        // Client context — defaults to site; AdminClientMiddleware swaps to admin
        $this->app->singleton('hubzero.client', fn () => (object) [
            'name' => 'site',
            'alias' => 'site',
            'id' => 0,
        ]);

        // HubZero services
        $this->app->singleton('hubzero.document', fn () => new Document());
        $this->app->singleton('hubzero.pathway', fn () => new Pathway());
        $this->app->singleton('hubzero.request', fn ($app) => new HubzeroRequest($app['request']));
        $this->app->singleton('hubzero.component', fn ($app) => new Loader($app));
        $this->app->singleton('hubzero.notify', fn () => new NotifyService());
        $this->app->singleton('hubzero.html', fn () => new Builder());

        // Toolbar instances used by admin facades (Toolbar:: and Submenu::)
        $this->app->singleton('toolbar', fn () => new \Hubzero\Html\Toolbar('toolbar'));
        $this->app->singleton('submenu', fn () => new \Hubzero\Html\Toolbar('submenu'));

        // Facade backing services
        $this->app->singleton('hubzero.app', fn ($app) => new AppService($app));
        $this->app->singleton('hubzero.lang', fn () => new LangService());
        $this->app->singleton('hubzero.component.facade', fn () => new ComponentService());
        $this->app->singleton('hubzero.user', fn () => new UserService());
        $this->app->singleton('hubzero.menu', fn () => new MenuService());

        // Module loader — based on legacy Hubzero\Module\Loader but works
        // directly with our framework services instead of the legacy Container.
        $this->app->singleton('hubzero.module', fn ($app) =>
            new Module\ModuleLoader($app)
        );

        // Event dispatcher with plugin lazy-loading
        $this->app->singleton('hubzero.dispatcher', function ($app) {
            $dispatcher = new \Hubzero\Events\Dispatcher();
            $dispatcher->addListenerLoader(new \Hubzero\Plugin\Loader());
            return $dispatcher;
        });

        // Plugin loader for direct plugin queries (import, byType, params)
        $this->app->singleton('hubzero.plugin', fn () => new \Hubzero\Plugin\Loader());
    }

    public function boot(): void
    {
        // Custom auth provider for HubZero password hashes
        Auth::provider('hubzero', function ($app, array $config) {
            return new HubzeroUserProvider(
                $app->make(HubzeroHasher::class),
                $config['model']
            );
        });

        // Custom session driver for jos_session table
        Session::extend('hubzero', function ($app) {
            return new HubzeroSessionHandler(
                config('session.lifetime', 120)
            );
        });

        // Legacy constants
        if (!defined('_HZEXEC_')) {
            define('_HZEXEC_', 1);
        }
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        if (!defined('PATH_ROOT')) {
            define('PATH_ROOT', base_path());
        }
        if (!defined('PATH_CORE')) {
            define('PATH_CORE', base_path('core'));
        }
        if (!defined('PATH_APP')) {
            define('PATH_APP', base_path('app'));
        }
        if (!defined('HVERSION')) {
            define('HVERSION', '3.0.0');
        }

        // Register facade aliases (our framework facades)
        FacadeRegistrar::register();

        // Ensure ClassLoader has base directories for extension autoloading
        // (Components\*, Modules\*, Plugins\*, Templates\*)
        \Hubzero\Base\ClassLoader::addDirectories([PATH_APP, PATH_CORE]);
        \Hubzero\Base\ClassLoader::register();

        // Initialize legacy Facade system so legacy Hubzero\Facades\* classes
        // can resolve their services through AppService::get()
        \Hubzero\Facades\Facade::setApplication($this->app->make('hubzero.app'));

        // Wire HubZero Relational ORM to Laravel's database config
        $this->bootRelationalOrm();

        // Admin routes — AdminClientMiddleware runs BEFORE web group so it
        // can set the admin session cookie name before StartSession reads it.
        Route::prefix('admin')
            ->middleware([AdminClientMiddleware::class, 'web'])
            ->group(function () {
                Route::any('/{path?}', [ComponentDispatchController::class, 'adminDispatch'])
                    ->where('path', '.*');
            });

        // Fallback route for legacy site component dispatch.
        // Only matches when no explicit route handles the request.
        // Uses Route::any() so POST (form submissions) also work.
        Route::fallback([ComponentDispatchController::class, 'dispatch'])
            ->middleware('web');
        Route::post('{fallbackPlaceholder}', [ComponentDispatchController::class, 'dispatch'])
            ->where('fallbackPlaceholder', '.*')
            ->middleware('web');
    }

    private function bootRelationalOrm(): void
    {
        $connection = config('database.default', 'mysql');
        $dbConfig = config("database.connections.{$connection}");

        if (!$dbConfig) {
            return;
        }

        $driverName = $dbConfig['driver'] ?? 'mysql';
        if ($driverName === 'pdo') {
            $driverName = 'mysql';
        }

        $driver = Driver::getInstance([
            'driver'   => $driverName,
            'host'     => $dbConfig['host'] ?? '127.0.0.1',
            'user'     => $dbConfig['username'] ?? '',
            'password' => $dbConfig['password'] ?? '',
            'database' => $dbConfig['database'] ?? '',
            'prefix'   => $dbConfig['prefix'] ?? 'jos_',
        ]);

        Relational::setDefaultConnection($driver);

        Relational::setUserIdResolver(function () {
            return Auth::id() ?? 0;
        });
    }
}
