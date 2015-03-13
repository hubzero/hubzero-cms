<?php
namespace Hubzero\Component;

use Exception;
use stdClass;
use Lang;

/**
 * Component helper class
 */
class Loader
{
	/**
	 * The component list cache
	 *
	 * @var  array
	 */
	protected static $components = array();

	/**
	 * Checks if the component is enabled
	 *
	 * @param   string   $option  The component option.
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned.
	 * @return  boolean
	 */
	public function isEnabled($option, $strict = false)
	{
		$result = $this->load($option, $strict);

		return ($result->enabled | \JFactory::getApplication()->isAdmin());
	}

	/**
	 * Gets the parameter object for the component
	 *
	 * @param   string   $option  The option for the component.
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned
	 * @return  JRegistry  A JRegistry object.
	 */
	public function params($option, $strict = false)
	{
		return $this->load($option, $strict)->params;
	}

	/**
	 * Render the component.
	 *
	 * @param   string  $option  The component option.
	 * @param   array   $params  The component parameters
	 * @return  object
	 */
	public function render($option, $params = array())
	{
		// Initialise variables.
		$app = \JFactory::getApplication();

		// Load template language files.
		$template = $app->getTemplate(true)->template;

		$lang = \JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true) || $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

		if (empty($option))
		{
			// Throw 404 if no component
			throw new Exception(Lang::txt('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'), 404);
		}

		// Record the scope
		$scope = $app->scope;

		// Set scope to component name
		$app->scope = $option;

		// Build the component path.
		$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
		$file = substr($option, 4);

		// [!] HUBZERO - Set path and constants for combined components
		$client = ($app->isAdmin() ? 'admin' : 'site');

		// Get component path
		if (is_dir(JPATH_SITE . '/components/' . $option . '/' . $client))
		{
			define('JPATH_COMPONENT', JPATH_SITE . '/components/' . $option . '/' . $client);
			define('JPATH_COMPONENT_SITE', JPATH_SITE . '/components/' . $option . '/site');
			define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_SITE . '/components/' . $option . '/admin');
		}
		else
		{
			define('JPATH_COMPONENT', JPATH_BASE . '/components/' . $option);
			define('JPATH_COMPONENT_SITE', JPATH_SITE . '/components/' . $option);
			define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/' . $option);
		}

		$path = JPATH_COMPONENT . '/' . $file . '.php';
		// [!] HUBZERO - END Set path and constants for combined components

		// If component is disabled throw error
		if (!$this->isEnabled($option) || !file_exists($path))
		{
			throw new Exception(Lang::txt('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'), 404);
		}

		// Load common and local language files.
		$lang->load($option, JPATH_BASE, null, false, true) || $lang->load($option, JPATH_COMPONENT, null, false, true);

		// Handle template preview outlining.
		$contents = null;

		// Execute the component.
		$contents = $this->execute($path);

		// Revert the scope
		$app->scope = $scope;

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
		ob_start();
		require_once $path;
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Load the installed components into the components property.
	 *
	 * @param   string   $option  The element value for the extension
	 * @param   boolean  $strict  If set and the component does not exist, the enabled attribute will be set to false.
	 * @return  object
	 */
	public function load($option, $strict = false)
	{
		if (isset(self::$components[$option]))
		{
			return self::$components[$option];
		}

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id AS id, element AS "option", params, enabled');
		$query->from('#__extensions');
		$query->where($query->qn('type') . ' = ' . $db->quote('component'));
		$query->where($query->qn('element') . ' = ' . $db->quote($option));
		$db->setQuery($query);

		$cache = \JFactory::getCache('_system', 'callback');

		self::$components[$option] = $cache->get(array($db, 'loadObject'), null, $option, false);

		if ($error = $db->getErrorMsg())// || empty(self::$components[$option]))
		{
			throw new Exception(Lang::txt('JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error), 500);
		}

		if (empty(self::$components[$option]))
		{
			self::$components[$option] = new stdClass;
			self::$components[$option]->option  = $option;
			self::$components[$option]->enabled = $strict ? 0 : 1;
			self::$components[$option]->params  = '';
			self::$components[$option]->id      = 0;
		}

		// Convert the params to an object.
		if (is_string(self::$components[$option]->params))
		{
			$temp = new \JRegistry;
			$temp->loadString(self::$components[$option]->params);

			self::$components[$option]->params = $temp;
		}

		return self::$components[$option];
	}
}
