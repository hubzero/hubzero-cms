<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Api\Providers;

use Hubzero\Error\Handler;
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
		$this->app['error'] = function($app)
		{
			$handler = new Handler(
				new Plain($app['config']->get('debug')),
				$app['config']->get('debug')
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
			$this->app['error']->setRenderer(new Api(
				$this->app['response'],
				$this->app['config']->get('debug')
			));
		}
	}
}
