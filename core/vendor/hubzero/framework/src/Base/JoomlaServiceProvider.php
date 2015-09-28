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
