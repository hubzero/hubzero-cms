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

namespace Hubzero\Error;

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
			$handler = new Handler(
				new Plain($app['config']->get('debug')), //new Page($app['document'], $app['template']->template, $app['config']->get('debug')),
				$app['config']->get('debug')
			);

			/*if ($handler->runningInConsole())
			{
				$handler->setRenderer(new Plain($app['config']->get('debug')));
			}*/

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
				error_reporting($config->error_reporting);
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
		if (!$this->app->runningInConsole() && $this->app->has('document'))
		{
			$this->app['error']->setRenderer(new Page($this->app['document'], $this->app['template']->template, $this->app['config']->get('debug')));
		}
	}
}
