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
		$file   = substr($option, 4);

		$client = (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);

		// Get component path
		if (is_dir(PATH_CORE . DS . 'components' . DS . $option . DS . $client))
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

		$path = JPATH_COMPONENT . DS . 'controllers' . DS . 'api.php';

		// If component is disabled throw error
		if (!$this->isEnabled($option) || !file_exists($path))
		{
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		// Load common and local language files.
		$lang->load($option, JPATH_COMPONENT, null, false, true) ||
		$lang->load($option, JPATH_BASE, null, false, true);

		include($path);

		// Handle template preview outlining.
		$contents = null;

		// Execute the component.
		$contents = $this->execute($file);

		// Revert the scope
		$this->app->forget('scope');
		$this->app->set('scope', $scope);

		return $contents;
	}

	/**
	 * Execute the component.
	 *
	 * @param   string  $path  The component path.
	 * @return  string  The component output
	 */
	protected function execute($path)
	{
		$classname = ucfirst($path) . 'ControllerApi';

/*foreach (debug_backtrace() as $line)
{
	echo $line['file'] . ':' . $line['line'] . '<br />';
}*/
		if (class_exists($classname))
		{
			$segments = $this->app['request']->segments();
			if (count($segments) > 1 && $segments[0] == 'api')
			{
				$prefix = array_shift($segments);
			}
			$component = array_shift($segments);

			$controller = new $classname();
			$controller->setRequest($this->app['request']);
			$controller->setResponse($this->app['response']);
			$controller->setProvider($this->app['provider']);
			$controller->setRouteSegments($segments);

			ob_start();
			$controller->execute();
			$contents = ob_get_contents();
			ob_end_clean();

			return $contents;
		}

		return '';
	}
}
