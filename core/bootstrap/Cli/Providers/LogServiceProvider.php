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

namespace Bootstrap\Cli\Providers;

use Hubzero\Log\Manager;
use Hubzero\Base\ServiceProvider;

/**
 * Event service provider
 */
class LogServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['log'] = function($app)
		{
			$path = $app['config']->get('log_path');
			if (is_dir('/var/log/hubzero'))
			{
				$path = '/var/log/hubzero';
			}

			$dispatcher = null;
			if ($app->has('dispatcher'))
			{
				$dispatcher = $app['dispatcher'];
			}

			$manager = new Manager($path);

			$manager->register('debug', array(
				'file'       => 'cmsdebug.log',
				'dispatcher' => $dispatcher
			));

			$manager->register('auth', array(
				'file'       => 'cmsauth.log',
				'level'      => 'info',
				'format'     => "%datetime% %message%\n",
				'dispatcher' => $dispatcher
			));

			$manager->register('spam', array(
				'file'       => 'cmsspam.log',
				'dispatcher' => $dispatcher
			));

			return $manager;
		};
	}
}
