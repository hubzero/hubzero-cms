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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\Component;

use Hubzero\Component\Loader as Base;

/**
 * Component helper class
 */
class Loader extends Base
{
	/**
	 * Render the component.
	 *
	 * Based off of Joomla's JComponentHelper::renderComponent()
	 * but with a number of changes.
	 *
	 * @param   string  $option  The component option.
	 * @param   array   $params  The component parameters
	 * @return  object
	 */
	public function render($option, $params = array())
	{
		$lang = $this->app['language'];

		if (empty($option))
		{
			// Throw 404 if no component
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		$option = $this->canonical($option);

		// Record the scope
		$scope = $this->app->has('scope') ? $this->app->get('scope') : null;

		// Set scope to component name
		$this->app->set('scope', $option);

		// Build the component path.
		$client = (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);

		// Get component path
		if (is_dir(PATH_APP . DS . 'components' . DS . $option . DS . $client))
		{
			// Set path and constants for combined components
			define('JPATH_COMPONENT', PATH_APP . DS . 'components' . DS . $option . DS . $client);
			define('JPATH_COMPONENT_SITE', PATH_APP . DS . 'components' . DS . $option . DS . 'site');
			define('JPATH_COMPONENT_ADMINISTRATOR', PATH_APP . DS . 'components' . DS . $option . DS . 'admin');
		}
		else if (is_dir(PATH_CORE . DS . 'components' . DS . $option . DS . $client))
		{
			// Set path and constants for combined components
			define('JPATH_COMPONENT', PATH_CORE . DS . 'components' . DS . $option . DS . $client);
			define('JPATH_COMPONENT_SITE', PATH_CORE . DS . 'components' . DS . $option . DS . 'site');
			define('JPATH_COMPONENT_ADMINISTRATOR', PATH_CORE . DS . 'components' . DS . $option . DS . 'admin');
		}
		else
		{
			// Set path and constants for legacy components
			define('JPATH_COMPONENT', JPATH_BASE . DS . 'components' . DS . $option);
			define('JPATH_COMPONENT_SITE', JPATH_SITE . DS . 'components' . DS . $option);
			define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DS . 'components' . DS . $option);
		}

		$controller = $this->app['request']->getCmd('controller', 'api') . 'v' . str_replace('.', '_', $this->app['request']->getVar('version', '1.0'));

		$path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';

		// If component is disabled throw error
		if (!$this->isEnabled($option) || !file_exists($path))
		{
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		// Load common and local language files.
		$lang->load($option, JPATH_COMPONENT, null, false, true) ||
		$lang->load($option, JPATH_BASE, null, false, true);

		require_once $path;

		// Handle template preview outlining.
		$controller = '\\Components\\' . ucfirst(substr($option, 4)) . '\\Api\\Controllers\\' . ucfirst($controller);
		$action = new $controller(\App::get('response'));
		$action->execute();

		// Revert the scope
		$this->app->forget('scope');
		$this->app->set('scope', $scope);

		return true;
	}

	/**
	 * Execute the component.
	 *
	 * @param   string  $path  The component path.
	 * @return  string  The component output
	 */
	protected function execute($path)
	{
		return '';
	}
}
