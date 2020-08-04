<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api;

use Hubzero\Base\ServiceProvider;

/**
 * API response service provider
 */
class ResponseServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app->forget('response');

		$this->app['response'] = function($app)
		{
			return new Response();
		};
	}

	/**
	 * Force debugging off.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if (function_exists('xdebug_disable'))
		{
			xdebug_disable();
		}
		ini_set('zlib.output_compression', '0');
		ini_set('output_handler', '');
		ini_set('implicit_flush', '0');

		$this->app['config']->set('debug', 0);
		$this->app['config']->set('debug_lang', 0);

		static $types = array(
			'xml'   => 'application/xml',
			'html'  => 'text/html',
			'xhtml' => 'application/xhtml+xml',
			'json'  => 'application/json',
			'text'  => 'text/plain',
			'txt'   => 'text/plain',
			'plain' => 'text/plain',
			'php'   => 'application/php',
			'php_serialized' => 'application/vnd.php.serialized'
		);

		$format = $this->app['request']->getWord('format', 'json');
		$format = (isset($types[$format]) ? $format : 'json');

		$this->app['response']->setStatusCode(404);
		$this->app['response']->headers->addCacheControlDirective('no-store', true);
		$this->app['response']->headers->addCacheControlDirective('must-revalidate', true);
		$this->app['response']->headers->set('Content-Type', $types[$format]);
	}
}
