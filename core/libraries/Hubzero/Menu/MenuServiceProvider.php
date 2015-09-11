<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Menu;

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
			return $app['menu.manager']->menu($app['client']->name);
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
