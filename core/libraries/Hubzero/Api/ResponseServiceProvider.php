<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$this->app['response']->headers->set('Content-Type', $types[$format]);
	}
}