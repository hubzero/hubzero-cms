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

namespace Hubzero\Template;

use Hubzero\Container\Container;
use Hubzero\Config\Registry;
use Exception;
use stdClass;

/**
 * Component helper class
 */
class Loader
{
	/**
	 * The application implementation.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * Base path for templates
	 *
	 * @var  array
	 */
	protected $paths;

	/**
	 * The component list cache
	 *
	 * @var  array
	 */
	protected static $components = array();

	/**
	 * Constructor
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct(Container $app, $path = null)
	{
		self::$components = array();

		if (!$path)
		{
			$path = array(
				PATH_APP . DS . 'templates',
				PATH_CORE . DS . 'templates'
			);
		}

		$this->paths = (array) $path;
		$this->app   = $app;
	}

	/**
	 * Checks if the template is enabled
	 *
	 * @param   string   $option  The component option.
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned.
	 * @return  boolean
	 */
	public function isEnabled($name, $client_id = 0)
	{
		$result = $this->load($name, $client_id);

		return ($result->name == $name);
	}

	/**
	 * Gets the parameter object for the component
	 *
	 * @param   string   $option     The option for the component.
	 * @param   integer  $client_id  If set and the component does not exist, false will be returned
	 * @return  object   A Registry object.
	 */
	public function params($name, $client_id = 0)
	{
		return $this->load($name, $client_id)->params;
	}

	/**
	 * Make sure template name follows naming conventions
	 *
	 * @param   string  $name
	 * @return  string
	 */
	public function canonical($name)
	{
		return preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
	}

	/**
	 * Load a template by client
	 *
	 * @param   integer  $client_id  The client to load the tmeplate for
	 * @return  string
	 */
	public function load($client_id = null)
	{
		if (!is_null($client_id))
		{
			$client = \Hubzero\Base\ClientManager::client($client_id, (! is_numeric($client_id)));
		}
		else
		{
			$client = $this->app['client'];
		}

		$name = $client->name;

		$method = 'get' . ucfirst($name) . 'Template';

		if (method_exists($this, $method))
		{
			return $this->$method();
		}

		return $this->getSystemTemplate();
	}

	/**
	 * Get the system template
	 *
	 * @return  object
	 */
	public function getSystemTemplate()
	{
		$template = new stdClass;
		$template->id        = 0;
		$template->home      = 0;
		$template->template  = 'system';
		$template->params    = new Registry();
		$template->protected = 1;
		$template->path      = PATH_CORE . DS . 'templates' . DS . $template->template;

		return $template;
	}

	/**
	 * Get the admin template
	 *
	 * @return  object
	 */
	public function getAdministratorTemplate()
	{
		// Load the template name from the database
		try
		{
			$db = \App::get('db');
			$query = $db->getQuery(true);
			$query->select('s.id, s.home, s.template, s.params, e.protected');
			$query->from('#__template_styles as s');
			$query->leftJoin('#__extensions as e ON e.type=' . $db->quote('template') . ' AND e.element=s.template AND e.client_id=s.client_id');
			if ($style = \User::getParam('admin_style'))
			{
				$query->where('s.client_id = 1 AND s.id = ' . (int) $style . ' AND e.enabled = 1', 'OR');
			}
			$query->where('s.client_id = 1 AND s.home = 1', 'OR');
			$query->order('home');
			$db->setQuery($query);

			$template = $db->loadObject();
		}
		catch (Exception $e)
		{
			return $this->getSystemTemplate();
		}

		$template->template = $this->canonical($template->template);
		$template->params   = new Registry($template->params);

		foreach ($this->paths as $path)
		{
			if (file_exists($path . DS . $template->template . DS . 'index.php'))
			{
				$template->path = $path . DS . $template->template;
				return $template;
			}
		}

		return $this->getSystemTemplate();
	}

	/**
	 * Get the site template
	 *
	 * @return  object
	 */
	public function getSiteTemplate()
	{
		// Get the id of the active menu item
		$menu = $this->app['menu'];
		$item = $menu->getActive();
		if (!$item)
		{
			$item = $menu->getItem($this->app['request']->getInt('Itemid', 0));
		}

		$id = 0;
		if (is_object($item))
		{
			// valid item retrieved
			$id = $item->template_style_id;
		}
		$condition = '';

		$tid = $this->app['request']->getVar('templateStyle', 0);
		if (is_numeric($tid) && (int) $tid > 0)
		{
			$id = (int) $tid;
		}

		if (!$this->app->has('cache.store') || !($cache = $this->app['cache.store']))
		{
			$cache = new \Hubzero\Cache\Storage\None();
		}

		$tag = '';

		if ($this->app->has('language.filter'))
		{
			$tag = $this->app['language']->getTag();
		}

		if (!$templates = $cache->get('com_templates.templates0' . $tag))
		{
			// Load styles
			try
			{
				$db = \App::get('db');
				$query = $db->getQuery(true);
				$query->select('s.id, s.home, s.template, s.params, e.protected');
				$query->from('#__template_styles as s');
				$query->where('s.client_id = 0');
				$query->where('e.enabled = 1');
				$query->leftJoin('#__extensions as e ON e.element=s.template AND e.type=' . $db->quote('template') . ' AND e.client_id=s.client_id');

				$db->setQuery($query);
				$templates = $db->loadObjectList('id');

				foreach ($templates as &$template)
				{
					if (!($template->params instanceof Registry))
					{
						$registry = new Registry($template->params);

						$template->params = $registry;
					}

					// Create home element
					if ($template->home == 1 && !isset($templates[0])) // || ($this->app->has('language.filter') && $this->app->get('language.filter') && $template->home == $tag))
					{
						$templates[0] = clone $template;
					}
				}
				$cache->put('com_templates.templates0' . $tag, $templates, $this->app['config']->get('cachetime', 15));
			}
			catch (Exception $e)
			{
				return $this->getSystemTemplate();
			}
		}

		if (isset($templates[$id]))
		{
			$template = $templates[$id];
		}
		else
		{
			// [!] zooley - Fixing template fallback to always load system template if current one is not found.
			//     Previous way could cause code to get stuck in a loop and run out of memory.
			if (isset($templates[0]))
			{
				$template = $templates[0];
			}
			else
			{
				$template = new stdClass;
				$template->params = new Registry;
				$template->home   = 0;
			}
			$template->id        = 0;
			$template->template  = 'system';
			$template->protected = 1;
		}

		// Allows for overriding the active template from the request
		//$template->template = $this->app['request']->getCmd('template', $template->template);
		$template->template = $this->canonical($template->template); // need to filter the default value as well

		// Fallback template
		foreach ($this->paths as $path)
		{
			if (file_exists($path . DS . $template->template . DS . 'index.php'))
			{
				$template->path = $path . DS . $template->template;
				return $template;
			}
		}

		return $this->getSystemTemplate();
	}
}
