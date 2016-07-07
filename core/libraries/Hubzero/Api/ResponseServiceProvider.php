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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		ini_set('zlib.output_compression','0');
		ini_set('output_handler','');
		ini_set('implicit_flush','0');

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