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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session;

use Hubzero\Base\ServiceProvider;

/**
 * Session service provider
 */
class SessionServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['session'] = function($app)
		{
			$handler = $app['config']->get('session_handler', 'none');

			$options = array(
				'name'          => md5($app['config']->get('secret') . ($app['client']->name == 'administrator' ? 'administrator' : 'site')),
				'cookie_domain' => $app['config']->get('cookie_domain', ''),
				'cookie_path'   => $app['config']->get('cookie_path', '/'),
				// Config time is in minutes so we need to do some
				// math to get seconds.
				'expire'        => ($app['config']->get('lifetime') ? $app['config']->get('lifetime') * 60 : 900)
			);

			switch ($app['client']->id)
			{
				case 0:
					if ($app['config']->get('force_ssl') == 2)
					{
						$options['force_ssl'] = true;
					}
				break;

				case 1:
					if ($app['config']->get('force_ssl') >= 1)
					{
						$options['force_ssl'] = true;
					}
				break;
			}

			$session = new Manager($handler, $options);

			if ($session->getState() == 'expired')
			{
				$session->restart();
			}

			return $session;
		};
	}
}