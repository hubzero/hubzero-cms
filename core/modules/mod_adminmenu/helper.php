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

namespace Modules\AdminMenu;

use Hubzero\Module\Module;
use Hubzero\Utility\Arr;
use Request;
use Lang;
use User;
use App;

/**
 * Module class for displaying the admin menu
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!App::isAdmin())
		{
			return;
		}

		// Include the module helper classes.
		if (!class_exists('\\Modules\\AdminMenu\\Tree'))
		{
			require __DIR__ . DS . 'tree.php';
		}

		// Initialise variables.
		$lang    = App::get('language');
		$user    = User::getInstance();
		$menu    = new Tree();
		$enabled = Request::getInt('hidemainmenu') ? false : true;

		$params  = $this->params;

		// Render the module layout
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get a list of the available menus.
	 *
	 * @return  array  An array of the available menus (from the menu types table).
	 */
	public static function getMenus()
	{
		$db = App::get('db');
		$query = $db->getQuery()
			->select('a.*')
			->select('SUM(b.home)', 'home')
			->from('#__menu_types', 'a')
			->joinRaw('#__menu AS b', 'b.menutype = a.menutype AND b.home != 0', 'left')
			->select('b.language')
			->join('#__languages AS l', 'l.lang_code', 'language', 'left')
			->select('l.image')
			->select('l.sef')
			->select('l.title_native')
			->whereRaw('(b.client_id = 0 OR b.client_id IS NULL)');

		// sqlsrv change
		$query
			->group('a.id')
			->group('a.menutype')
			->group('a.description')
			->group('a.title')
			->group('b.menutype')
			->group('b.language')
			->group('l.image')
			->group('l.sef')
			->group('l.title_native');

		$db->setQuery($query->toString());

		return $db->loadObjectList();
	}

	/**
	 * Get a list of the authorised, non-special components to display in the components menu.
	 *
	 * @param   boolean  $authCheck  An optional switch to turn off the auth check (to support custom layouts 'grey out' behaviour).
	 * @return  array    A nest array of component objects and submenus
	 */
	public static function getComponents($authCheck = true)
	{
		// Initialise variables.
		$lang   = App::get('language');
		$db     = App::get('db');
		$result = array();
		$langs  = array();

		// Prepare the query.
		$query = $db->getQuery()
			->select('m.id')
			->select('m.title')
			->select('m.alias')
			->select('m.link')
			->select('m.parent_id')
			->select('m.img')
			->select('e.element')
			->select('e.protected')
			->from('#__menu', 'm');

		// Filter on the enabled states.
		$query->join('#__extensions AS e', 'm.component_id', 'e.extension_id', 'left')
			->whereEquals('m.client_id', '1')
			->whereEquals('e.enabled', '1')
			->where('m.id', '>', '1');

		// Order by lft.
		$query->order('m.lft', 'asc');

		$db->setQuery($query->toString());

		// Component list
		$components	= $db->loadObjectList();

		// Parse the list of extensions.
		foreach ($components as &$component)
		{
			// Trim the menu link.
			$component->link = trim($component->link);

			if ($component->parent_id == 1)
			{
				// Only add this top level if it is authorised and enabled.
				if ($authCheck == false || ($authCheck && User::authorise('core.manage', $component->element)))
				{
					// Root level.
					$result[$component->id] = $component;
					if (!isset($result[$component->id]->submenu))
					{
						$result[$component->id]->submenu = array();
					}

					// If the root menu link is empty, add it in.
					if (empty($component->link))
					{
						$component->link = 'index.php?option=' . $component->element;
					}

					if (!empty($component->element))
					{
						// Load the core file then
						// Load extension-local file.
						$lang->load($component->element . '.sys', PATH_APP . '/bootstrap/administrator', null, false, false)
						|| $lang->load($component->element . '.sys', ($component->protected ? PATH_CORE : PATH_APP) . '/components/' . $component->element . '/admin', null, false, false)
						|| $lang->load($component->element . '.sys', PATH_APP . '/bootstrap/administrator', $lang->getDefault(), false, false)
						|| $lang->load($component->element . '.sys', ($component->protected ? PATH_CORE : PATH_APP) . '/components/' . $component->element . '/admin', $lang->getDefault(), false, false);
					}
					$component->text = $lang->hasKey($component->title) ? Lang::txt($component->title) : $component->alias;
				}
			}
			else
			{
				// Sub-menu level.
				if (isset($result[$component->parent_id]))
				{
					// Add the submenu link if it is defined.
					if (isset($result[$component->parent_id]->submenu) && !empty($component->link))
					{
						$component->text = $lang->hasKey($component->title) ? Lang::txt($component->title) : $component->alias;
						$result[$component->parent_id]->submenu[] =& $component;
					}
				}
			}
		}

		$result = Arr::sortObjects($result, 'text', 1, true, $lang->getLocale());

		return $result;
	}
}
