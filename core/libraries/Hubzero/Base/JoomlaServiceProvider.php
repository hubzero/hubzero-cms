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

namespace Hubzero\Base;

/**
 * Joomla handler service provider
 * 
 * This loads in the core Joomla framework and instantiates
 * the base application class.
 */
class JoomlaServiceProvider extends ServiceProvider
{
	/**
	 * Register the exception handler.
	 *
	 * @return  void
	 */
	public function boot()
	{
		require_once PATH_CORE . DS . 'libraries' . DS . 'import.php';
		require_once PATH_CORE . DS . 'libraries' . DS . 'cms.php';

		if ($this->app->isAdmin() || $this->app->isSite())
		{
			jimport('joomla.application.menu');
		}

		jimport('joomla.environment.uri');
		jimport('joomla.utilities.utility');
		jimport('joomla.event.dispatcher');
		jimport('joomla.utilities.arrayhelper');

		if ($this->app->isAdmin())
		{
			jimport('joomla.html.parameter');

			require_once PATH_CORE . DS . 'bootstrap' . DS . $this->app['client']->name . DS . 'helper.php';
			require_once PATH_CORE . DS . 'bootstrap' . DS . $this->app['client']->name . DS . 'toolbar.php';
		}

		$app = \JFactory::getApplication($this->app['client']->name);
	}
}
