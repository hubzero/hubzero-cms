<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Disable cookie encryption for HubZero session cookies so the
        // legacy entry point (port 8443) and Laravel (port 9443) can share
        // session cookies on localhost without conflicts.
        // Site cookie: md5(md5($secret . 'site'))
        // Admin cookie: md5(md5($secret . 'administrator'))
        $hubzeroApp = file_exists(base_path('app/config/app.php'))
            ? (array) require base_path('app/config/app.php')
            : [];
        $secret = $hubzeroApp['secret'] ?? '';
        $except = ['hubzero_session'];
        if ($secret) {
            $except[] = md5(md5($secret . 'site'));
            $except[] = md5(md5($secret . 'administrator'));
        }
        $middleware->encryptCookies(except: $except);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Redirect CSRF token mismatches back to the previous page with
        // a user-friendly notification instead of Laravel's generic 419 page.
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, $request) {
            if ($response->getStatusCode() !== 419) {
                return $response;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session has expired. Please try again.'], 419);
            }

            $back = $request->header('referer', url()->previous('/'));

            return redirect($back)
                ->withInput()
                ->with('warning', 'Your session has expired. Please try again.');
        });
    })->create();
