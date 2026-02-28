<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Switches the HubZero bridge services to admin context.
 *
 * Sets hubzero.client to administrator (id=1) and resets the
 * menu and module singletons so they reload with client_id=1.
 */
class AdminClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Switch client context to admin
        app()->instance('hubzero.client', (object) [
            'name' => 'administrator',
            'alias' => 'admin',
            'id' => 1,
        ]);

        // Switch session cookie to the admin cookie name:
        // md5(md5($secret . 'administrator')) — matches legacy admin session
        $hubzeroApp = file_exists(base_path('app/config/app.php'))
            ? (array) require base_path('app/config/app.php')
            : [];
        $secret = $hubzeroApp['secret'] ?? '';
        if ($secret) {
            config(['session.cookie' => md5(md5($secret . 'administrator'))]);
        }

        // Reset menu and module singletons so they reload with client_id=1
        app()->forgetInstance('hubzero.menu');
        app()->forgetInstance('hubzero.module');

        // Also reset LangService so it loads admin language files
        app()->forgetInstance('hubzero.lang');
        app()->singleton('hubzero.lang', fn () =>
            new \Hubzero\Framework\Facades\Services\LangService()
        );

        // Rebind fresh instances that will read the new client context
        app()->singleton('hubzero.menu', fn () =>
            new \Hubzero\Framework\Facades\Services\MenuService()
        );
        app()->singleton('hubzero.module', fn ($app) =>
            new \Hubzero\Framework\Module\ModuleLoader($app)
        );

        return $next($request);
    }
}
