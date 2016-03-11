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

namespace Hubzero\Module;

use Hubzero\Container\Container;
use Hubzero\Utility\Date;
use Hubzero\Config\Registry;

/**
 * Module loader class
 *
 * Inspired by Joomla's JModuleHelper class
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
	 * A profiler for debugging
	 *
	 * @var  object
	 */
	protected $profiler;

	/**
	 * Constructor
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct(Container $app, $profiler = null)
	{
		$this->app      = $app;
		$this->profiler = $profiler;
	}

	/**
	 * Count the modules based on the given condition
	 *
	 * @param   string   $condition  The condition to use
	 * @return  integer  Number of modules found
	 */
	public function count($condition)
	{
		$words = explode(' ', $condition);
		for ($i = 0; $i < count($words); $i+=2)
		{
			// odd parts (modules)
			$name = strtolower($words[$i]);
			$words[$i] = count($this->byPosition($name));
		}

		$str = 'return ' . implode(' ', $words) . ';';

		return eval($str);
	}

	/**
	 * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'mod_breadcrumbs')
	 *
	 * @param   string  $name   The name of the module
	 * @param   string  $title  The title of the module, optional
	 * @return  object  The Module object
	 */
	public function byName($name, $title = null)
	{
		$result = null;

		$modules = $this->all();
		$total   = count($modules);

		for ($i = 0; $i < $total; $i++)
		{
			// Match the name of the module
			if ($modules[$i]->name == $name || $modules[$i]->module == $name)
			{
				// Match the title if we're looking for a specific instance of the module
				if (!$title || $modules[$i]->title == $title)
				{
					// Found it
					$result =& $modules[$i];
					break;
				}
			}
		}

		// If we didn't find it, and the name is mod_something, create a dummy object
		if (is_null($result) && substr($name, 0, 4) == 'mod_')
		{
			$result = new \stdClass;
			$result->id        = 0;
			$result->title     = '';
			$result->module    = $name;
			$result->position  = '';
			$result->content   = '';
			$result->showtitle = 0;
			$result->control   = '';
			$result->params    = '';
			$result->user      = 0;
		}

		return $result;
	}

	/**
	 * Get modules by position
	 *
	 * @param   string  $position  The position of the module
	 * @return  array   An array of module objects
	 */
	public function byPosition($position)
	{
		$position = strtolower($position);
		$result   = array();

		$modules = $this->all();

		$total = count($modules);
		for ($i = 0; $i < $total; $i++)
		{
			if ($modules[$i]->position == $position)
			{
				$result[] =& $modules[$i];
			}
		}

		if (count($result) == 0)
		{
			if ($this->outline())
			{
				$result[0] = $this->byName('mod_' . $position);
				$result[0]->title    = $position;
				$result[0]->content  = $position;
				$result[0]->position = $position;
			}
		}

		return $result;
	}

	/**
	 * Checks if a module is enabled
	 *
	 * @param   string  $module  The module name
	 * @return  boolean
	 */
	public function isEnabled($module)
	{
		$result = $this->byName($module);

		return !is_null($result);
	}

	/**
	 * Render modules for a position
	 *
	 * @param   string  $position  Position to render modules for
	 * @param   string  $style     Module style (deprecated?)
	 * @return  string  HTML
	 */
	public function position($position, $style='none')
	{
		if (!is_array($style))
		{
			$style = array('style' => $style);
		}

		$contents = '';
		foreach ($this->byPosition($position) as $mod)
		{
			$contents .= $this->render($mod, $style);
		}

		return $contents;
	}

	/**
	 * Render module by name
	 *
	 * @param   string  $name   Module name
	 * @param   string  $style  Module style (deprecated?)
	 * @return  string  HTML
	 */
	public function name($name, $style='none')
	{
		if (!is_array($style))
		{
			$style = array('style' => $style);
		}

		return $this->render(
			$this->byName($name),
			$style
		);
	}

	/**
	 * Determine if module position outlining is enabled
	 *
	 * @return  boolean
	 */
	protected function outline()
	{
		if ($this->app['request']->getBool('tp')
		 && $this->app['component']->params('com_templates')->get('template_positions_display'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Render the module.
	 *
	 * @param   object  $module   A module object.
	 * @param   array   $attribs  An array of attributes for the module (probably from the XML).
	 * @return  string  The HTML content of the module output.
	 */
	public function render($module, $attribs = array())
	{
		static $chrome;

		if (null !== $this->profiler)
		{
			$this->profiler->mark('beforeRenderModule ' . $module->module . ' (' . $module->title . ')');
		}

		// Record the scope.
		$scope = $this->app->has('scope') ? $this->app->get('scope') : null;

		// Set scope to component name
		$this->app->set('scope', $module->module);

		// Get module parameters
		$params = new Registry($module->params);

		if (isset($attribs['params']))
		{
			$customparams = new Registry(html_entity_decode($attribs['params'], ENT_COMPAT, 'UTF-8'));

			$params->merge($customparams);

			$module->params = $params->toString();
		}

		// Get module path
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
		$path = PATH_APP . DS . 'modules' . DS . $module->module . DS . $module->module . '.php';

		if (!file_exists($path))
		{
			$path = PATH_CORE . DS . 'modules' . DS . $module->module . DS . $module->module . '.php';
		}

		// Load the module
		// $module->user is a check for 1.0 custom modules and is deprecated refactoring
		if (file_exists($path))
		{
			$this->app['language']->load($module->module, PATH_APP . DS . 'bootstrap' . DS . $this->app['client']->name, null, false, true) ||
			$this->app['language']->load($module->module, dirname($path), null, false, true);

			$content = '';
			ob_start();
			include $path;
			$module->content = ob_get_contents() . $content;
			ob_end_clean();
		}

		// Load the module chrome functions
		if (!$chrome)
		{
			$chrome = array();
		}

		include_once PATH_CORE . DS . 'templates' . DS . 'system' . DS . 'html' . DS . 'modules.php';
		$chromePath = $this->app['template']->path . DS . 'html' . DS . 'modules.php';

		if (!isset($chrome[$chromePath]))
		{
			if (file_exists($chromePath))
			{
				include_once $chromePath;
			}

			$chrome[$chromePath] = true;
		}

		// Make sure a style is set
		if (!isset($attribs['style']))
		{
			$attribs['style'] = 'none';
		}

		// Dynamically add outline style
		if ($this->outline())
		{
			$attribs['style'] .= ' outline';
		}

		foreach (explode(' ', $attribs['style']) as $style)
		{
			$chromeMethod = 'modChrome_' . $style;

			// Apply chrome and render module
			if (function_exists($chromeMethod))
			{
				$module->style = $attribs['style'];

				ob_start();
				$chromeMethod($module, $params, $attribs);
				$module->content = ob_get_contents();
				ob_end_clean();
			}
		}

		// Revert the scope
		$this->app->forget('scope');
		$this->app->set('scope', $scope);

		if (null !== $this->profiler)
		{
			$this->profiler->mark('afterRenderModule ' . $module->module . ' (' . $module->title . ')');
		}

		return $module->content;
	}

	/**
	 * Get the path to a layout for a module
	 *
	 * @param   string  $module  The name of the module
	 * @param   string  $layout  The name of the module layout. If alternative layout, in the form template:filename.
	 * @return  string  The path to the module layout
	 */
	public function getLayoutPath($module, $layout = 'default')
	{
		$template = $this->app['template']->template;
		$path     = ($this->app['template']->protected ? PATH_CORE : PATH_APP) . DS . 'templates';
		$default  = $layout;

		if (strpos($layout, ':') !== false)
		{
			// Get the template and file name from the string
			$temp = explode(':', $layout);

			$template = ($temp[0] == '_') ? $template : $temp[0];
			$layout   = $temp[1];
			$default  = ($temp[1]) ? $temp[1] : 'default';
		}

		// Build the template and base path for the layout
		$tPath = $path . '/' . $template . '/html/' . $module . '/' . $layout . '.php';

		$base = PATH_APP . '/modules/' . $module;
		if (!is_dir($base))
		{
			$base = PATH_CORE . '/modules/' . $module;
		}

		$bPath = $base . '/tmpl/' . $default . '.php';
		$dPath = $base . '/tmpl/default.php';

		// If the template has a layout override use it
		if (file_exists($tPath))
		{
			return $tPath;
		}
		elseif (file_exists($bPath))
		{
			return $bPath;
		}

		return $dPath;
	}

	/**
	 * Load published modules.
	 *
	 * @return  array
	 */
	public function all()
	{
		static $clean;

		if (isset($clean))
		{
			return $clean;
		}

		$Itemid   = $this->app['request']->getInt('Itemid');

		$user     = \User::getRoot();
		$groups   = implode(',', $user->getAuthorisedViewLevels());
		$lang     = $this->app['language']->getTag();
		$clientId = (int) $this->app['client']->id;

		$cache = $this->app['cache.store'];
		$cacheid = 'com_modules.' . md5(serialize(array($Itemid, $groups, $clientId, $lang)));

		if (!($clean = $cache->get($cacheid)))
		{
			$db = $this->app['db'];

			$query = $db->getQuery(true);
			$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid, e.protected');
			$query->from('#__modules AS m');
			$query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id');
			$query->where('m.published = 1');

			$query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
			$query->where('e.enabled = 1');

			$now = with(new Date('now'))->toSql();
			$nullDate = $db->getNullDate();

			$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')');
			$query->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')');

			$query->where('m.access IN (' . $groups . ')');
			$query->where('m.client_id = ' . $clientId);
			$query->where('(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)');

			// Filter by language
			if ($this->app->isSite() && $this->app->get('language.filter'))
			{
				$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
			}

			$query->order('m.position, m.ordering');

			// Set the query
			$db->setQuery($query);
			$modules = $db->loadObjectList();
			$clean = array();

			if ($db->getErrorNum())
			{
				$this->app['notification']->error(
					$this->app['language']->txt('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg())
				);

				return $clean;
			}

			// Apply negative selections and eliminate duplicates
			$negId = $Itemid ? -(int) $Itemid : false;
			$dupes = array();
			for ($i = 0, $n = count($modules); $i < $n; $i++)
			{
				$module = &$modules[$i];

				// The module is excluded if there is an explicit prohibition
				$negHit = ($negId === (int) $module->menuid);

				if (isset($dupes[$module->id]))
				{
					// If this item has been excluded, keep the duplicate flag set,
					// but remove any item from the cleaned array.
					if ($negHit)
					{
						unset($clean[$module->id]);
					}
					continue;
				}

				$dupes[$module->id] = true;

				// Only accept modules without explicit exclusions.
				if (!$negHit)
				{
					$module->name     = substr($module->module, 4);
					$module->style    = null;
					$module->position = strtolower($module->position);

					$clean[$module->id] = $module;
				}
			}

			unset($dupes);

			// Return to simple indexing that matches the query order.
			$clean = array_values($clean);

			$cache->put($cacheid, $clean, $this->app['config']->get('cachetime', 15));
		}

		return $clean;
	}

	/**
	 * Module cache helper
	 *
	 * Caching modes:
	 * To be set in XML:
	 * 'static'      One cache file for all pages with the same module parameters
	 * 'oldstatic'   1.5 definition of module caching, one cache file for all pages
	 * with the same module id and user aid,
	 * 'itemid'      Changes on itemid change, to be called from inside the module:
	 * 'safeuri'     Id created from $cacheparams->modeparams array,
	 * 'id'          Module sets own cache id's
	 *
	 * @param   object  $module        Module object
	 * @param   object  $moduleparams  Module parameters
	 * @param   object  $cacheparams   Module cache parameters - id or url parameters, depending on the module cache mode
	 * @return  string
	 */
	public function cache($module, $moduleparams, $cacheparams)
	{
		// [!] Deprecated. Needs to be refactored.
		return true;

		if (!isset($cacheparams->modeparams))
		{
			$cacheparams->modeparams = null;
		}

		if (!isset($cacheparams->cachegroup))
		{
			$cacheparams->cachegroup = $module->module;
		}

		$cache = \JFactory::getCache($cacheparams->cachegroup, 'callback');

		// Turn cache off for internal callers if parameters are set to off and for all logged in users
		if ($moduleparams->get('owncache', null) === '0' || $this->app['config']->get('caching') == 0 || \User::get('id'))
		{
			$cache->setCaching(false);
		}

		// module cache is set in seconds, global cache in minutes, setLifeTime works in minutes
		$cache->setLifeTime($moduleparams->get('cache_time', $this->app['config']->get('cachetime') * 60) / 60);

		$wrkaroundoptions = array('nopathway' => 1, 'nohead' => 0, 'nomodules' => 1, 'modulemode' => 1, 'mergehead' => 1);

		$wrkarounds = true;
		$view_levels = md5(serialize(\User::getAuthorisedViewLevels()));

		switch ($cacheparams->cachemode)
		{
			case 'id':
				$ret = $cache->get(
					array($cacheparams->class, $cacheparams->method),
					$cacheparams->methodparams,
					$cacheparams->modeparams,
					$wrkarounds,
					$wrkaroundoptions
				);
				break;

			case 'safeuri':
				$secureid = null;
				if (is_array($cacheparams->modeparams))
				{
					$uri = \Request::get();
					$safeuri = new \stdClass;
					foreach ($cacheparams->modeparams as $key => $value)
					{
						// Use int filter for id/catid to clean out spamy slugs
						if (isset($uri[$key]))
						{
							$safeuri->$key = \Request::_cleanVar($uri[$key], 0, $value);
						}
					}
				}
				$secureid = md5(serialize(array($safeuri, $cacheparams->method, $moduleparams)));
				$ret = $cache->get(
					array($cacheparams->class, $cacheparams->method),
					$cacheparams->methodparams,
					$module->id . $view_levels . $secureid,
					$wrkarounds,
					$wrkaroundoptions
				);
				break;

			case 'static':
				$ret = $cache->get(
					array($cacheparams->class, $cacheparams->method),
					$cacheparams->methodparams,
					$module->module . md5(serialize($cacheparams->methodparams)),
					$wrkarounds,
					$wrkaroundoptions
				);
				break;

			case 'oldstatic': // provided for backward compatibility, not really usefull
				$ret = $cache->get(
					array($cacheparams->class, $cacheparams->method),
					$cacheparams->methodparams,
					$module->id . $view_levels,
					$wrkarounds,
					$wrkaroundoptions
				);
				break;

			case 'itemid':
			default:
				$ret = $cache->get(
					array($cacheparams->class, $cacheparams->method),
					$cacheparams->methodparams,
					$module->id . $view_levels . \Request::getVar('Itemid', null, 'default', 'INT'),
					$wrkarounds,
					$wrkaroundoptions
				);
				break;
		}

		return $ret;
	}

	/**
	 * Get the parameters for a module
	 *
	 * @param   integer  $id  Module ID
	 * @return  object
	 */
	public function params($id)
	{
		//database object
		$db = $this->app['db'];

		//select module params based on name passed in
		if (is_numeric($id))
		{
			$query = "SELECT params FROM `#__modules` WHERE `id`=" . $db->quote(intval($id)) . " AND `published`=1";
		}
		else
		{
			$query = "SELECT params FROM `#__modules` WHERE `module`=" . $db->quote($id) . " AND `published`=1";
		}
		$db->setQuery($query);
		$params = $db->loadResult();

		//return params
		return new Registry($params);
	}
}
