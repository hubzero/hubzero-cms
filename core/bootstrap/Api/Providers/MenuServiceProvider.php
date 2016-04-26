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

use Hubzero\Menu\Manager;
use Hubzero\Base\ServiceProvider;
use Hubzero\Config\Registry;

/**
 * Menu service provider
 */
class MenuServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['menu.manager'] = function($app)
		{
			return $manager = new Manager();
		};

		$this->app['menu'] = function($app)
		{
			$options = [
				'language_filter' => null,
				'language'        => null,
				'access'          => \User::getAuthorisedViewLevels()
			];

			return $app['menu.manager']->menu($app['client']->name, $options);
		};

		$this->app['menu.params'] = function($app)
		{
			$params = new Registry();

			$menu = $app['menu']->getActive();
			if (is_object($menu))
			{
				$params->parse($menu->params);
			}
			else if ($app->has('component'))
			{
				$temp = clone $app['component']->params('com_menus');
				$params->merge($temp);
			}

			return $params;
		};
	}
}
