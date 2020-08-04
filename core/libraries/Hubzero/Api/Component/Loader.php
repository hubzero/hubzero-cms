<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @return  bool
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
		$found      = false;

		$version    = $this->app['request']->getVar('version');
		$controller = $this->app['request']->getCmd('controller', $this->app['request']->segment(2, 'api'));
		$controllerClass = '\\Hubzero\\Component\\ApiController';

		// Make sure the component is enabled
		if ($this->isEnabled($option) && is_dir($this->path($option)))
		{
			// Set path and constants
			define('PATH_COMPONENT', $this->path($option) . DIRECTORY_SEPARATOR . $client);
			define('PATH_COMPONENT_SITE', $this->path($option) . DIRECTORY_SEPARATOR . 'site');
			define('PATH_COMPONENT_ADMINISTRATOR', $this->path($option) . DIRECTORY_SEPARATOR . 'admin');

			// Legacy compatibility
			// @TODO: Deprecate this!
			define('JPATH_COMPONENT', PATH_COMPONENT);
			define('JPATH_COMPONENT_SITE', PATH_COMPONENT_SITE);
			define('JPATH_COMPONENT_ADMINISTRATOR', PATH_COMPONENT_ADMINISTRATOR);

			if (is_dir(PATH_COMPONENT))
			{
				// If no version is specified, try to determine the most
				// recent version from the available controllers
				if (!$version)
				{
					$files = glob(PATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . 'v*.php');

					if (!empty($files))
					{
						natsort($files);

						$file = end($files);
						$controller = basename($file, '.php');
					}
				}
				else
				{
					$controller .= 'v' . str_replace('.', '_', $version);
				}

				$path       = PATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
				$controllerClass = '\\Components\\' . ucfirst(substr($option, 4)) . '\\Api\\Controllers\\' . ucfirst($controller);

				// Include the file
				if (file_exists($path))
				{
					require_once $path;
				}
			}

			// Check to see if the class exists
			if ($controllerClass && class_exists($controllerClass))
			{
				$found = true;

				$lang->load($option, PATH_COMPONENT, null, false, true);
			}
		}

		if (!$found)
		{
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		// Handle template preview outlining.
		$action = new $controllerClass($this->app->get('response'), array(
			'name'       => substr($option, 4),
			'controller' => $controller
		));
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
