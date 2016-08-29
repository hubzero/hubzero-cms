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

use Hubzero\Base\ServiceProvider;
use Hubzero\User\Picture\File;
use Hubzero\User\Manager;
use Hubzero\User\User;

/**
 * User service provider
 */
class UserServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['user'] = function($app)
		{
			return new Manager($app);
		};
	}

	/**
	 * Force SSL if site is configured to and
	 * the connection is not secure.
	 *
	 * @return  void
	 */
	public function boot()
	{
		// Set the base link to use for profiles
		User::$linkBase = 'index.php?option=com_members&id={ID}';

		// Set the picture resolver
		if ($this->app->has('component'))
		{
			try {
				$params = $this->app['component']->params('com_members');
			}
			catch (Exception $e) {
				$params = new \Hubzero\Config\Registry;
			}

			$config = [
				'path'          => PATH_APP . DS . 'site' . DS . 'members',
				'pictureName'   => 'profile.png',
				'thumbnailName' => 'thumb.png',
				'fallback'      => $params->get('defaultpic', '/core/components/com_members/site/assets/img/profile.gif')
			];

			User::$pictureResolvers[] = new File($config);

			$resolver = $params->get('picture');

			// Build the class name
			$cls = 'Hubzero\\User\\Picture\\' . ucfirst($resolver);

			if (class_exists($cls))
			{
				User::$pictureResolvers[] = new $cls($config);
			}
		}
	}
}
