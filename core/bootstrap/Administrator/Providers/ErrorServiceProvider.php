<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Error\Handler;
use Hubzero\Error\Renderer\Page;
use Hubzero\Error\Renderer\Plain;
use Hubzero\Error\Renderer\Api;
use Hubzero\Base\ServiceProvider;

/**
 * Error handler service provider
 */
class ErrorServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['error'] = function ($app) {
            $logger = new \Monolog\Logger('cms');

            // Log to php's `error_log()`
            $loghandler = new \Monolog\Handler\ErrorLogHandler();

            $formatter = new \Monolog\Formatter\LineFormatter(
                "%channel%.%level_name%: %message% %context% %extra%",
                // Format of message in log, default [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n
                null, // Datetime format
                true,
                // allowInlineLineBreaks option, default false
                true  // discard empty Square brackets in the end, default false
            );

            $loghandler->setFormatter($formatter);

            // Alternatively, if you need to a specified file
            //$loghandler = new \Monolog\Handler\StreamHandler($app['config']->get('log_path') . '/error.php',
            //                                                    'error', true);

            $logger->pushHandler($loghandler);

            $handler = new Handler(
                new Plain($app['config']->get('debug')),
                $logger
            );

            return $handler;
        };
    }

    /**
     * Register the exception handler.
     *
     * @return  void
     */
    public function startHandling()
    {
        // Set the error_reporting
        switch ($this->app['config']->get('error_reporting', 0)) {
            case 'default':
                break;

            case 'none':
            case '0':
                error_reporting(0);
                break;

            case 'simple':
                $this->app['config']->set('error_reporting', 'relaxed');
                // no break
            case 'relaxed':
                error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
                break;

            case 'maximum':
            case 'development':
            case '-1':
                error_reporting(E_ALL);
                break;

            default:
                error_reporting($this->app['config']->get('error_reporting', 0));
                break;
        }

        $this->app['error']->register($this->app['client']->name);
    }

    /**
     * Register the exception handler.
     *
     * @return  void
     */
    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            $this->app['error']->setRenderer(new Page(
                $this->app['document'],
                $this->app['template.loader'],
                $this->app['config']->get('debug')
            ));
        }
    }
}
