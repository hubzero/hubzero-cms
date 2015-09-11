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

namespace Hubzero\Html\Builder;

use Hubzero\Utility\Arr;
use Lang;

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
			$db = \App::get('db');
			$query = $db->getQuery(true);

			$query->select('a.id, a.title, a.level');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			$query->where('extension = ' . $db->quote($extension));

			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published']))
				{
					$query->where('a.published = ' . (int) $config['filter.published']);
				}
				elseif (is_array($config['filter.published']))
				{
					Arr::toInteger($config['filter.published']);
					$query->where('a.published IN (' . implode(',', $config['filter.published']) . ')');
				}
			}

			$query->order('a.lft');

			$db->setQuery($query);
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
			$db = \App::get('db');
			$query = $db->getQuery(true);

			$query->select('a.id, a.title, a.level, a.parent_id');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			$query->where('extension = ' . $db->quote($extension));

			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published']))
				{
					$query->where('a.published = ' . (int) $config['filter.published']);
				}
				elseif (is_array($config['filter.published']))
				{
					Arr::toInteger($config['filter.published']);
					$query->where('a.published IN (' . implode(',', $config['filter.published']) . ')');
				}
			}

			$query->order('a.lft');

			$db->setQuery($query);
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
