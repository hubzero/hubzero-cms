<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Error\Handler;
use Hubzero\Error\Renderer\Page;
use Hubzero\Error\Renderer\Plain;
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
		$this->app['error'] = function($app)
		{
			$logger = new \Monolog\Logger('cms');

			// Log to php's `error_log()`
			$loghandler = new \Monolog\Handler\ErrorLogHandler();

			// Alternatively, if you need to a specified file
			//$loghandler = new \Monolog\Handler\StreamHandler($app['config']->get('log_path') . '/error.php', 'error', true);

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
		switch ($this->app['config']->get('error_reporting'))
		{
			case 'default':
			case '-1':
				break;

			case 'none':
			case '0':
				error_reporting(0);
				break;

			case 'simple':
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				ini_set('display_errors', 1);
				break;

			case 'maximum':
				error_reporting(E_ALL);
				ini_set('display_errors', 1);
				break;

			case 'development':
				error_reporting(-1);
				ini_set('display_errors', 1);
				break;

			default:
				error_reporting($this->app['config']->get('error_reporting'));
				ini_set('display_errors', 1);
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
		if (!$this->app->runningInConsole())
		{
			$this->app['error']->setRenderer(new Page(
				$this->app['document'],
				$this->app['template.loader'],
				$this->app['config']->get('debug')
			));
		}
	}
}
