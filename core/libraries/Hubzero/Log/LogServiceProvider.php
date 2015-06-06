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

namespace Hubzero\Log;

use Hubzero\Base\ServiceProvider;
use Hubzero\Log\Writer;
use Monolog\Logger as Monolog;

/**
 * Event service provider
 */
class LogServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerDebugLog();

		$this->registerAuthLog();

		$this->registerSpamLog();
	}

	/**
	 * Register the debug log.
	 *
	 * @return  void
	 */
	public function registerDebugLog()
	{
		$this->app['log.debug'] = function($app)
		{
			$log = new Writer(
				new Monolog($app['config']->get('application_env')),
				$app['dispatcher']
			);

			$path = $app['config']->get('log_path');
			if (is_dir('/var/log/hubzero'))
			{
				$path = '/var/log/hubzero';
			}

			$log->useFiles($path . DS . 'cmsdebug.log', 'debug', '', 'Y-m-d H:i:s', 0640);

			return $log;
		};
	}

	/**
	 * Register the auth log.
	 *
	 * @return  void
	 */
	public function registerAuthLog()
	{
		$this->app['log.auth'] = function($app)
		{
			$log = new Writer(
				new Monolog($app['config']->get('application_env')),
				$app['dispatcher']
			);

			$path = $app['config']->get('log_path');
			if (is_dir('/var/log/hubzero'))
			{
				$path = '/var/log/hubzero';
			}

			$log->useFiles($path . DS . 'cmsauth.log', 'info', "%datetime% %message%\n", 'Y-m-d H:i:s', 0640);

			return $log;
		};
	}

	/**
	 * Register the spam log.
	 *
	 * @return  void
	 */
	public function registerSpamLog()
	{
		$this->app['log.spam'] = function($app)
		{
			$log = new Writer(
				new Monolog($app['config']->get('application_env')),
				$app['dispatcher']
			);

			$path = $app['config']->get('log_path');
			if (is_dir('/var/log/hubzero'))
			{
				$path = '/var/log/hubzero';
			}

			$log->useFiles($path . DS . 'cmsspam.log', 'debug', '', 'Y-m-d H:i:s', 0640);

			return $log;
		};
	}
}