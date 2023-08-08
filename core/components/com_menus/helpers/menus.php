<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Helpers;

use Components\Menus\Models\Menu;
use Hubzero\Base\Obj;
use Hubzero\Access\Access;
use Submenu;
use Route;
use User;
use Lang;
use App;

/**
 * Menus component helper.
 */
class Menus
{
	/**
	 * Defines the valid request variables for the reverse lookup.
	 *
	 * @var  array
	 */
	protected static $_filter = array('option', 'view', 'layout');

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  $parentId  The menu ID.
	 * @return  object
	 */
	public static function getActions($parentId = 0)
	{
		$result = new Obj;

		if (empty($parentId))
		{
			$assetName = 'com_menus';
		}
		else
		{
			$assetName = 'com_menus.item.' . (int) $parentId;
		}

		$actions = Access::getActionsFromFile(\Component::path('com_menus') . '/config/access.xml');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Gets a standard form of a link for lookups.
	 *
	 * @param   mixed  $request  A link string or array of request variables.
	 * @return  mixed  A link in standard option-view-layout form, or false if the supplied response is invalid.
	 */
	public static function getLinkKey($request)
	{
		if (empty($request))
		{
			return false;
		}

		// Check if the link is in the form of index.php?...
		if (is_string($request))
		{
			$args = array();
			if (strpos($request, 'index.php') === 0)
			{
				parse_str(parse_url(htmlspecialchars_decode($request), PHP_URL_QUERY), $args);
			}
			else
			{
				parse_str($request, $args);
			}
			$request = $args;
		}

		// Only take the option, view and layout parts.
		foreach ($request as $name => $value)
		{
			if ((!in_array($name, self::$_filter)) && (!($name == 'task' && !array_key_exists('view', $request))))
			{
				// Remove the variables we want to ignore.
				unset($request[$name]);
			}
		}

		ksort($request);

		return 'index.php?' . http_build_query($request, '', '&');
	}

	/**
	 * Get the menu list for create a menu module
	 *
	 * @return  array  The menu array list
	 */
	public static function getMenuTypes()
	{
		$rows = Menu::all()
			->rows()
			->fieldsByKey('menutype');

		return $rows;
	}

	/**
	 * Get a list of menu links for one or all menus.
	 *
	 * @param   string  $menuType   An option menu to filter the list on, otherwise all menu links are returned as a grouped array.
	 * @param   int     $parentId   An optional parent ID to pivot results around.
	 * @param   int     $mode       An optional mode. If parent ID is set and mode=2, the parent and children are excluded from the list.
	 * @param   array   $published  An optional array of states
	 * @param   array   $languages
	 * @return  mixed
	 */
	public static function getMenuLinks($menuType = null, $parentId = 0, $mode = 0, $published=array(), $languages=array())
	{
		$db = App::get('db');
		$query = $db->getQuery();

		$query->select('a.id', 'value');
		$query->select('a.title', 'text');
		$query->select('a.level');
		$query->select('a.menutype');
		$query->select('a.type');
		$query->select('a.template_style_id');
		$query->select('a.checked_out');
		$query->from('#__menu', 'a');
		$query->joinRaw('#__menu AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left');

		// Filter by the type
		if ($menuType)
		{
			$query->whereEquals('a.menutype', $menuType, 1)
				->orWhereEquals('a.parent_id', 0, 1)
				->resetDepth();
		}

		if ($parentId)
		{
			if ($mode == 2)
			{
				// Prevent the parent and children from showing.
				$query->join('#__menu AS p', 'p.id', (int) $parentId, 'left');
				$query->where('a.lft', '<=', 'p.lft', 1)
					->orWhere('a.rgt', '>=', 'p.rgt', 1)
					->resetDepth();
			}
		}

		if (!empty($languages))
		{
			$query->whereIn('a.language', $languages);
		}

		if (!empty($published))
		{
			$query->whereIn('a.published', $published);
		}

		$query->where('a.published', '!=', '-2');
		$query->group('a.id')
			->group('a.title')
			->group('a.level')
			->group('a.menutype')
			->group('a.type')
			->group('a.template_style_id')
			->group('a.checked_out')
			->group('a.lft');
		$query->order('a.lft', 'ASC');

		// Get the options.
		$db->setQuery($query->toString());

		$links = $db->loadObjectList();

		// Check for a database error.
		if ($error = $db->getErrorMsg())
		{
			throw new \Exception($error, 500);
			return false;
		}

		// Pad the option text with spaces using depth level as a multiplier.
		foreach ($links as &$link)
		{
			$link->text = str_repeat('- ', $link->level).$link->text;
		}

		if (empty($menuType))
		{
			// If the menutype is empty, group the items by menutype.
			$query = $db->getQuery();
			$query->select('*');
			$query->from('#__menu_types');
			$query->where('menutype', '<>', '');
			$query->order('title', 'asc')
				->order('menutype', 'asc');
			$db->setQuery($query->toString());

			$menuTypes = $db->loadObjectList();

			// Check for a database error.
			if ($error = $db->getErrorMsg())
			{
				return false;
			}

			// Create a reverse lookup and aggregate the links.
			$rlu = array();
			foreach ($menuTypes as &$type)
			{
				$rlu[$type->menutype] = &$type;
				$type->links = array();
			}

			// Loop through the list of menu links.
			foreach ($links as &$link)
			{
				if (isset($rlu[$link->menutype]))
				{
					$rlu[$link->menutype]->links[] = &$link;

					// Cleanup garbage.
					unset($link->menutype);
				}
			}

			return $menuTypes;
		}
		else
		{
			return $links;
		}
	}

	/**
	 * Get associations
	 *
	 * @param   integer  $pk
	 * @return  array
	 */
	public static function getAssociations($pk)
	{
		$associations = array();

		$db = App::get('db');

		$query = $db->getQuery();
		$query->from('#__menu', 'm');
		$query->join('#__associations as a', 'a.id', 'm.id', 'inner');
		$query->whereEquals('a.context', 'com_menus.item');
		$query->join('#__associations as a2', 'a.key', 'a2.key', 'inner');
		$query->join('#__menu as m2', 'a2.id', 'm2.id', 'inner');
		$query->whereEquals('m.id', (int)$pk);
		$query->select('m2.language');
		$query->select('m2.id');

		$db->setQuery($query->toString());
		$menuitems = $db->loadObjectList('language');

		// Check for a database error.
		if ($error = $db->getErrorMsg())
		{
			throw new \Exception($error, 500);
		}

		foreach ($menuitems as $tag => $item)
		{
			$associations[$tag] = $item->id;
		}

		return $associations;
	}
}
