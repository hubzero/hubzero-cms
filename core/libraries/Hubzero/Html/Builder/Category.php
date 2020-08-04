<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Utility\Arr;
use Lang;
use App;

/**
 * Utility class for categories
 */
class Category
{
	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 */
	protected static $items = array();

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param   string  $extension  The extension option e.g. com_something.
	 * @param   array   $config     An array of configuration options. By default, only
	 *                              published and unpublished categories are returned.
	 * @return  array
	 */
	public static function options($extension, $config = array('filter.published' => array(0, 1)))
	{
		$hash = md5($extension . '.' . serialize($config));

		if (!isset(self::$items[$hash]))
		{
			$config = (array) $config;
			$db = App::get('db');

			$query = $db->getQuery()
				->select('a.id')
				->select('a.title')
				->select('a.level')
				->from('#__categories', 'a')
				->where('a.parent_id', '>', '0');

			// Filter on extension.
			$query->whereEquals('extension', $extension);

			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published']))
				{
					$query->whereEquals('a.published', (int) $config['filter.published']);
				}
				elseif (is_array($config['filter.published']))
				{
					Arr::toInteger($config['filter.published']);
					$query->whereIn('a.published', $config['filter.published']);
				}
			}

			$query->order('a.lft', 'asc');

			$db->setQuery($query->toString());
			$items = $db->loadObjectList();

			// Assemble the list options.
			self::$items[$hash] = array();

			foreach ($items as &$item)
			{
				$repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
				$item->title = str_repeat('- ', $repeat) . $item->title;
				self::$items[$hash][] = Select::option($item->id, $item->title);
			}
		}

		return self::$items[$hash];
	}

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param   string  $extension  The extension option.
	 * @param   array   $config     An array of configuration options. By default, only published and unpublished categories are returned.
	 * @return  array   Categories for the extension
	 */
	public static function categories($extension, $config = array('filter.published' => array(0, 1)))
	{
		$hash = md5($extension . '.' . serialize($config));

		if (!isset(self::$items[$hash]))
		{
			$config = (array) $config;
			$db = App::get('db');

			$query = $db->getQuery()
				->select('a.id, a.title, a.level, a.parent_id')
				->select('a.title')
				->select('a.level')
				->select('a.parent_id')
				->from('#__categories', 'a')
				->where('a.parent_id', '>', '0');

			// Filter on extension.
			$query->whereEquals('extension', $extension);

			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published']))
				{
					$query->whereEquals('a.published', (int) $config['filter.published']);
				}
				elseif (is_array($config['filter.published']))
				{
					Arr::toInteger($config['filter.published']);
					$query->whereIn('a.published', $config['filter.published']);
				}
			}

			$query->order('a.lft', 'asc');

			$db->setQuery($query->toString());
			$items = $db->loadObjectList();

			// Assemble the list options.
			self::$items[$hash] = array();

			foreach ($items as &$item)
			{
				$repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
				$item->title = str_repeat('- ', $repeat) . $item->title;
				self::$items[$hash][] = Select::option($item->id, $item->title);
			}
			// Special "Add to root" option:
			self::$items[$hash][] = Select::option('1', Lang::txt('JLIB_HTML_ADD_TO_ROOT'));
		}

		return self::$items[$hash];
	}
}
